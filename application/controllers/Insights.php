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

        // Get date range from query params or default to last 30 days
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');
        $start_date = $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days'));
        
        // Validate dates
        if (strtotime($start_date) > strtotime($end_date)) {
            $temp = $start_date;
            $start_date = $end_date;
            $end_date = $temp;
        }

        // Fetch insights for date range
        $this->db->where('location_id', $location_id);
        $this->db->where('date >=', $start_date);
        $this->db->where('date <=', $end_date);
        $this->db->order_by('date', 'ASC');
        $insights = $this->db->get('insights')->result();

        // Get available date range for this location
        $date_range = $this->db->select('MIN(date) as min_date, MAX(date) as max_date')
                               ->where('location_id', $location_id)
                               ->get('insights')->row();

        $data['location'] = $location;
        $data['insights'] = $insights;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['min_available_date'] = $date_range->min_date ?? date('Y-m-d', strtotime('-90 days'));
        $data['max_available_date'] = $date_range->max_date ?? date('Y-m-d');

        // Calculate period comparison
        $days_in_range = (strtotime($end_date) - strtotime($start_date)) / 86400;
        $prev_end_date = date('Y-m-d', strtotime($start_date . ' -1 day'));
        $prev_start_date = date('Y-m-d', strtotime($prev_end_date . ' -' . $days_in_range . ' days'));
        
        // Get previous period data for comparison
        $this->db->select('SUM(calls) as calls, SUM(website_clicks) as website_clicks, SUM(direction_requests) as direction_requests');
        $this->db->where('location_id', $location_id);
        $this->db->where('date >=', $prev_start_date);
        $this->db->where('date <=', $prev_end_date);
        $prev_data = $this->db->get('insights')->row();
        
        $data['prev_period'] = $prev_data;

        $this->load->view('insights/view', $data);
    }
}
?>

