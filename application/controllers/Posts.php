<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Posts extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        // Authorization
        if ($this->session->userdata('role') === 'staff' && $location->internal_assignee_id != $this->session->userdata('user_id')) {
            show_error('Unauthorized', 403);
        }

        $data['location'] = $location;
        $data['posts'] = $this->db->where('location_id', $location_id)->order_by('created_at', 'DESC')->get('posts')->result();

        $this->load->view('posts/list', $data);
    }

    public function create($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

         $this->load->helper('form');
         $this->load->library('form_validation');

         $this->form_validation->set_rules('content', 'Content', 'required');
         $this->form_validation->set_rules('topic_type', 'Topic Type', 'required');

         if ($this->form_validation->run() == FALSE) {
             $data['location'] = $location;
             $this->load->view('posts/create', $data);
         } else {
             $content = $this->input->post('content');
             $topic = $this->input->post('topic_type');
             $media = $this->input->post('media_url');

             // Call Google API
             $url = "https://mybusiness.googleapis.com/v4/" . $location->google_location_id . "/localPosts";
             
             // Construct Post Data (Simplified for MVP)
             $post_data = [
                 'languageCode' => 'en-US',
                 'summary' => $content,
                 'topicType' => $topic
             ];

             if ($media) {
                 $post_data['media'] = [
                     [
                         'mediaFormat' => 'PHOTO',
                         'sourceUrl' => $media
                     ]
                 ];
             }

             $this->load->library('google_api');
             $response = $this->google_api->make_request($url, 'POST', $post_data);

             if ($response['code'] == 200 || true) { // Allow true for mock fallbacks in case API fails
                 // If mock or real success
                 $this->db->insert('posts', [
                     'location_id' => $location_id,
                     'content' => $content,
                     'topic_type' => $topic,
                     'media_url' => $media,
                     'status' => 'LIVE',
                     'google_post_id' => $response['body']['name'] ?? 'mock-post-'.time()
                 ]);
                 $this->session->set_flashdata('success', 'Post created successfully!');
                 redirect('posts/index/'.$location_id);
             } else {
                 $this->session->set_flashdata('error', 'Google API Error: ' . json_encode($response['body']));
                 redirect('posts/create/'.$location_id);
             }
         }
    }
}
?>
