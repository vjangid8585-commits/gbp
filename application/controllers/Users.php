<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // Only Admin/Manager can access users? Requirements say Admin only for managing users usually, 
        // but checklist says "Map users -> assigned GBP locations" which implies assignment logic later.
        // For now, let's restrict to 'admin' role for creating users.
        if ($this->session->userdata('role') !== 'admin') {
            show_error('Unauthorized', 403);
        }
        $this->load->model('User_model');
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['users'] = $this->db->where('deleted_at', NULL)->get('users')->result();
        $this->load->view('users/list', $data);
    }

    public function create() {
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,manager,staff]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('users/create');
        } else {
            $data = array(
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password'),
                'role' => $this->input->post('role')
            );
            $this->User_model->create_user($data);
            $this->session->set_flashdata('success', 'User created successfully');
            redirect('users');
        }
    }
}
?>
