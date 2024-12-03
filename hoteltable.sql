CREATE TABLE hotels (
    hotel_id INT PRIMARY KEY,
    hotel_name VARCHAR(255),
    city VARCHAR(255),
    price_per_night DECIMAL(10, 2)
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

CREATE TABLE hotel_booking (
    hotel_booking_id INT PRIMARY KEY,
    hotel_id INT,
    check_in_date DATE,
    check_out_date DATE,
    number_of_rooms INT,
    price_per_night DECIMAL(10, 2),
    total_price DECIMAL(10, 2),
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id)
);
