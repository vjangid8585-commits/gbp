<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync extends MY_Controller {

    public function __construct() {
        parent::__construct();
        if ($this->session->userdata('role') !== 'admin') {
            show_error('Unauthorized', 403);
        }
        $this->load->library('google_api');
    }

    public function locations() {
        // Step 1: List all accounts
        $url = 'https://mybusinessaccountmanagement.googleapis.com/v1/accounts';
        $response = $this->google_api->make_request($url);

        log_message('debug', 'Accounts API Response: ' . json_encode($response));

        if (isset($response['error'])) {
            $this->session->set_flashdata('error', 'API Error: ' . $response['error']);
            redirect('locations');
            return;
        }

        if ($response['code'] != 200) {
            $error_msg = $response['body']['error']['message'] ?? 'Unknown error (Code: ' . $response['code'] . ')';
            log_message('error', 'Google Accounts API Error: ' . json_encode($response['body']));
            $this->session->set_flashdata('error', 'Google API Error: ' . $error_msg);
            redirect('locations');
            return;
        }

        if (!isset($response['body']['accounts']) || empty($response['body']['accounts'])) {
            $this->session->set_flashdata('error', 'No Google Business Profile accounts found for this Google account.');
            redirect('locations');
            return;
        }

        $accounts = $response['body']['accounts'];
        $total_locations = 0;

        // Step 2: For each account, list locations
        foreach ($accounts as $account) {
            $account_name = $account['name']; // e.g. accounts/112233
            
            $loc_url = "https://mybusinessbusinessinformation.googleapis.com/v1/{$account_name}/locations?readMask=name,title,storeCode,latlng,phoneNumbers,websiteUri,regularHours,categories,profile,storefrontAddress,metadata";
            
            $loc_resp = $this->google_api->make_request($loc_url);
            
            log_message('debug', 'Locations API Response for ' . $account_name . ': ' . json_encode($loc_resp));

            if ($loc_resp['code'] == 200 && isset($loc_resp['body']['locations'])) {
                foreach ($loc_resp['body']['locations'] as $loc) {
                    $this->_upsert_location($loc, $account_name);
                    $total_locations++;
                }
            } else {
                log_message('error', 'Locations API Error for ' . $account_name . ': ' . json_encode($loc_resp['body']));
            }
        }

        if ($total_locations > 0) {
            $this->session->set_flashdata('success', "Successfully synced {$total_locations} location(s) from Google.");
        } else {
            $this->session->set_flashdata('error', 'No locations found in your Google Business Profile accounts.');
        }
        
        redirect('locations');
    }

    // Sync all data for a single location
    public function location($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $google_id = $location->google_location_id;
        $account_id = $location->account_id;
        $results = [];

        // 1. Sync Insights (last 90 days)
        $start = new DateTime('-90 days');
        $end = new DateTime();
        $url = "https://businessprofileperformance.googleapis.com/v1/{$google_id}:fetchMultiDailyMetricsTimeSeries";
        $url .= "?dailyRange.startDate.year=" . $start->format('Y');
        $url .= "&dailyRange.startDate.month=" . $start->format('n');
        $url .= "&dailyRange.startDate.day=" . $start->format('j');
        $url .= "&dailyRange.endDate.year=" . $end->format('Y');
        $url .= "&dailyRange.endDate.month=" . $end->format('n');
        $url .= "&dailyRange.endDate.day=" . $end->format('j');
        $url .= "&dailyMetrics=WEBSITE_CLICKS&dailyMetrics=CALL_CLICKS&dailyMetrics=BUSINESS_DIRECTION_REQUESTS";

        $response = $this->google_api->make_request($url);
        if ($response['code'] == 200 && isset($response['body']['multiDailyMetricTimeSeries'])) {
            $count = $this->_parse_insights($location_id, $response['body']['multiDailyMetricTimeSeries']);
            $results[] = "{$count} days of insights";
        }

        // 2. Sync Reviews
        $url = "https://mybusiness.googleapis.com/v4/{$account_id}/{$google_id}/reviews";
        $response = $this->google_api->make_request($url);
        if ($response['code'] == 200 && isset($response['body']['reviews'])) {
            $count = 0;
            foreach ($response['body']['reviews'] as $rev) {
                $rating = $this->_convert_star_rating($rev['starRating'] ?? 'STAR_RATING_UNSPECIFIED');
                $data = [
                    'location_id' => $location_id,
                    'google_review_id' => $rev['reviewId'],
                    'reviewer_name' => $rev['reviewer']['displayName'] ?? 'Anonymous',
                    'rating' => $rating,
                    'comment' => $rev['comment'] ?? '',
                    'reply_text' => $rev['reviewReply']['comment'] ?? null,
                    'created_at' => date('Y-m-d H:i:s', strtotime($rev['createTime']))
                ];
                $exists = $this->db->get_where('reviews', ['google_review_id' => $rev['reviewId']])->row();
                if ($exists) {
                    $this->db->where('id', $exists->id)->update('reviews', $data);
                } else {
                    $this->db->insert('reviews', $data);
                }
                $count++;
            }
            $results[] = "{$count} reviews";
        }

        // 3. Sync Posts
        $url = "https://mybusiness.googleapis.com/v4/{$account_id}/{$google_id}/localPosts";
        $response = $this->google_api->make_request($url);
        if ($response['code'] == 200 && isset($response['body']['localPosts'])) {
            $count = 0;
            foreach ($response['body']['localPosts'] as $post) {
                $data = [
                    'location_id' => $location_id,
                    'google_post_id' => $post['name'],
                    'content' => $post['summary'] ?? '',
                    'topic_type' => $post['topicType'] ?? 'STANDARD',
                    'media_url' => $post['media'][0]['sourceUrl'] ?? null,
                    'status' => $post['state'] ?? 'LIVE',
                    'created_at' => date('Y-m-d H:i:s', strtotime($post['createTime'] ?? 'now'))
                ];
                $exists = $this->db->get_where('posts', ['google_post_id' => $post['name']])->row();
                if ($exists) {
                    $this->db->where('id', $exists->id)->update('posts', $data);
                } else {
                    $this->db->insert('posts', $data);
                }
                $count++;
            }
            $results[] = "{$count} posts";
        }

        // 4. Sync Products
        $location_name = $account_id . '/' . $google_id;
        $prod_response = $this->google_api->get_products($location_name);
        if ($prod_response['code'] == 200 && isset($prod_response['body']['products'])) {
            $count = 0;
            foreach ($prod_response['body']['products'] as $google_product) {
                $existing = $this->db->get_where('products', [
                    'google_product_id' => $google_product['name']
                ])->row();

                $product_data = [
                    'location_id' => $location_id,
                    'google_product_id' => $google_product['name'],
                    'name' => $google_product['productName'] ?? '',
                    'description' => $google_product['productDescription'] ?? '',
                    'price' => $google_product['price']['units'] ?? 0,
                    'sync_status' => 'synced',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                // Only insert/update if it's new or not pending sync
                if ($existing) {
                    if ($existing->sync_status !== 'pending') { 
                        $this->db->where('id', $existing->id)->update('products', $product_data);
                    }
                } else {
                    $this->db->insert('products', $product_data);
                }
                $count++;
            }
            $results[] = "{$count} products";
        }

        // 5. Sync Services
        $serv_response = $this->google_api->get_services($location_name);
        if ($serv_response['code'] == 200 && isset($serv_response['body']['serviceItems'])) {
            $count = 0;
            foreach ($serv_response['body']['serviceItems'] as $index => $google_service) {
                $existing = $this->db->get_where('services', [
                    'location_id' => $location_id,
                    'name' => $google_service['displayName'] ?? ''
                ])->row();

                $service_data = [
                    'location_id' => $location_id,
                    'name' => $google_service['displayName'] ?? '',
                    'description' => $google_service['description'] ?? '',
                    'price' => $google_service['price']['units'] ?? 0,
                    'display_order' => $index + 1,
                    'sync_status' => 'synced',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                if ($existing) {
                    $this->db->where('id', $existing->id)->update('services', $service_data);
                } else {
                    $this->db->insert('services', $service_data);
                }
                $count++;
            }
            $results[] = "{$count} services";
        }

        if (!empty($results)) {
            $this->session->set_flashdata('success', 'Synced: ' . implode(', ', $results));
        } else {
            $this->session->set_flashdata('warning', 'Sync completed but no new data was found.');
        }

        redirect('locations/view/' . $location_id);
    }

    public function insights($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $google_id = $location->google_location_id;

        // Get date range from query params (days to go back, default 90, max 540 for 18 months)
        $days = $this->input->get('days') ?: 90;
        $days = min(max(intval($days), 7), 540); // Between 7 and 540 days
        
        $start = new DateTime("-{$days} days");
        $end = new DateTime();

        $url = "https://businessprofileperformance.googleapis.com/v1/{$google_id}:fetchMultiDailyMetricsTimeSeries";
        $url .= "?dailyRange.startDate.year=" . $start->format('Y');
        $url .= "&dailyRange.startDate.month=" . $start->format('n');
        $url .= "&dailyRange.startDate.day=" . $start->format('j');
        $url .= "&dailyRange.endDate.year=" . $end->format('Y');
        $url .= "&dailyRange.endDate.month=" . $end->format('n');
        $url .= "&dailyRange.endDate.day=" . $end->format('j');
        $url .= "&dailyMetrics=WEBSITE_CLICKS&dailyMetrics=CALL_CLICKS&dailyMetrics=BUSINESS_DIRECTION_REQUESTS";

        $response = $this->google_api->make_request($url);
        
        log_message('debug', 'Insights API Response: ' . json_encode($response));

        if (isset($response['error'])) {
            $this->session->set_flashdata('error', 'API Error: ' . $response['error']);
            redirect('insights/view/' . $location_id);
            return;
        }

        if ($response['code'] != 200) {
            $error_msg = $response['body']['error']['message'] ?? 'Unknown error';
            log_message('error', 'Google Insights API Error: ' . json_encode($response['body']));
            $this->session->set_flashdata('error', 'Insights API Error: ' . $error_msg);
            redirect('insights/view/' . $location_id);
            return;
        }

        // Parse real insights data
        if (isset($response['body']['multiDailyMetricTimeSeries'])) {
            $count = $this->_parse_insights($location_id, $response['body']['multiDailyMetricTimeSeries']);
            $this->session->set_flashdata('success', "Insights synced successfully! {$count} days of data from the last {$days} days.");
        } else {
            $this->session->set_flashdata('error', 'No insights data returned from Google.');
        }

        redirect('insights/view/' . $location_id);
    }

    // Extended insights sync for historical data
    public function insights_historical($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $google_id = $location->google_location_id;
        $total_count = 0;
        
        // Fetch data in 90-day chunks going back 18 months (540 days)
        for ($i = 0; $i < 6; $i++) {
            $end_days = $i * 90;
            $start_days = ($i + 1) * 90;
            
            $start = new DateTime("-{$start_days} days");
            $end = new DateTime("-{$end_days} days");
            
            if ($end_days == 0) {
                $end = new DateTime(); // Today for the most recent chunk
            }

            $url = "https://businessprofileperformance.googleapis.com/v1/{$google_id}:fetchMultiDailyMetricsTimeSeries";
            $url .= "?dailyRange.startDate.year=" . $start->format('Y');
            $url .= "&dailyRange.startDate.month=" . $start->format('n');
            $url .= "&dailyRange.startDate.day=" . $start->format('j');
            $url .= "&dailyRange.endDate.year=" . $end->format('Y');
            $url .= "&dailyRange.endDate.month=" . $end->format('n');
            $url .= "&dailyRange.endDate.day=" . $end->format('j');
            $url .= "&dailyMetrics=WEBSITE_CLICKS&dailyMetrics=CALL_CLICKS&dailyMetrics=BUSINESS_DIRECTION_REQUESTS";

            $response = $this->google_api->make_request($url);

            if ($response['code'] == 200 && isset($response['body']['multiDailyMetricTimeSeries'])) {
                $count = $this->_parse_insights($location_id, $response['body']['multiDailyMetricTimeSeries']);
                $total_count += $count;
            }
            
            // Small delay to avoid rate limiting
            usleep(200000); // 200ms
        }
        
        $this->session->set_flashdata('success', "Historical insights synced! {$total_count} days of data (up to 18 months).");
        redirect('insights/view/' . $location_id);
    }

    public function reviews($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $google_id = $location->google_location_id;
        $account_id = $location->account_id;
        
        // Use Business Profile API v1 for reviews
        // Format: accounts/{accountId}/locations/{locationId}/reviews
        $url = "https://mybusiness.googleapis.com/v4/{$account_id}/{$google_id}/reviews";
        
        $response = $this->google_api->make_request($url);
        
        log_message('debug', 'Reviews API Response: ' . json_encode($response));

        if (isset($response['error'])) {
            $this->session->set_flashdata('error', 'API Error: ' . $response['error']);
            redirect('reviews/index/' . $location_id);
            return;
        }

        if ($response['code'] != 200) {
            $error_msg = $response['body']['error']['message'] ?? 'Unknown error';
            log_message('error', 'Google Reviews API Error: ' . json_encode($response['body']));
            $this->session->set_flashdata('error', 'Reviews API Error: ' . $error_msg);
            redirect('reviews/index/' . $location_id);
            return;
        }

        if (!isset($response['body']['reviews']) || empty($response['body']['reviews'])) {
            $this->session->set_flashdata('success', 'No reviews found for this location.');
            redirect('reviews/index/' . $location_id);
            return;
        }

        $count = 0;
        foreach ($response['body']['reviews'] as $rev) {
            $rating = $this->_convert_star_rating($rev['starRating'] ?? 'STAR_RATING_UNSPECIFIED');
            
            $data = [
                'location_id' => $location_id,
                'google_review_id' => $rev['reviewId'],
                'reviewer_name' => $rev['reviewer']['displayName'] ?? 'Anonymous',
                'rating' => $rating,
                'comment' => $rev['comment'] ?? '',
                'reply_text' => $rev['reviewReply']['comment'] ?? null,
                'created_at' => date('Y-m-d H:i:s', strtotime($rev['createTime']))
            ];
            
            $exists = $this->db->get_where('reviews', ['google_review_id' => $rev['reviewId']])->row();
            if ($exists) {
                $this->db->where('id', $exists->id)->update('reviews', $data);
            } else {
                $this->db->insert('reviews', $data);
            }
            $count++;
        }

        $this->session->set_flashdata('success', "Successfully synced {$count} review(s) from Google.");
        redirect('reviews/index/' . $location_id);
    }

    public function posts($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $google_id = $location->google_location_id;
        $account_id = $location->account_id;
        
        // Use Business Profile API v4 for posts
        $url = "https://mybusiness.googleapis.com/v4/{$account_id}/{$google_id}/localPosts";
        
        $response = $this->google_api->make_request($url);
        
        log_message('debug', 'Posts API Response: ' . json_encode($response));

        if (isset($response['error'])) {
            $this->session->set_flashdata('error', 'API Error: ' . $response['error']);
            redirect('posts/index/' . $location_id);
            return;
        }

        if ($response['code'] != 200) {
            $error_msg = $response['body']['error']['message'] ?? 'Unknown error';
            log_message('error', 'Google Posts API Error: ' . json_encode($response['body']));
            $this->session->set_flashdata('error', 'Posts API Error: ' . $error_msg);
            redirect('posts/index/' . $location_id);
            return;
        }

        if (!isset($response['body']['localPosts']) || empty($response['body']['localPosts'])) {
            $this->session->set_flashdata('success', 'No posts found for this location.');
            redirect('posts/index/' . $location_id);
            return;
        }

        $count = 0;
        foreach ($response['body']['localPosts'] as $post) {
            $data = [
                'location_id' => $location_id,
                'google_post_id' => $post['name'],
                'content' => $post['summary'] ?? '',
                'topic_type' => $post['topicType'] ?? 'STANDARD',
                'media_url' => $post['media'][0]['sourceUrl'] ?? null,
                'status' => $post['state'] ?? 'LIVE',
                'created_at' => date('Y-m-d H:i:s', strtotime($post['createTime'] ?? 'now'))
            ];
            
            $exists = $this->db->get_where('posts', ['google_post_id' => $post['name']])->row();
            if ($exists) {
                $this->db->where('id', $exists->id)->update('posts', $data);
            } else {
                $this->db->insert('posts', $data);
            }
            $count++;
        }

        $this->session->set_flashdata('success', "Successfully synced {$count} post(s) from Google.");
        redirect('posts/index/' . $location_id);
    }

    // ===================== PRIVATE HELPERS =====================

    private function _upsert_location($loc, $account_id) {
        $google_id = $loc['name'];
        
        $data = [
            'google_location_id' => $google_id,
            'account_id' => $account_id,
            'business_name' => $loc['title'] ?? 'Unnamed Location',
            'address_json' => json_encode($loc['storefrontAddress'] ?? []),
            'data_json' => json_encode($loc),
            'sync_status' => 'synced',
        ];

        $exists = $this->db->get_where('locations', ['google_location_id' => $google_id])->row();

        if ($exists) {
            $this->db->where('id', $exists->id)->update('locations', $data);
        } else {
            $this->db->insert('locations', $data);
        }
    }

    private function _parse_insights($location_id, $series_data) {
        // Google returns: multiDailyMetricTimeSeries[].dailyMetricTimeSeries[].dailyMetric + timeSeries.datedValues[]
        $daily_data = [];
        
        log_message('debug', 'Parsing insights data: ' . json_encode($series_data));
        
        foreach ($series_data as $multi_series) {
            // Each multi_series contains dailyMetricTimeSeries array
            $metric_series_array = $multi_series['dailyMetricTimeSeries'] ?? [];
            
            foreach ($metric_series_array as $metric_series) {
                $metric_type = $metric_series['dailyMetric'] ?? '';
                $values = $metric_series['timeSeries']['datedValues'] ?? [];
                
                foreach ($values as $dv) {
                    $date = sprintf('%04d-%02d-%02d', $dv['date']['year'], $dv['date']['month'], $dv['date']['day']);
                    
                    if (!isset($daily_data[$date])) {
                        $daily_data[$date] = [
                            'calls' => 0,
                            'website_clicks' => 0,
                            'direction_requests' => 0,
                            'total_interactions' => 0
                        ];
                    }
                    
                    $value = intval($dv['value'] ?? 0);
                    
                    switch ($metric_type) {
                        case 'CALL_CLICKS':
                            $daily_data[$date]['calls'] = $value;
                            break;
                        case 'WEBSITE_CLICKS':
                            $daily_data[$date]['website_clicks'] = $value;
                            break;
                        case 'BUSINESS_DIRECTION_REQUESTS':
                            $daily_data[$date]['direction_requests'] = $value;
                            break;
                    }
                    
                    $daily_data[$date]['total_interactions'] = 
                        $daily_data[$date]['calls'] + 
                        $daily_data[$date]['website_clicks'] + 
                        $daily_data[$date]['direction_requests'];
                }
            }
        }
        
        log_message('debug', 'Parsed ' . count($daily_data) . ' days of insights data');
        
        // Insert/update insights
        foreach ($daily_data as $date => $metrics) {
            $exists = $this->db->get_where('insights', ['location_id' => $location_id, 'date' => $date])->row();
            
            $data = array_merge(['location_id' => $location_id, 'date' => $date], $metrics);
            
            if ($exists) {
                $this->db->where('id', $exists->id)->update('insights', $data);
            } else {
                $this->db->insert('insights', $data);
            }
        }
        
        return count($daily_data);
    }

    private function _convert_star_rating($rating_str) {
        return match($rating_str) {
            'ONE' => 1,
            'TWO' => 2,
            'THREE' => 3,
            'FOUR' => 4,
            'FIVE' => 5,
            default => 0
        };
    }
}
?>
