<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

try {
    // Ensure the request method is GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Invalid request method.');
    }

    // Validate and retrieve parameters
    $flightId = $_GET['flightId'] ?? null;
    $decrementBy = intval($_GET['decrementBy'] ?? 0);

    if (empty($flightId) || $decrementBy <= 0) {
        throw new Exception('Invalid parameters. flightId and decrementBy are required.');
    }

    // Check if the XML file exists
    $xmlFile = 'flights.xml';
    if (!file_exists($xmlFile)) {
        throw new Exception('Flights data file (flights.xml) not found.');
    }

    // Load the XML file
    $xml = simplexml_load_file($xmlFile);
    if ($xml === false) {
        throw new Exception('Failed to load flights.xml. Ensure the file is properly formatted.');
    }

    $flightFound = false;

    // Iterate over flights to find the matching flight ID
    foreach ($xml->flight as $flight) {
        if ((string)$flight->{'flight-id'} === $flightId) {
            $availableSeats = intval($flight->{'available-seats'});

            // Check if there are enough available seats
            if ($availableSeats >= $decrementBy) {
                $flight->{'available-seats'} = $availableSeats - $decrementBy;
                $flightFound = true;

                // Save the updated XML
                $xml->asXML($xmlFile);

                // Return success response
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Seats updated successfully.',
                    'flightId' => $flightId,
                    'remainingSeats' => $availableSeats - $decrementBy
                ]);
                exit;
            } else {
                throw new Exception('Not enough seats available.');
            }
        }
    }

    // If no matching flight ID is found
    if (!$flightFound) {
        throw new Exception('Flight not found.');
    }
} catch (Exception $e) {
    // Handle exceptions and return an error response
    http_response_code(400);
    error_log('Error in updateSeats.php: ' . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
