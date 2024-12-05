<?php

$host = 'localhost';
$dbname = 'hoteldb';
$username = 'yofriend';
$password = 'helloworld123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO guests (ssn, hotel_booking_id, first_name, last_name, date_of_birth, category) 
            VALUES (:ssn, :hotel_booking_id, :first_name, :last_name, :date_of_birth, :category)";
    
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':ssn', $ssn);
    $stmt->bindParam(':hotel_booking_id', $hotel_booking_id);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':date_of_birth', $date_of_birth);
    $stmt->bindParam(':category', $category);

    $ssn = '33334444';
    $hotel_booking_id = 1;
    $first_name = 'YUSEF';
    $last_name = 'ALIMAM';
    $date_of_birth = '2001-11-11';
    $category = 'adult';

    $stmt->execute();

    echo "Values inserted successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
