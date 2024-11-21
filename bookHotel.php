<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// Validate required fields
$requiredFields = ['hotel-id', 'checkin', 'checkout', 'adults', 'children', 'infants', 'rooms'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Extract and validate data
$hotelId = $data['hotel-id'];
$checkinDate = $data['checkin'];
$checkoutDate = $data['checkout'];
$numAdults = (int)$data['adults'];
$numChildren = (int)$data['children'];
$numInfants = (int)$data['infants'];
$numRooms = (int)$data['rooms'];

// Check for valid input values
if ($numAdults < 0 || $numChildren < 0 || $numInfants < 0 || $numRooms <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid number of adults, children, infants, or rooms']);
    exit;
}

// Load hotel data
$hotelsData = json_decode(file_get_contents('availablehotels.json'), true);
if ($hotelsData === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load hotel data']);
    exit;
}

// Find hotel details
$hotelDetails = null;
foreach ($hotelsData['hotels'] as $hotel) {
    if ($hotel['hotel-id'] == $hotelId) {
        $hotelDetails = $hotel;
        break;
    }
}

if (!$hotelDetails) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Hotel not found']);
    exit;
}

// Calculate total price
$totalPrice = $hotelDetails['price-per-night'] * $numRooms;

// Create XML structure
$xml = new SimpleXMLElement('<booking/>');

$hotelNode = $xml->addChild('hotel');
$hotelNode->addChild('hotel-id', $hotelDetails['hotel-id']);
$hotelNode->addChild('hotel-name', $hotelDetails['hotel-name']);
$hotelNode->addChild('city', $hotelDetails['city']);
$hotelNode->addChild('price-per-night', $hotelDetails['price-per-night']);
$hotelNode->addChild('rooms', $numRooms);
$hotelNode->addChild('total-price', $totalPrice);

$bookingDetailsNode = $xml->addChild('booking-details');
$bookingDetailsNode->addChild('checkin', $checkinDate);
$bookingDetailsNode->addChild('checkout', $checkoutDate);
$bookingDetailsNode->addChild('adults', $numAdults);
$bookingDetailsNode->addChild('children', $numChildren);
$bookingDetailsNode->addChild('infants', $numInfants);

// File to store bookings
$xmlFilename = 'bookings.xml';

// Check if the XML file exists, and create it if necessary
if (!file_exists($xmlFilename)) {
    // Create the XML file with initial structure if it doesn't exist
    $initialXml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<bookings></bookings>";
    if (file_put_contents($xmlFilename, $initialXml) === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to create the XML file']);
        exit;
    }
    echo "XML file created successfully.<br>";
} else {
    echo "XML file already exists.<br>";
}

// Output the file's contents before loading it for debugging purposes
echo "XML file contents before loading: <br>";
echo nl2br(file_get_contents($xmlFilename));

// Try to load the XML file
$xmlContent = simplexml_load_file($xmlFilename);

// Check if the XML loading failed
if ($xmlContent === false) {
    echo json_encode(['success' => false, 'message' => 'Failed to load XML data']);
    exit;
}

// Continue with your XML modification and saving logic
$booking = $xmlContent->addChild('booking');
$booking->addChild('hotel-id', $hotelDetails['hotel-id']);
$booking->addChild('hotel-name', $hotelDetails['hotel-name']);
$booking->addChild('city', $hotelDetails['city']);
$booking->addChild('price-per-night', $hotelDetails['price-per-night']);
$booking->addChild('rooms', $numRooms);
$booking->addChild('total-price', $totalPrice);
$booking->addChild('checkin', $checkinDate);
$booking->addChild('checkout', $checkoutDate);
$booking->addChild('adults', $numAdults);
$booking->addChild('children', $numChildren);
$booking->addChild('infants', $numInfants);

// Save the updated XML back to the file
if ($xmlContent->asXML($xmlFilename)) {
    echo json_encode(['success' => true, 'message' => 'Booking saved successfully', 'total-price' => $totalPrice]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save the booking']);
}

?>
