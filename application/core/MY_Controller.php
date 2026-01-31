<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('auth/login');
        }
    }
}
?>
