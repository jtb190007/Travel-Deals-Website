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

// Check if data was sent
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Loop through the form data and insert it into the database
    $count = count($_POST) / 5; // Assuming 5 fields per passenger (first name, last name, dob, ssn, category)
    
    for ($i = 0; $i < $count; $i++) {
        // Get the data for each passenger
        $firstName = $_POST["firstName$i"];
        $lastName = $_POST["lastName$i"];
        $dob = $_POST["dob$i"];
        $ssn = $_POST["ssn$i"];
        $category = $_POST["category$i"];

        // Insert the data into the 'guests' table
        $stmt = $pdo->prepare("INSERT INTO guests (ssn, first_name, last_name, date_of_birth, category) 
                               VALUES (:ssn, :first_name, :last_name, :date_of_birth, :category)");
        $stmt->execute([
            ':ssn' => $ssn,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':date_of_birth' => $dob,
            ':category' => $category
        ]);
    }

    // Respond with a success message (JSON response)
    echo json_encode(['status' => 'success', 'message' => 'Guest data saved successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}

?>
