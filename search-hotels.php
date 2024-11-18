<?php
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['city'])) {
    die("City not selected or missing in the request.");
}

$city = $data['city'];
$checkin = $data['checkin'];
$checkout = $data['checkout'];
$adults = $data['adults'];
$children = $data['children'];
$infants = $data['infants'];

$hotels = json_decode(file_get_contents('availablehotels.json'), true);

$filteredHotels = array_filter($hotels, function($hotel) use ($city) {
    return strtolower($hotel['city']) === strtolower($city);
});

$html = '';
foreach ($filteredHotels as $hotel) {
    $html .= "
        <div>
            Hotel: {$hotel['hotel-name']} <br>
            Price: \${$hotel['price-per-night']} per night <br>
            Available rooms: {$hotel['available-rooms']} <br>
            <button onclick='addToCart({$hotel['hotel-id']}, \"{$hotel['hotel-name']}\", {$hotel['price-per-night']}, 1, \"$checkin\", \"$checkout\")'>Add to Cart</button>
        </div><br>
    ";
}

echo $html;
?>