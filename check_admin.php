<?php
require_once('index.php');

$CI =& get_instance();
$CI->load->model('User_model');

$user = $CI->User_model->get_user_by_email('admin@agency.com');

if ($user) {
    echo "User found!\n";
    echo "Email: " . $user->email . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Password hash exists: " . (isset($user->password_hash) ? 'YES' : 'NO') . "\n";
    echo "Password hash: " . $user->password_hash . "\n";
    echo "Verify 'admin123': " . (password_verify('admin123', $user->password_hash) ? 'YES' : 'NO') . "\n";
} else {
    echo "User NOT found in database.\n";
}
?>
