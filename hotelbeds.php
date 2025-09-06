<?php
header("Content-Type: text/html");

$apiKey = "731bbff7861f706e83017245582904ee";
$secret = "89d69c1cb2";

$timestamp = time();
$signature = hash("sha256", $apiKey . $secret . $timestamp);

// Example: Hotels search in PMI (sandbox)
$url = "https://api.test.hotelbeds.com/hotel-api/1.0/hotels?destinationCode=PMI";

// cURL init
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Api-key: $apiKey",
    "X-Signature: $signature",
    "Accept: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

// Decode
$data = json_decode($response, true);

// Show results
if (isset($data['hotels']['hotels'])) {
    foreach ($data['hotels']['hotels'] as $hotel) {
        echo "<div style='border:1px solid #ccc; margin:10px; padding:10px;'>";
        echo "<h2>" . $hotel['name']['content'] . "</h2>";
        echo "<p>Category: " . ($hotel['categoryName']['content'] ?? "N/A") . "</p>";
        echo "<p>Destination: " . ($hotel['destinationName'] ?? "N/A") . "</p>";
        echo "<p>Coordinates: " . $hotel['coordinates']['latitude'] . ", " . $hotel['coordinates']['longitude'] . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>No hotels found or invalid response.</p>";
}
?>
