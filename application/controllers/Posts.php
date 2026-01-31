<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Posts extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('google_api');
        $this->load->helper('form');
    }

    public function index($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        if ($this->session->userdata('role') === 'staff' && $location->internal_assignee_id != $this->session->userdata('user_id')) {
            show_error('Unauthorized', 403);
        }

        $data['location'] = $location;
        
        // Get posts - both published and scheduled
        $data['posts'] = $this->db->where('location_id', $location_id)
                                   ->order_by('created_at', 'DESC')
                                   ->get('posts')->result();

        // Get scheduled posts count
        $data['scheduled_count'] = $this->db->where('location_id', $location_id)
                                            ->where('status', 'SCHEDULED')
                                            ->count_all_results('posts');

        $this->load->view('posts/list', $data);
    }

    public function create($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $data['location'] = $location;
        $this->load->view('posts/create', $data);
    }

    public function store($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        $content = $this->input->post('content');
        $topic_type = $this->input->post('topic_type');
        $action_url = $this->input->post('action_url');
        $is_scheduled = $this->input->post('schedule');
        $scheduled_at = $this->input->post('scheduled_at');

        if (empty($content)) {
            $this->session->set_flashdata('error', 'Content is required!');
            redirect('posts/create/'.$location_id);
            return;
        }

        // Handle image upload
        $media_url = null;
        if (!empty($_FILES['image']['name'])) {
            $upload_path = './uploads/posts/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0755, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 5120; // 5MB
            $config['file_name'] = time() . '_' . $_FILES['image']['name'];

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('image')) {
                $upload_data = $this->upload->data();
                $media_url = base_url('uploads/posts/' . $upload_data['file_name']);
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('posts/create/'.$location_id);
                return;
            }
        }

        // Determine status
        $status = 'LIVE';
        if ($is_scheduled && $scheduled_at) {
            $status = 'SCHEDULED';
        }

        // Build post data for Google API
        $post_data = [
            'languageCode' => 'en-US',
            'summary' => $content,
            'topicType' => $topic_type
        ];

        // Add media if uploaded
        if ($media_url) {
            $post_data['media'] = [
                ['mediaFormat' => 'PHOTO', 'sourceUrl' => $media_url]
            ];
        }

        // Add call to action if provided
        if ($action_url) {
            $post_data['callToAction'] = [
                'actionType' => 'LEARN_MORE',
                'url' => $action_url
            ];
        }

        // Add event details if EVENT type
        if ($topic_type === 'EVENT') {
            $event_start = $this->input->post('event_start');
            $event_end = $this->input->post('event_end');
            $event_title = $this->input->post('event_title');
            
            if ($event_start && $event_end) {
                $post_data['event'] = [
                    'title' => $event_title ?: $content,
                    'schedule' => [
                        'startDate' => $this->_format_date($event_start),
                        'startTime' => $this->_format_time($event_start),
                        'endDate' => $this->_format_date($event_end),
                        'endTime' => $this->_format_time($event_end)
                    ]
                ];
            }
        }

        // Add offer details if OFFER type
        if ($topic_type === 'OFFER') {
            $coupon = $this->input->post('coupon_code');
            $terms = $this->input->post('offer_terms');
            
            $post_data['offer'] = [];
            if ($coupon) $post_data['offer']['couponCode'] = $coupon;
            if ($terms) $post_data['offer']['termsConditions'] = $terms;
        }

        // If scheduled, save locally only
        if ($status === 'SCHEDULED') {
            $this->db->insert('posts', [
                'location_id' => $location_id,
                'content' => $content,
                'topic_type' => $topic_type,
                'media_url' => $media_url,
                'status' => 'SCHEDULED',
                'scheduled_at' => $scheduled_at,
                'post_data_json' => json_encode($post_data),
                'google_post_id' => null
            ]);
            $this->session->set_flashdata('success', 'Post scheduled for ' . date('M d, Y h:i A', strtotime($scheduled_at)));
        } else {
            // Publish immediately via API
            // Build the location name in format: accounts/{account_id}/locations/{location_id}
            $location_name = $location->account_id . '/' . $location->google_location_id;
            
            $response = $this->google_api->create_local_post($location_name, $post_data);

            $google_post_id = $response['body']['name'] ?? 'local-' . time();
            
            $this->db->insert('posts', [
                'location_id' => $location_id,
                'content' => $content,
                'topic_type' => $topic_type,
                'media_url' => $media_url,
                'status' => ($response['code'] == 200) ? 'LIVE' : 'LOCAL',
                'google_post_id' => $google_post_id
            ]);

            if ($response['code'] == 200) {
                $this->session->set_flashdata('success', 'Post published to Google!');
            } else {
                $error_msg = $response['body']['error']['message'] ?? ($response['error'] ?? 'Unknown error');
                $this->session->set_flashdata('warning', 'Post saved locally. Google API: ' . $error_msg);
            }
        }

        redirect('posts/index/'.$location_id);
    }

    // Helper to format date for Google API
    private function _format_date($datetime) {
        $dt = new DateTime($datetime);
        return [
            'year' => (int)$dt->format('Y'),
            'month' => (int)$dt->format('n'),
            'day' => (int)$dt->format('j')
        ];
    }

    private function _format_time($datetime) {
        $dt = new DateTime($datetime);
        return [
            'hours' => (int)$dt->format('G'),
            'minutes' => (int)$dt->format('i')
        ];
    }

    // Publish scheduled posts (call via cron)
    public function publish_scheduled() {
        $now = date('Y-m-d H:i:s');
        $posts = $this->db->where('status', 'SCHEDULED')
                          ->where('scheduled_at <=', $now)
                          ->get('posts')->result();

        foreach ($posts as $post) {
            $location = $this->db->get_where('locations', ['id' => $post->location_id])->row();
            if (!$location) continue;

            $post_data = json_decode($post->post_data_json, true);
            $url = "https://mybusiness.googleapis.com/v4/{$location->account_id}/{$location->google_location_id}/localPosts";
            
            $response = $this->google_api->make_request($url, 'POST', $post_data);

            $this->db->where('id', $post->id)->update('posts', [
                'status' => ($response['code'] == 200) ? 'LIVE' : 'FAILED',
                'google_post_id' => $response['body']['name'] ?? null
            ]);
        }

        echo "Processed " . count($posts) . " scheduled posts.";
    }
}
?>
