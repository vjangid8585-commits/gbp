<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    public function login() {
        // If already logged in, redirect
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('auth/login');
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $user = $this->User_model->get_user_by_email($email);

            if ($user && $this->User_model->verify_password($password, $user->password_hash)) {
                $session_data = array(
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                );
                $this->session->set_userdata($session_data);
                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error', 'Invalid email or password');
                redirect('auth/login');
            }
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
?>
