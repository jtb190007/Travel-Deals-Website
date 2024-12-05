<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$inputData = file_get_contents('php://input');
$data = json_decode($inputData, true);

$jsonData = file_get_contents('registeredusers.json');
$registeredUsers = json_decode($jsonData, true);

if ($registeredUsers === null) {
    echo "Error decoding JSON data.";
    exit;
}

$extra = array(
	'phone-number' => $data['phone-number'],
        'first-name' => $data['first-name'],
	'last-name' => $data['last-name'],
	'birth-date' => $data['birth-date'],
	'gender' => $data['gender'],
	'email' => $data['email'],
        'password' => $data['password']
);

array_push($registeredUsers['users'], $extra);
$final_data = json_encode($registeredUsers);

file_put_contents('registeredusers.json', $final_data)



?>
