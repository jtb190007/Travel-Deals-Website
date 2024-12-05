CREATE TABLE flights (
  flight_id VARCHAR(10) PRIMARY KEY,
  origin VARCHAR(50),
  destination VARCHAR(50),
  departure_date DATE,
  arrival_date DATE,
  departure_time TIME,
  arrival_time TIME,
  available_seats INT,
  price DECIMAL(10, 2)
);
