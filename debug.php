<?php
// Complete API Debug Tool - Tests ALL endpoints

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

$pdo = new PDO("mysql:host=localhost;dbname=gbp_db", 'root', '');

echo "<pre style='background:#1e1e2e;color:#cdd6f4;padding:20px;font-family:monospace;'>";
echo "<h2 style='color:#89b4fa;'>🔍 Complete GBP API Debug</h2>\n\n";

// Get token
$stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'google_access_token'");
$token = $stmt->fetchColumn();

if (!$token) {
    echo "<span style='color:#f38ba8;'>✗</span> No token! Run /oauth/connect first.\n";
    die("</pre>");
}

echo "<span style='color:#a6e3a1;'>✓</span> Token found\n\n";

// 1. Get Account
echo "<b style='color:#f9e2af;'>1. Fetching Account...</b>\n";
$accounts_response = api_call('https://mybusinessaccountmanagement.googleapis.com/v1/accounts', $token);

if ($accounts_response['code'] != 200) {
    echo "<span style='color:#f38ba8;'>✗</span> Accounts API Error\n";
    print_r($accounts_response['body']);
    die("</pre>");
}

$account = $accounts_response['body']['accounts'][0];
$account_name = $account['name']; // accounts/xxx
echo "<span style='color:#a6e3a1;'>✓</span> Account: {$account['accountName']} ({$account_name})\n\n";

// 2. Get Locations
echo "<b style='color:#f9e2af;'>2. Fetching Locations...</b>\n";
$loc_url = "https://mybusinessbusinessinformation.googleapis.com/v1/{$account_name}/locations?readMask=name,title";
$loc_response = api_call($loc_url, $token);

if ($loc_response['code'] != 200) {
    echo "<span style='color:#f38ba8;'>✗</span> Locations API Error\n";
    print_r($loc_response['body']);
    die("</pre>");
}

$location = $loc_response['body']['locations'][0];
$location_name = $location['name']; // locations/xxx
echo "<span style='color:#a6e3a1;'>✓</span> Location: {$location['title']} ({$location_name})\n\n";

// 3. Test Reviews API
echo "<b style='color:#f9e2af;'>3. Testing Reviews API...</b>\n";

// Try different endpoint formats
$review_endpoints = [
    "https://mybusiness.googleapis.com/v4/{$account_name}/{$location_name}/reviews",
    "https://mybusiness.googleapis.com/v4/{$account_name}/locations/" . basename($location_name) . "/reviews",
    "https://mybusiness.googleapis.com/v4/accounts/" . basename($account_name) . "/locations/" . basename($location_name) . "/reviews",
];

foreach ($review_endpoints as $i => $url) {
    echo "   Trying endpoint " . ($i+1) . "...\n";
    echo "   URL: {$url}\n";
    $response = api_call($url, $token);
    echo "   Code: {$response['code']}\n";
    
    if ($response['code'] == 200) {
        echo "   <span style='color:#a6e3a1;'>✓ WORKING!</span>\n";
        echo "   Reviews found: " . count($response['body']['reviews'] ?? []) . "\n";
        break;
    } else {
        echo "   <span style='color:#f38ba8;'>✗</span> " . ($response['body']['error']['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
}

// 4. Test Posts API
echo "\n<b style='color:#f9e2af;'>4. Testing Posts API...</b>\n";

$post_endpoints = [
    "https://mybusiness.googleapis.com/v4/{$account_name}/{$location_name}/localPosts",
    "https://mybusiness.googleapis.com/v4/{$account_name}/locations/" . basename($location_name) . "/localPosts",
    "https://mybusiness.googleapis.com/v4/accounts/" . basename($account_name) . "/locations/" . basename($location_name) . "/localPosts",
];

foreach ($post_endpoints as $i => $url) {
    echo "   Trying endpoint " . ($i+1) . "...\n";
    echo "   URL: {$url}\n";
    $response = api_call($url, $token);
    echo "   Code: {$response['code']}\n";
    
    if ($response['code'] == 200) {
        echo "   <span style='color:#a6e3a1;'>✓ WORKING!</span>\n";
        echo "   Posts found: " . count($response['body']['localPosts'] ?? []) . "\n";
        break;
    } else {
        echo "   <span style='color:#f38ba8;'>✗</span> " . ($response['body']['error']['message'] ?? 'Unknown error') . "\n";
    }
    echo "\n";
}

// 5. Test Insights API
echo "\n<b style='color:#f9e2af;'>5. Testing Insights API...</b>\n";
$start = new DateTime('-7 days');
$end = new DateTime();

$insights_url = "https://businessprofileperformance.googleapis.com/v1/{$location_name}:fetchMultiDailyMetricsTimeSeries";
$insights_url .= "?dailyRange.startDate.year=" . $start->format('Y');
$insights_url .= "&dailyRange.startDate.month=" . $start->format('n');
$insights_url .= "&dailyRange.startDate.day=" . $start->format('j');
$insights_url .= "&dailyRange.endDate.year=" . $end->format('Y');
$insights_url .= "&dailyRange.endDate.month=" . $end->format('n');
$insights_url .= "&dailyRange.endDate.day=" . $end->format('j');
$insights_url .= "&dailyMetrics=WEBSITE_CLICKS&dailyMetrics=CALL_CLICKS&dailyMetrics=BUSINESS_DIRECTION_REQUESTS";

echo "   URL: " . substr($insights_url, 0, 100) . "...\n";
$response = api_call($insights_url, $token);
echo "   Code: {$response['code']}\n";

if ($response['code'] == 200) {
    echo "   <span style='color:#a6e3a1;'>✓ Insights API WORKING!</span>\n";
} else {
    echo "   <span style='color:#f38ba8;'>✗</span> " . ($response['body']['error']['message'] ?? 'Unknown error') . "\n";
}

echo "\n<b style='color:#89b4fa;'>✅ Debug Complete</b>\n";
echo "</pre>";

function api_call($url, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $result = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ['code' => $code, 'body' => json_decode($result, true)];
}
?>
