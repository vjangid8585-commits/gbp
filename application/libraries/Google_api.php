<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Google_api {
    private $CI;
    private $auth_endpoint = 'https://accounts.google.com/o/oauth2/v2/auth';
    private $token_endpoint = 'https://oauth2.googleapis.com/token';
    
    // New Google Business Profile API base URLs
    private $account_management_api = 'https://mybusinessaccountmanagement.googleapis.com/v1';
    private $business_info_api = 'https://mybusinessbusinessinformation.googleapis.com/v1';

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('google');
        if (!isset($this->CI->db)) {
            $this->CI->load->database();
        }
    }

    public function get_login_url() {
        $params = [
            'client_id' => $this->CI->config->item('google_client_id'),
            'redirect_uri' => $this->CI->config->item('google_redirect_uri'),
            'response_type' => 'code',
            'scope' => implode(' ', $this->CI->config->item('google_scopes')),
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];
        return $this->auth_endpoint . '?' . http_build_query($params);
    }

    public function authenticate_code($code) {
        $params = [
            'code' => $code,
            'client_id' => $this->CI->config->item('google_client_id'),
            'client_secret' => $this->CI->config->item('google_client_secret'),
            'redirect_uri' => $this->CI->config->item('google_redirect_uri'),
            'grant_type' => 'authorization_code'
        ];

        $response = $this->_send_auth_request($params);
        
        log_message('debug', 'OAuth Token Response: ' . json_encode($response));
        
        if (isset($response['error'])) {
            log_message('error', 'OAuth Error: ' . $response['error_description'] ?? $response['error']);
            return false;
        }
        
        if (isset($response['refresh_token'])) {
            $this->_save_setting('google_refresh_token', $response['refresh_token']);
        }
        if (isset($response['access_token'])) {
            $this->_save_setting('google_access_token', $response['access_token']);
            $this->_save_setting('google_token_expiry', time() + $response['expires_in']);
            return true;
        }
        return false;
    }

    // Get accounts for the authenticated user
    public function get_accounts() {
        $url = $this->account_management_api . '/accounts';
        return $this->make_request($url, 'GET');
    }

    // Get locations for an account
    public function get_locations($account_name) {
        $url = $this->business_info_api . '/' . $account_name . '/locations?readMask=name,title,storefrontAddress,websiteUri,regularHours,phoneNumbers,categories,profile,metadata';
        return $this->make_request($url, 'GET');
    }

    // Get location details
    public function get_location($location_name) {
        $url = $this->business_info_api . '/' . $location_name . '?readMask=name,title,storefrontAddress,websiteUri,regularHours,phoneNumbers,categories,profile,metadata';
        return $this->make_request($url, 'GET');
    }

    // Create a local post - Note: Uses a different API endpoint
    public function create_local_post($location_name, $post_data) {
        // The localPosts API is now part of mybusiness.googleapis.com v4 (still active for posts)
        $url = "https://mybusiness.googleapis.com/v4/{$location_name}/localPosts";
        return $this->make_request($url, 'POST', $post_data);
    }

    // Get local posts for a location
    public function get_local_posts($location_name) {
        $url = "https://mybusiness.googleapis.com/v4/{$location_name}/localPosts";
        return $this->make_request($url, 'GET');
    }

    // Delete a local post
    public function delete_local_post($post_name) {
        $url = "https://mybusiness.googleapis.com/v4/{$post_name}";
        return $this->make_request($url, 'DELETE');
    }

    // Get reviews for a location
    public function get_reviews($account_name, $location_name) {
        $url = "https://mybusiness.googleapis.com/v4/{$account_name}/{$location_name}/reviews";
        return $this->make_request($url, 'GET');
    }

    // Reply to a review
    public function reply_to_review($review_name, $reply_text) {
        $url = "https://mybusiness.googleapis.com/v4/{$review_name}/reply";
        return $this->make_request($url, 'PUT', ['comment' => $reply_text]);
    }

    // Get products for a location (uses the Merchant API)
    public function get_products($location_name) {
        $url = $this->business_info_api . '/' . $location_name . '/products';
        return $this->make_request($url, 'GET');
    }

    // Create/update a product
    public function update_product($location_name, $product_data) {
        $url = $this->business_info_api . '/' . $location_name . '/products';
        return $this->make_request($url, 'POST', $product_data);
    }

    // Get services for a location
    public function get_services($location_name) {
        // Services are part of the location's categories/attributes
        $url = $this->business_info_api . '/' . $location_name . '?readMask=serviceItems';
        return $this->make_request($url, 'GET');
    }

    // Update location services
    public function update_services($location_name, $services_data) {
        $url = $this->business_info_api . '/' . $location_name . '?updateMask=serviceItems';
        return $this->make_request($url, 'PATCH', $services_data);
    }

    // Update location information
    public function update_location($location_name, $data, $update_mask) {
        $url = $this->business_info_api . '/' . $location_name . '?updateMask=' . $update_mask;
        return $this->make_request($url, 'PATCH', $data);
    }

    public function make_request($url, $method = 'GET', $data = []) {
        $access_token = $this->_get_valid_access_token();
        
        if (!$access_token) {
            log_message('error', 'No valid access token available. User needs to re-authenticate.');
            return [
                'code' => 401,
                'error' => 'No valid access token. Please reconnect your Google account.',
                'body' => null
            ];
        }

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            log_message('error', 'cURL Error: ' . $curl_error);
            return [
                'code' => 0,
                'error' => 'Network error: ' . $curl_error,
                'body' => null
            ];
        }

        $body = json_decode($result, true);
        
        // Log API errors
        if ($http_code >= 400) {
            log_message('error', "Google API Error [{$http_code}] for {$url}: " . $result);
        }

        return [
            'code' => $http_code,
            'body' => $body
        ];
    }

    private function _get_valid_access_token() {
        $expiry = $this->_get_setting('google_token_expiry');
        $access_token = $this->_get_setting('google_access_token');
        
        // Check if token exists and is still valid (with 60s buffer)
        if ($access_token && $expiry && time() < ($expiry - 60)) {
            return $access_token;
        }
        
        // Try to refresh
        return $this->_refresh_token();
    }

    private function _refresh_token() {
        $refresh_token = $this->_get_setting('google_refresh_token');
        
        if (!$refresh_token) {
            log_message('error', 'No refresh token available. User needs to re-authenticate.');
            return false;
        }

        $params = [
            'client_id' => $this->CI->config->item('google_client_id'),
            'client_secret' => $this->CI->config->item('google_client_secret'),
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token'
        ];

        $response = $this->_send_auth_request($params);
        
        log_message('debug', 'Token Refresh Response: ' . json_encode($response));

        if (isset($response['access_token'])) {
            $this->_save_setting('google_access_token', $response['access_token']);
            $this->_save_setting('google_token_expiry', time() + $response['expires_in']);
            return $response['access_token'];
        }
        
        if (isset($response['error'])) {
            log_message('error', 'Token Refresh Error: ' . ($response['error_description'] ?? $response['error']));
        }
        
        return false;
    }

    private function _send_auth_request($params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->token_endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $result = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        if ($curl_error) {
            log_message('error', 'OAuth cURL Error: ' . $curl_error);
            return ['error' => $curl_error];
        }
        
        return json_decode($result, true) ?? ['error' => 'Invalid response'];
    }

    private function _save_setting($key, $value) {
        $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?";
        $this->CI->db->query($sql, [$key, $value, $value]);
    }

    private function _get_setting($key) {
        $query = $this->CI->db->get_where('settings', ['setting_key' => $key]);
        $row = $query->row();
        return $row ? $row->setting_value : null;
    }

    // Check if we have a valid connection
    public function is_connected() {
        $token = $this->_get_setting('google_access_token');
        $refresh = $this->_get_setting('google_refresh_token');
        return !empty($token) || !empty($refresh);
    }
}
?>
