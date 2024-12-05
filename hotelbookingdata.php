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

$xml = simplexml_load_file('bookings.xml');

if ($xml === false) {
    echo "Error loading XML file.";
    exit;
}

foreach ($xml->booking as $booking) {
    $hotelId = (int)$booking->{"hotel-id"};
    $pricePerNight = (float)$booking->{"price-per-night"};
    $rooms = (int)$booking->rooms;
    $totalPrice = (float)$booking->{"total-price"};
    $checkin = (string)$booking->checkin;
    $checkout = (string)$booking->checkout;

    $checkinDate = date('Y-m-d H:i:s', strtotime($checkin));
    $checkoutDate = date('Y-m-d H:i:s', strtotime($checkout));

    $hotelBookingId = 1;

    $stmt = $pdo->prepare("INSERT INTO hotel_booking 
        (hotel_booking_id, hotel_id, price_per_night, rooms, total_price, checkin, checkout) 
        VALUES 
        (:hotel_booking_id, :hotel_id, :price_per_night, :rooms, :total_price, :checkin, :checkout)");

    $stmt->execute([
        ':hotel_booking_id' => $hotelBookingId,
        ':hotel_id' => $hotelId,
        ':price_per_night' => $pricePerNight,
        ':rooms' => $rooms,
        ':total_price' => $totalPrice,
        ':checkin' => $checkinDate,
        ':checkout' => $checkoutDate,
    ]);

    $hotelBookingId = $pdo->lastInsertId();

    echo "Booking ID: " . $hotelBookingId . " inserted successfully!<br>";
}

echo "Hotel booking data inserted successfully!";
?>
