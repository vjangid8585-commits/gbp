<?php
require_once('index.php'); // Bootstrap CI

$CI =& get_instance();
$CI->load->model('User_model');

$email = 'admin@agency.com';
$password = 'admin123';

// Check if exists
$existing = $CI->User_model->get_user_by_email($email);
if ($existing) {
    echo "Admin user already exists.\n";
} else {
    $data = [
        'name' => 'Super Admin',
        'email' => $email,
        'password' => $password,
        'role' => 'admin'
    ];
    if ($CI->User_model->create_user($data)) {
        echo "Admin user created successfully.\nEmail: $email\nPassword: $password\n";
    } else {
        echo "Failed to create admin user.\n";
    }
}
?>
