<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oauth extends MY_Controller {

    public function __construct() {
        parent::__construct();
        // Only Admin should be connecting the specific agency account
        if ($this->session->userdata('role') !== 'admin') {
            show_error('Unauthorized', 403);
        }
        $this->load->library('google_api');
    }

    public function connect() {
        redirect($this->google_api->get_login_url());
    }

    public function callback() {
        $code = $this->input->get('code');
        if ($code) {
            if ($this->google_api->authenticate_code($code)) {
                $this->session->set_flashdata('success', 'Google Account Connected Successfully');
                redirect('dashboard');
            } else {
                $this->session->set_flashdata('error', 'Failed to authenticate with Google');
                redirect('dashboard');
            }
        } else {
            // Handle error or cancellation
            redirect('dashboard');
        }
    }
}
?>
