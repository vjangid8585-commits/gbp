<?php
// Quick check of locations table

$pdo = new PDO("mysql:host=localhost;dbname=gbp_db", 'root', '');

echo "<pre>";
echo "=== Locations in Database ===\n\n";

$stmt = $pdo->query("SELECT id, google_location_id, business_name FROM locations");
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($locations)) {
    echo "No locations found! Run sync/locations first.\n";
} else {
    foreach ($locations as $loc) {
        echo "ID: {$loc['id']}\n";
        echo "Google ID: {$loc['google_location_id']}\n";
        echo "Name: {$loc['business_name']}\n";
        echo "---\n";
    }
}

echo "\n=== Insights in Database ===\n\n";
$stmt = $pdo->query("SELECT * FROM insights LIMIT 5");
$insights = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($insights)) {
    echo "No insights stored yet.\n";
} else {
    print_r($insights);
}

echo "</pre>";
?>
