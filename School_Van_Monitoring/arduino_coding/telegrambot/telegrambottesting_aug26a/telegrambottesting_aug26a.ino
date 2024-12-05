#include <TinyGPS++.h>
#include <HardwareSerial.h>
#include <WiFi.h>
#include <UniversalTelegramBot.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>

// GPS Configuration
TinyGPSPlus gps;
HardwareSerial gpsSerial(1); // Use Serial1 for GPS
const int RXPin = 16; // RX pin connected to GPS module TX
const int TXPin = 17; // TX pin connected to GPS module RX
const int GPSBaud = 9600; // Baud rate for GPS module

// WiFi credentials
const char* ssid = "amir"; // Your WiFi SSID
const char* password = "1sampai0"; // Your WiFi Password
const String botToken = "7232670812:AAGbC9uNE2Rgb-zbWFGbsPKWMYdnP6ZBgrs"; // Your Telegram Bot Token

// Telegram Bot
WiFiClientSecure client;
UniversalTelegramBot bot(botToken, client);

double previousLatitude = 0;
double previousLongitude = 0;

// Function to handle new Telegram messages
void handleNewMessages(int numNewMessages) {
  for (int i = 0; i < numNewMessages; i++) {
    String chat_id = String(bot.messages[i].chat_id);
    String text = bot.messages[i].text;

    if (text == "/start") {
      String welcomeMessage = "Welcome to the School Van Monitoring System!\n";
      welcomeMessage += "You will receive notifications when the van arrives.\n";
      welcomeMessage += "Please register using the following link: ";
      welcomeMessage += "[Register Here](http://172.20.10.4/school_van_monitoring/submit.php)"; // Update your local IP
      bot.sendMessage(chat_id, welcomeMessage, "Markdown");
    }
  }
}

// Send a notification to a specific user via Telegram
void sendNotification(String message) {
  String chat_id = "YOUR_CHAT_ID"; // Replace with your chat_id
  bot.sendMessage(chat_id, message, "");
}

void setup() {
  // Initialize serial communications
  Serial.begin(115200);
  gpsSerial.begin(GPSBaud, SERIAL_8N1, RXPin, TXPin);
  
  // Initialize Wi-Fi connection
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi!");

  // Initialize Telegram bot
  client.setInsecure();
  Serial.println("Telegram bot initialized.");

  Serial.println("Initializing GPS module...");
}

void loop() {
  // Read data from the GPS module
  while (gpsSerial.available() > 0) {
    char c = gpsSerial.read();
    gps.encode(c);

    // Print raw GPS data for debugging
    Serial.print(c);

    if (gps.location.isUpdated()) {
      double latitude = gps.location.lat();
      double longitude = gps.location.lng();

      Serial.print("Latitude: ");
      Serial.println(latitude, 6);
      Serial.print("Longitude: ");
      Serial.println(longitude, 6);
      
      // Check if the location has changed significantly (e.g., when the van arrives at school/home)
      if (latitude != previousLatitude || longitude != previousLongitude) {
        // Example condition: Replace this with your specific location check logic
        if (latitude == YOUR_SCHOOL_LATITUDE && longitude == YOUR_SCHOOL_LONGITUDE) {
          sendNotification("The School Van has arrived at the school!");
        } else if (latitude == YOUR_HOME_LATITUDE && longitude == YOUR_HOME_LONGITUDE) {
          sendNotification("The School Van has arrived at home!");
        }

        // Update previous coordinates
        previousLatitude = latitude;
        previousLongitude = longitude;
      }
    }
  }

  // Handle Telegram messages
  int numNewMessages = bot.getUpdates(bot.last_message_received + 1);
  while (numNewMessages) {
    handleNewMessages(numNewMessages);
    numNewMessages = bot.getUpdates(bot.last_message_received + 1);
  }

  // Add a delay before the next loop iteration
  delay(1000);
}
