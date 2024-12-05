<?php
header('Content-Type: application/json');

try {
    error_log("uploadFlights.php: Script started.");

    // Ensure the request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        error_log("uploadFlights.php: Invalid request method - " . $_SERVER['REQUEST_METHOD']);
        throw new Exception('Invalid request method. Please use POST.');
    }

    // Ensure 'flightsXml' is provided
    if (!isset($_POST['flightsXml'])) {
        error_log("uploadFlights.php: No XML data provided in POST request.");
        throw new Exception('No XML data received. Please provide flightsXml in the request body.');
    }

    // Retrieve and sanitize the XML data
    $xmlData = trim($_POST['flightsXml']);
    error_log("uploadFlights.php: Received XML data: " . substr($xmlData, 0, 200) . "..."); // Truncate for logging

    if (empty($xmlData)) {
        error_log("uploadFlights.php: The XML data is empty.");
        throw new Exception('The XML data is empty.');
    }

    // Parse the XML data
    try {
        error_log("uploadFlights.php: Parsing XML data...");
        $flights = new SimpleXMLElement($xmlData);
        error_log("uploadFlights.php: XML parsed successfully.");
    } catch (Exception $e) {
        error_log("uploadFlights.php: Failed to parse XML. Error: " . $e->getMessage());
        throw new Exception('Failed to parse XML data. Ensure it is properly formatted.');
    }

    // Path to the JSON file that stores flight data
    $jsonFilePath = 'flights_data.json';

    // Read existing flight data from the JSON file
    $existingFlights = [];
    if (file_exists($jsonFilePath)) {
        $jsonData = file_get_contents($jsonFilePath);
        $existingFlights = json_decode($jsonData, true) ?? [];
        error_log("uploadFlights.php: Existing flight data loaded from JSON file.");
    }

    // Append new flights to the existing data
    foreach ($flights->flight as $flight) {
        $flightData = [
            'flight_id' => (string)$flight->{'flight-id'},
            'origin' => (string)$flight->origin,
            'destination' => (string)$flight->destination,
            'departure_date' => (string)$flight->{'departure-date'},
            'arrival_date' => (string)$flight->{'arrival-date'},
            'departure_time' => (string)$flight->{'departure-time'},
            'arrival_time' => (string)$flight->{'arrival-time'},
            'available_seats' => intval($flight->{'available-seats'}),
            'price' => floatval($flight->price)
        ];

        // Log the flight data being added
        error_log("uploadFlights.php: Adding flight - " . json_encode($flightData));
        $existingFlights[] = $flightData;
    }

    // Save the updated flight data back to the JSON file
    if (file_put_contents($jsonFilePath, json_encode($existingFlights, JSON_PRETTY_PRINT))) {
        error_log("uploadFlights.php: Flights saved successfully to JSON file.");
        echo json_encode(['success' => true, 'message' => 'Flights uploaded successfully.']);
    } else {
        throw new Exception('Failed to save flight data to JSON file.');
    }
} catch (Exception $e) {
    // Log the error
    error_log("uploadFlights.php: Error occurred - " . $e->getMessage());

    // Return an error response
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
