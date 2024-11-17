
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $flightId = $_GET['flightId'] ?? '';
    $decrementBy = intval($_GET['decrementBy'] ?? 0);

    if (empty($flightId) || $decrementBy <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
        exit;
    }

    $xmlFile = 'flights.xml';
    if (!file_exists($xmlFile)) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'XML file not found']);
        exit;
    }

    $xml = simplexml_load_file($xmlFile);
    $flightFound = false;

    foreach ($xml->flight as $flight) {
        if ((string)$flight->{'flight-id'} === $flightId) {
            $availableSeats = intval($flight->{'available-seats'});
            if ($availableSeats >= $decrementBy) {
                $flight->{'available-seats'} = $availableSeats - $decrementBy;
                $flightFound = true;

                // Save updated XML
                $xml->asXML($xmlFile);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Seats updated successfully',
                    'remainingSeats' => $availableSeats - $decrementBy
                ]);
                exit;
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Not enough seats available'
                ]);
                exit;
            }
        }
    }

    if (!$flightFound) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Flight not found']);
    }
}
?>
