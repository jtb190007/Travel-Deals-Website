const express = require("express");
const fs = require("fs");
const { parseString, Builder } = require("xml2js");

const app = express();
app.use(express.json()); // For parsing JSON request bodies

// Serve static files (e.g., HTML, CSS, JS)
app.use(express.static(__dirname));
// CORS configuration to allow all methods from the same origin
app.use((req, res, next) => {
    res.setHeader("Access-Control-Allow-Origin", "*");
    res.setHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
    res.setHeader("Access-Control-Allow-Headers", "Content-Type");
    if (req.method === "OPTIONS") {
      return res.status(200).end();
    }
    next();
  });
  
// Endpoint to update available seats in the XML
app.post("/updateSeats", (req, res) => {
    const { flightId, decrementBy } = req.body;

    if (!flightId || decrementBy === undefined) {
        return res.status(400).send("Invalid request body");
    }

    // Read the XML file
    fs.readFile("flights.xml", "utf8", (err, data) => {
        if (err) {
            console.error("Error reading XML file:", err);
            return res.status(500).send("Error reading XML file");
        }

        // Parse the XML file
        parseString(data, (err, result) => {
            if (err) {
                console.error("Error parsing XML:", err);
                return res.status(500).send("Error parsing XML");
            }

            // Locate the flight by flightId
            const flights = result.flights.flight;
            const flight = flights.find(f => f["flight-id"][0] === flightId);

            if (!flight) {
                return res.status(404).send("Flight not found");
            }

            // Update available seats
            const currentSeats = parseInt(flight["available-seats"][0]);
            const newSeats = currentSeats - decrementBy;

            if (newSeats < 0) {
                return res.status(400).send("Not enough seats available");
            }

            flight["available-seats"][0] = newSeats.toString();

            // Convert back to XML
            const builder = new Builder();
            const updatedXML = builder.buildObject(result);

            // Write the updated XML back to the file
            fs.writeFile("flights.xml", updatedXML, "utf8", (err) => {
                if (err) {
                    console.error("Error writing XML file:", err);
                    return res.status(500).send("Error writing XML file");
                }

                console.log(`Updated flight ${flightId}: ${newSeats} seats remaining`);
                res.send({ message: "Flight updated successfully", newSeats });
            });
        });
    });
});

// Start the server
const PORT = 3000;
app.listen(PORT, () => {
    console.log(`Server running on http://localhost:${PORT}`);
});
