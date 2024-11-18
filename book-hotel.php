<?php
$data = json_decode($_POST['data'], true);

$hotels = json_decode(file_get_contents('availablehotels.json'), true);

foreach ($hotels as &$hotel) {
    if ($hotel['hotel-id'] == $data['hotelId']) {
        $hotel['available-rooms'] -= $data['rooms'];
        break;
    }
}

file_put_contents('availablehotels.json', json_encode($hotels, JSON_PRETTY_PRINT));

$xml = new SimpleXMLElement('<bookings/>');
$booking = $xml->addChild('booking');
$booking->addChild('hotel-id', $data['hotelId']);
$booking->addChild('hotel-name', $data['hotelName']);
$booking->addChild('price-per-night', $data['price']);
$booking->addChild('rooms', $data['rooms']);
$booking->addChild('checkin', $data['checkin']);
$booking->addChild('checkout', $data['checkout']);
$booking->addChild('total-price', $data['rooms'] * $data['price']);

$xml->asXML('bookings.xml');

echo "Booking successful!";
?>