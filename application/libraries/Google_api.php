<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Google_api {
    private $CI;
    private $auth_endpoint = 'https://accounts.google.com/o/oauth2/v2/auth';
    private $token_endpoint = 'https://oauth2.googleapis.com/token';

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->config->load('google');
        // Load database to access settings
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
            'prompt' => 'consent' // Force refresh token
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

    public function make_request($url, $method = 'GET', $data = []) {
        $access_token = $this->_get_valid_access_token();
        if (!$access_token) {
            return ['error' => 'Unable to get access token'];
        }

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
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
        curl_close($ch);

        return [
            'code' => $http_code,
            'body' => json_decode($result, true)
        ];
    }

    private function _get_valid_access_token() {
        $expiry = $this->_get_setting('google_token_expiry');
        if ($expiry && time() < ($expiry - 60)) {
            return $this->_get_setting('google_access_token');
        }
        return $this->_refresh_token();
    }

    private function _refresh_token() {
        $refresh_token = $this->_get_setting('google_refresh_token');
        if (!$refresh_token) return false;

        $params = [
            'client_id' => $this->CI->config->item('google_client_id'),
            'client_secret' => $this->CI->config->item('google_client_secret'),
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token'
        ];

        $response = $this->_send_auth_request($params);

        if (isset($response['access_token'])) {
            $this->_save_setting('google_access_token', $response['access_token']);
            $this->_save_setting('google_token_expiry', time() + $response['expires_in']);
            return $response['access_token'];
        }
        return false;
    }

    private function _send_auth_request($params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->token_endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
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
}
?>
