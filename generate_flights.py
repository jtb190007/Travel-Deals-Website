import xml.etree.ElementTree as ET
import xml.dom.minidom as minidom
import os
import random
from datetime import datetime, timedelta

# List of cities in Texas and California
texas_cities = ["Dallas", "Houston", "San Antonio", "Austin", "El Paso"]
california_cities = ["Los Angeles", "San Francisco", "San Diego", "Sacramento", "Oakland"]

# Generate random flights
def generate_flights(num_flights=50):
    flights = []
    for i in range(1, num_flights + 1):
        # Determine origin and destination
        origin = random.choice(texas_cities if i % 2 == 1 else california_cities)
        destination = random.choice(california_cities if i % 2 == 1 else texas_cities)
        base_date = datetime(2024, 9, 1) + timedelta(days=random.randint(0, 90))
        departure_time = base_date + timedelta(hours=random.randint(6, 18))  # Departure between 6 AM to 6 PM
        arrival_time = departure_time + timedelta(hours=random.randint(2, 4))  # Flight duration 2-4 hours

        # Add one-way flight
        flights.append({
            "flight-id": f"{'TX' if i % 2 == 1 else 'CA'}{100 + i}",
            "origin": origin,
            "destination": destination,
            "departure-date": departure_time.strftime("%m-%d-%Y"),
            "arrival-date": arrival_time.strftime("%m-%d-%Y"),
            "departure-time": departure_time.strftime("%I:%M %p"),
            "arrival-time": arrival_time.strftime("%I:%M %p"),
            "available-seats": str(random.randint(30, 50)),
            "price": str(random.randint(150, 250))
        })

        # Add round-trip flight
        if i % 2 == 0:  # Alternate between one-way and round-trip flights
            return_date = base_date + timedelta(days=random.randint(1, 7))  # Return date within 1-7 days
            return_departure_time = return_date + timedelta(hours=random.randint(6, 18))
            return_arrival_time = return_departure_time + timedelta(hours=random.randint(2, 4))
            flights.append({
                "flight-id": f"{'CA' if i % 2 == 1 else 'TX'}{200 + i}",
                "origin": destination,  # Reverse the origin and destination
                "destination": origin,
                "departure-date": return_departure_time.strftime("%m-%d-%Y"),
                "arrival-date": return_arrival_time.strftime("%m-%d-%Y"),
                "departure-time": return_departure_time.strftime("%I:%M %p"),
                "arrival-time": return_arrival_time.strftime("%I:%M %p"),
                "available-seats": str(random.randint(30, 50)),
                "price": str(random.randint(150, 250))
            })

    return flights

# Create the root element
flights_root = ET.Element("flights")

# Populate the XML structure with generated flight data
flight_data = generate_flights(50)
for flight in flight_data:
    flight_element = ET.SubElement(flights_root, "flight")
    for key, value in flight.items():
        sub_element = ET.SubElement(flight_element, key)
        sub_element.text = value

# Convert the tree to a pretty-printed XML string
xml_str = ET.tostring(flights_root, encoding="utf-8")
pretty_xml = minidom.parseString(xml_str).toprettyxml(indent="    ")

# Write the formatted XML to a file
output_path = os.path.join(os.path.dirname(__file__), "output.xml")
with open(output_path, "w", encoding="utf-8") as f:
    f.write(pretty_xml)

print(f"Formatted XML file with flight data has been created at {output_path}")
