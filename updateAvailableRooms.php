<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$inputData = file_get_contents('php://input');

file_put_contents('php://stdout', "Received data: " . $inputData . "\n");

$data = json_decode($inputData, true);

if ($data === null) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON in request']);
    exit;
}

file_put_contents('php://stdout', "Parsed data: " . print_r($data, true) . "\n");

if (!isset($data['hotel-id'])) {
    echo json_encode(['success' => false, 'message' => 'Hotel ID is missing']);
    exit;
}

$hotelId = $data['hotel-id'];
$decrementBy = $data['decrementBy'];

file_put_contents('php://stdout', "Hotel ID: " . $hotelId . ", Decrement By: " . $decrementBy . "\n");

$hotelsData = json_decode(file_get_contents('availablehotels.json'), true);

if ($hotelsData === null) {
    echo json_encode(['success' => false, 'message' => 'Failed to load hotel data']);
    exit;
}

file_put_contents('php://stdout', "Loaded Hotels Data: " . print_r($hotelsData, true) . "\n");

$hotels = $hotelsData['hotels'];

$hotelUpdated = false;
foreach ($hotels as &$hotel) {
    if ($hotel['hotel-id'] == $hotelId) {
        if ($hotel['available-rooms'] >= $decrementBy) {
            $hotel['available-rooms'] -= $decrementBy;
            $hotelUpdated = true;
        } else {
            echo json_encode(['success' => false, 'message' => 'Not enough rooms available']);
            exit;
        }
        break;
    }
}

if ($hotelUpdated) {
    $hotelsData['hotels'] = $hotels;
    file_put_contents('availablehotels.json', json_encode($hotelsData, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true, 'message' => 'Rooms updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Hotel not found']);
}

?>
