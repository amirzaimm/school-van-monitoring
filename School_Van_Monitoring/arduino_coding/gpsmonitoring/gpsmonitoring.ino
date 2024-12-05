#include <TinyGPS++.h>
#include <HardwareSerial.h>

// Create an instance of the TinyGPS++ object
TinyGPSPlus gps;

// Define Serial port for GPS communication
HardwareSerial gpsSerial(1); // Use Serial1 for GPS, change if necessary

// Define RX and TX pins for GPS connection
const int RXPin = 16; // RX pin connected to GPS module TX
const int TXPin = 17; // TX pin connected to GPS module RX
const int GPSBaud = 9600; // Baud rate of the GPS module (commonly 9600)

void setup() {
  // Initialize Serial Monitor for debugging
  Serial.begin(9600);
  delay(1000);
  Serial.println("Initializing GPS module...");

  // Initialize Serial for GPS communication
  gpsSerial.begin(GPSBaud, SERIAL_8N1, RXPin, TXPin);

  Serial.println("Waiting for GPS signal...");
}

void loop() {
  // Read data from GPS module
  while (gpsSerial.available() > 0) {
    char c = gpsSerial.read();
    gps.encode(c); // Encode the incoming GPS data
    Serial.print(c); // Print raw GPS data for debugging

    // Check if new GPS data is available
    if (gps.location.isUpdated()) {
      // Print latitude and longitude to Serial Monitor
      Serial.print("Latitude: ");
      Serial.println(gps.location.lat(), 6); // Print latitude with 6 decimal places
      Serial.print("Longitude: ");
      Serial.println(gps.location.lng(), 6); // Print longitude with 6 decimal places
      Serial.println("GPS has a valid location fix.");
    } else if (!gps.location.isValid()) {
      Serial.println("Waiting for valid GPS location fix...");
    }

    // Display the number of satellites in view
    if (gps.satellites.isValid()) {
      Serial.print("Satellites in view: ");
      Serial.println(gps.satellites.value());
      if (gps.satellites.value() > 0) {
        Serial.println("GPS is trying to receive signal from satellites.");
      } else {
        Serial.println("No satellites detected. Trying to acquire signal...");
      }
    } else {
      Serial.println("Satellites data is not valid.");
    }

    // Check HDOP (Horizontal Dilution of Precision)
    if (gps.hdop.isValid()) {
      Serial.print("HDOP (Signal Quality): ");
      Serial.println(gps.hdop.value());
      if (gps.hdop.value() <= 5) {
        Serial.println("Good GPS signal quality.");
      } else if (gps.hdop.value() > 5 && gps.hdop.value() <= 20) {
        Serial.println("Fair GPS signal quality.");
      } else {
        Serial.println("Poor GPS signal quality. Ensure clear sky visibility and check antenna.");
      }
    } else {
      Serial.println("HDOP data is not valid.");
    }

    // Optional: Display altitude if available
    if (gps.altitude.isValid()) {
      Serial.print("Altitude (meters): ");
      Serial.println(gps.altitude.meters());
    }

    // Check if the location data is valid
    if (!gps.location.isValid()) {
      Serial.println("GPS location data is not valid. Searching for satellite fix...");
    }
  }

  // Delay before the next loop iteration (optional)
  delay(1000); // Add delay to avoid flooding Serial Monitor with data
}
