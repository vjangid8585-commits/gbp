<?php
require_once('index.php');

$CI =& get_instance();

// Reset password for admin user
$new_hash = password_hash('admin123', PASSWORD_BCRYPT);

$CI->db->where('email', 'admin@agency.com');
$result = $CI->db->update('users', array('password_hash' => $new_hash));

if ($result) {
    echo "Password reset successfully!\n";
    echo "Email: admin@agency.com\n";
    echo "Password: admin123\n";
} else {
    echo "Failed to reset password.\n";
}
?>
