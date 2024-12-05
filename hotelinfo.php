<?php

$host = 'localhost';
$dbname = 'hoteldb';
$username = 'yofriend';
$password = 'helloworld123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

$pdo->exec("DELETE FROM hotels");

$jsonData = file_get_contents('availablehotels.json');  

$data = json_decode($jsonData, true);

if ($data === null) {
    echo "Error decoding JSON data.";
    exit;
}

$stmt = $pdo->prepare("INSERT INTO hotels (hotel_id, hotel_name, city, price_per_night) VALUES (:hotel_id, :hotel_name, :city, :price_per_night)");

foreach ($data['hotels'] as $hotel) {
    $stmt->execute([
        ':hotel_id' => $hotel['hotel-id'],
        ':hotel_name' => $hotel['hotel-name'],
        ':city' => $hotel['city'],
        ':price_per_night' => $hotel['price-per-night']
    ]);
}

echo "Data loaded successfully!";
?>
