/* Uncomment if tables do not exist
CREATE TABLE hotels (
    hotel_id INT PRIMARY KEY,
    hotel_name VARCHAR(255),
    city VARCHAR(255),
    price_per_night DECIMAL(10, 2)
);

CREATE TABLE hotel_booking (
    hotel_booking_id INT PRIMARY KEY,
    hotel_id INT,
    checkin DATE,
    checkout DATE,
    rooms INT,
    price_per_night DECIMAL(10, 2),
    total_price DECIMAL(10, 2),
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id)
);

CREATE TABLE guests (
    ssn CHAR(9) PRIMARY KEY,
    hotel_booking_id INT,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    date_of_birth DATE,
    category VARCHAR(20),
    FOREIGN KEY (hotel_booking_id) REFERENCES hotel_booking(hotel_booking_id)
);
*/

SELECT * FROM hotel_booking WHERE hotel_booking_id = @hotel_booking_id;

SELECT *
FROM hotel_booking hb
JOIN hotels h ON hb.hotel_id = h.hotel_id
WHERE h.city IN ('Austin', 'Dallas', 'Houston', 'San Antonio')
AND hb.checkin BETWEEN '2024-09-01' AND '2024-10-31';

/*
SELECT * FROM hotel_booking
ORDER BY total_price DESC
LIMIT 1;
*/

/*
SELECT TOP 1 * FROM hotel_booking
ORDER BY total_price DESC;
*/