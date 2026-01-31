<?php
// Debug script to check token and API connectivity

// Load CodeIgniter
require_once 'index.php';

$CI =& get_instance();
$CI->load->database();
$CI->load->library('google_api');

echo "=== GBP Debug Tool ===\n\n";

// Check settings
echo "1. Checking stored tokens...\n";
$settings = $CI->db->get('settings')->result();
foreach ($settings as $s) {
    $value = strlen($s->setting_value) > 50 ? substr($s->setting_value, 0, 50) . '...' : $s->setting_value;
    echo "   - {$s->setting_key}: {$value}\n";
}

if (empty($settings)) {
    echo "   [ERROR] No tokens found! Please connect Google account first.\n";
    exit;
}

// Test API call
echo "\n2. Testing Google Business Profile API...\n";

$url = 'https://mybusinessaccountmanagement.googleapis.com/v1/accounts';
$response = $CI->google_api->make_request($url);

echo "   Response Code: {$response['code']}\n";
echo "   Response Body:\n";
print_r($response['body']);

if ($response['code'] == 200 && isset($response['body']['accounts'])) {
    echo "\n3. Found " . count($response['body']['accounts']) . " account(s). Checking locations...\n";
    
    foreach ($response['body']['accounts'] as $account) {
        echo "\n   Account: {$account['accountName']} ({$account['name']})\n";
        
        $loc_url = "https://mybusinessbusinessinformation.googleapis.com/v1/{$account['name']}/locations?readMask=name,title";
        $loc_response = $CI->google_api->make_request($loc_url);
        
        echo "   Locations Response Code: {$loc_response['code']}\n";
        print_r($loc_response['body']);
    }
}

echo "\n=== Debug Complete ===\n";
?>
