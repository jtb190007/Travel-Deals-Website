<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

// File to store bookings
$xmlFilename = 'contact.xml';

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

$xmlSize = $xmlContent->count() + 1;

// Continue with your XML modification and saving logic
$comment = $xmlContent->addChild('contact');

$comment->addChild('contact-id', $xmlSize);
$comment->addChild('comment', $data['comment']);
$comment->addChild('phone-number', $data['phone-number']);
$comment->addChild('first-name', $data['first-name']);
$comment->addChild('last-name', $data['last-name']);
$comment->addChild('birth-date', $data['birth-date']);
$comment->addChild('gender', $data['gender']);
$comment->addChild('email', $data['email']);

// Save the updated XML back to the file
if ($xmlContent->asXML($xmlFilename)) {
    echo json_encode(['success' => true, 'message' => 'Comment saved successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save the comment']);
}

?>
