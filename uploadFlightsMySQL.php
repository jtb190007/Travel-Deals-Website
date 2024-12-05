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

    // Database connection
    error_log("uploadFlights.php: Connecting to database...");
    $conn = new mysqli('localhost', 'root', 'password', 'flights_db');

    if ($conn->connect_error) {
        error_log("uploadFlights.php: Database connection failed: " . $conn->connect_error);
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    error_log("uploadFlights.php: Database connection established.");

    // Prepare SQL statement
    $stmt = $conn->prepare(
        "INSERT INTO flights (flight_id, origin, destination, departure_date, arrival_date, departure_time, arrival_time, available_seats, price)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$stmt) {
        error_log("uploadFlights.php: Failed to prepare SQL statement: " . $conn->error);
        throw new Exception('Failed to prepare SQL statement.');
    }
    error_log("uploadFlights.php: SQL statement prepared successfully.");

    // Insert each flight into the database
    foreach ($flights->flight as $flight) {
        $flightId = (string)$flight->{'flight-id'};
        $origin = (string)$flight->origin;
        $destination = (string)$flight->destination;
        $departureDate = (string)$flight->{'departure-date'};
        $arrivalDate = (string)$flight->{'arrival-date'};
        $departureTime = (string)$flight->{'departure-time'};
        $arrivalTime = (string)$flight->{'arrival-time'};
        $availableSeats = intval($flight->{'available-seats'});
        $price = floatval($flight->price);

        error_log("uploadFlights.php: Inserting flight ID: $flightId, Origin: $origin, Destination: $destination...");

        // Bind parameters and execute
        if (!$stmt->bind_param('sssssssdi', $flightId, $origin, $destination, $departureDate, $arrivalDate, $departureTime, $arrivalTime, $availableSeats, $price)) {
            error_log("uploadFlights.php: Failed to bind parameters for flight ID: $flightId. Error: " . $stmt->error);
            throw new Exception('Failed to bind parameters.');
        }

        if (!$stmt->execute()) {
            error_log("uploadFlights.php: Failed to execute SQL statement for flight ID: $flightId. Error: " . $stmt->error);
            throw new Exception('Failed to execute SQL statement.');
        }

        error_log("uploadFlights.php: Flight ID: $flightId inserted successfully.");
    }

    $stmt->close();
    $conn->close();

    error_log("uploadFlights.php: All flights uploaded successfully.");
    echo json_encode(['success' => true, 'message' => 'Flights uploaded successfully.']);
} catch (Exception $e) {
    // Log the error
    error_log("uploadFlights.php: Error occurred - " . $e->getMessage());

    // Return an error response
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
