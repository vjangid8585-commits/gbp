<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_user_by_email($email) {
        $query = $this->db->get_where('users', array('email' => $email, 'deleted_at' => NULL));
        return $query->row();
    }

    public function create_user($data) {
        // Hash password before saving
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']); // Remove raw password
        }
        return $this->db->insert('users', $data);
    }
    
    public function verify_password($input_password, $stored_hash) {
        return password_verify($input_password, $stored_hash);
    }
}
?>
