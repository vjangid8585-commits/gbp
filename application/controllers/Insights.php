<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Insights extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function view($location_id) {
        $location = $this->db->get_where('locations', ['id' => $location_id])->row();
        if (!$location) show_404();

        // Authorization
        if ($this->session->userdata('role') === 'staff' && $location->internal_assignee_id != $this->session->userdata('user_id')) {
            show_error('Unauthorized', 403);
        }

        // Fetch local insights (Last 30 days)
        $this->db->where('location_id', $location_id);
        $this->db->order_by('date', 'ASC');
        $this->db->limit(30);
        $insights = $this->db->get('insights')->result();

        $data['location'] = $location;
        $data['insights'] = $insights;

        $this->load->view('insights/view', $data);
    }
}
?>
