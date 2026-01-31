<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index() {
        $this->db->select('locations.*, users.name as assignee_name');
        $this->db->join('users', 'users.id = locations.internal_assignee_id', 'left');
        $this->db->where('locations.deleted_at', NULL);
        
        // If staff, only see assigned
        if ($this->session->userdata('role') === 'staff') {
            $this->db->where('internal_assignee_id', $this->session->userdata('user_id'));
        }

    public function edit($id) {
        $location = $this->db->get_where('locations', ['id' => $id])->row();
        
        if (!$location) show_404();
        
        // Authorization check
        if ($this->session->userdata('role') === 'staff' && $location->internal_assignee_id != $this->session->userdata('user_id')) {
            show_error('Unauthorized access to this location', 403);
        }

        // Decode JSON data
        $location->data = json_decode($location->data_json, true);
        
        $data['location'] = $location;
        $this->load->view('locations/edit', $data);
    }

    public function update($id) {
        $location = $this->db->get_where('locations', ['id' => $id])->row();
        if (!$location) show_404();

        // Simple validation checks for MVP
        $update_mask = [];
        $google_data = [];

        // 1. Website
        $website = $this->input->post('websiteUri');
        if ($website) {
            $google_data['websiteUri'] = $website;
            $update_mask[] = 'websiteUri';
        }

        // 3. Profile Description
        $description = $this->input->post('description');
        if ($description) {
            $google_data['profile'] = ['description' => $description];
            $update_mask[] = 'profile.description';
        }

        if (!empty($google_data)) {
            // Call Google API
            $url = "https://mybusinessbusinessinformation.googleapis.com/v1/" . $location->google_location_id . "?updateMask=" . implode(',', $update_mask);
            
            $this->load->library('google_api');
            // PATCH request
            $response = $this->google_api->make_request($url, 'PATCH', $google_data);

            if ($response['code'] == 200) {
                // Update Local DB
                $new_data = json_decode($location->data_json, true);
                $new_data = array_merge($new_data, $google_data); // Simple merge
                $this->db->where('id', $id)->update('locations', ['data_json' => json_encode($new_data)]);
                
                $this->session->set_flashdata('success', 'Profile updated successfully on Google!');
            } else {
                $this->session->set_flashdata('error', 'Google API Error: ' . json_encode($response['body']));
            }
        }
        
        redirect('locations/edit/'.$id);
    }
?>
