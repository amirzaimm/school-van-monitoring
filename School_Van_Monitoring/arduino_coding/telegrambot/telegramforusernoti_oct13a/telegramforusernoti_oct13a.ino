#include <WiFi.h>
#include <UniversalTelegramBot.h>
#include <WiFiClientSecure.h>
#include <NTPClient.h>
#include <WiFiUdp.h>

// Replace these with your actual WiFi credentials and Telegram Bot Token
const char* ssid = "amir";                // Your WiFi SSID
const char* password = "1sampai0";        // Your WiFi Password
const String botToken = "7232670812:AAGbC9uNE2Rgb-zbWFGbsPKWMYdnP6ZBgrs";  // Your Telegram Bot Token

// Define your Telegram chat IDs (where you want to receive the notifications)
String authorized_chat_ids[] = {
    "1061844442",  // Admin chat ID
    
    // Add more user chat IDs as needed
};

WiFiClientSecure client;
UniversalTelegramBot bot(botToken, client);

// NTP client to get time
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", 28800, 60000); // GMT+8 (Sekolah Bandar Tasik Kesuma), sync every 60 seconds

unsigned long lastTimeBotRan = 0;
const long botDelay = 1000;  // Check for new messages every 1 second

void handleNewMessages(int numNewMessages) {
  Serial.println("Checking for new messages...");
  for (int i = 0; i < numNewMessages; i++) {
    String chat_id_received = String(bot.messages[i].chat_id);
    String text = bot.messages[i].text;

    Serial.print("Message received from: ");
    Serial.println(chat_id_received);
    Serial.print("Message text: ");
    Serial.println(text);

    // Get the current time and date
    String formattedDate = timeClient.getFormattedTime();
    time_t rawTime = timeClient.getEpochTime();
    struct tm* timeInfo = localtime(&rawTime);

    char dateBuffer[30];
    snprintf(dateBuffer, sizeof(dateBuffer), "%02d/%02d/%04d %02d:%02d:%02d", 
             timeInfo->tm_mday, timeInfo->tm_mon + 1, timeInfo->tm_year + 1900,
             timeInfo->tm_hour, timeInfo->tm_min, timeInfo->tm_sec);

    // Respond to the /start command
    if (text == "/start") {
      String welcomeMessage = "Welcome to the School Van Monitoring System!\n";
      welcomeMessage += "You will receive notifications when the van arrives at school or home.\n";
      welcomeMessage += "First, get your chat ID here: /getid\n";
      welcomeMessage += "Please register using the following link: [Register Here](http://172.20.10.4/school_van_monitoring/submit.php)";
      bot.sendMessage(chat_id_received, welcomeMessage, "Markdown");
    }

    // Respond to the /getid command to send the chat ID back to the user
    if (text == "/getid") {
      String idMessage = "Your chat ID is: " + chat_id_received;
      bot.sendMessage(chat_id_received, idMessage, "");
    }

    // Check if the command is to simulate van arrival at school
    if (text == "ARRIVED") {
      String message = "The School Van has arrived at Sekolah Bandar Tasik Kesuma.\n";
      message += "Date: " + String(dateBuffer);
      // Notify all authorized users
      for (const String& userId : authorized_chat_ids) {
        bot.sendMessage(userId, message, "");
      }
    }

    // Check if the command is to simulate van arrival at home
    if (text == "ARRIVED00") {
      String message = "The School Van has arrived at home.\n";
      message += "Date: " + String(dateBuffer);
      // Notify all authorized users
      for (const String& userId : authorized_chat_ids) {
        bot.sendMessage(userId, message, "");
      }
    }
  }
}

void setup() {
  // Initialize serial communication
  Serial.begin(115200);

  // Connect to Wi-Fi
  WiFi.begin(ssid, password);
  Serial.print("Connecting to WiFi...");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }
  Serial.println("\nConnected to WiFi!");

  // Initialize the Telegram bot
  client.setInsecure();  // Disable SSL certificate validation

  // Initialize NTP client
  timeClient.begin();
  timeClient.update();  // Fetch the initial time
}

void loop() {
  // Update time client
  timeClient.update();

  // Check if it's time to get new messages from the bot
  if (millis() - lastTimeBotRan > botDelay) {
    int numNewMessages = bot.getUpdates(bot.last_message_received + 1);
    while (numNewMessages) {
      handleNewMessages(numNewMessages);
      numNewMessages = bot.getUpdates(bot.last_message_received + 1);
    }
    lastTimeBotRan = millis();
  }

  // Check if data is available on Serial Monitor (manual input simulation)
  if (Serial.available() > 0) {
    String command = Serial.readStringUntil('\n');

    // Get the current time and date
    String formattedDate = timeClient.getFormattedTime();
    time_t rawTime = timeClient.getEpochTime();
    struct tm* timeInfo = localtime(&rawTime);

    char dateBuffer[30];
    snprintf(dateBuffer, sizeof(dateBuffer), "%02d/%02d/%04d %02d:%02d:%02d", 
             timeInfo->tm_mday, timeInfo->tm_mon + 1, timeInfo->tm_year + 1900,
             timeInfo->tm_hour, timeInfo->tm_min, timeInfo->tm_sec);

    // Check if the command is to simulate van arrival at school
    if (command == "ARRIVED") {
      String message = "The School Van has arrived at Sekolah Bandar Tasik Kesuma.\n";
      message += "Date: " + String(dateBuffer);
      // Notify all authorized users
      for (const String& userId : authorized_chat_ids) {
        bot.sendMessage(userId, message, "");
      }
    }

    // Check if the command is to simulate van arrival at home
    if (command == "ARRIVED00") {
      String message = "The School Van has arrived at home.\n";
      message += "Date: " + String(dateBuffer);
      // Notify all authorized users
      for (const String& userId : authorized_chat_ids) {
        bot.sendMessage(userId, message, "");
      }
    }
  }
}