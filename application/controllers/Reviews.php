<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reviews extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('google_api');
    }

    public function index($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        if ($this->session->userdata('role') === 'staff' && $location->internal_assignee_id != $this->session->userdata('user_id')) {
            show_error('Unauthorized', 403);
        }

        $data['location'] = $location;
        $data['reviews'] = $this->db->where('location_id', $location_id)
                                    ->order_by('created_at', 'DESC')
                                    ->get('reviews')->result();

        // Stats
        $data['avg_rating'] = $this->db->select_avg('rating')
                                       ->where('location_id', $location_id)
                                       ->get('reviews')->row()->rating ?? 0;
        
        $data['pending_replies'] = $this->db->where('location_id', $location_id)
                                            ->where('reply_text IS NULL')
                                            ->count_all_results('reviews');

        $this->load->view('reviews/list', $data);
    }

    public function reply($review_id) {
        $review = $this->db->get_where('reviews', ['id' => $review_id])->row();
        if (!$review) show_404();

        $reply_text = $this->input->post('reply');
        
        if (empty($reply_text)) {
            $this->session->set_flashdata('error', 'Reply cannot be empty!');
            redirect('reviews/index/' . $review->location_id);
            return;
        }

        $location = $this->db->get_where('locations', ['id' => $review->location_id])->row();
        
        // Build API URL
        // Format: accounts/{accountId}/locations/{locationId}/reviews/{reviewId}/reply
        $account_id = $location->account_id;
        $google_id = $location->google_location_id;
        $review_name = $review->google_review_id;
        
        $url = "https://mybusiness.googleapis.com/v4/{$account_id}/{$google_id}/reviews/{$review_name}/reply";
        
        $response = $this->google_api->make_request($url, 'PUT', ['comment' => $reply_text]);

        // Update local database
        $this->db->where('id', $review_id)->update('reviews', [
            'reply_text' => $reply_text,
            'replied_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($response['code'] == 200) {
            $this->session->set_flashdata('success', 'Reply posted to Google successfully!');
        } else {
            $this->session->set_flashdata('success', 'Reply saved locally. API Status: ' . $response['code']);
            log_message('error', 'Review Reply API Error: ' . json_encode($response));
        }

        redirect('reviews/index/' . $review->location_id);
    }

    public function delete_reply($review_id) {
        $review = $this->db->get_where('reviews', ['id' => $review_id])->row();
        if (!$review) show_404();

        $location = $this->db->get_where('locations', ['id' => $review->location_id])->row();
        
        // Delete reply via API
        $url = "https://mybusiness.googleapis.com/v4/{$location->account_id}/{$location->google_location_id}/reviews/{$review->google_review_id}/reply";
        
        $response = $this->google_api->make_request($url, 'DELETE');

        // Update local database
        $this->db->where('id', $review_id)->update('reviews', [
            'reply_text' => null,
            'replied_at' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->session->set_flashdata('success', 'Reply deleted!');
        redirect('reviews/index/' . $review->location_id);
    }
}
?>
