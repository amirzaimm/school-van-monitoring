#include <WiFi.h>
#include <UniversalTelegramBot.h>
#include <WiFiClientSecure.h>

// Replace these with your actual WiFi credentials and Telegram Bot Token
const char* ssid = "amir";                // Your WiFi SSID
const char* password = "1sampai0";        // Your WiFi Password
const String botToken = "7232670812:AAGbC9uNE2Rgb-zbWFGbsPKWMYdnP6ZBgrs"; // Your Telegram Bot Token

// Define admin chat_id (your chat_id)
const String admin_chat_id = "1061844442"; // Your admin Telegram chat ID

// Define other user chat IDs
const String authorized_chat_ids[] = {
    "", // User 1 chat ID (your friend's chat ID)
    "",  // User 2 chat ID (add more as needed)
};

WiFiClientSecure client;
UniversalTelegramBot bot(botToken, client);

int lastUpdateId = 0; // Initialize a variable to keep track of the last update ID

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
}

void loop() {
  // Check for new messages
  int numNewMessages = bot.getUpdates(lastUpdateId + 1);

  // Handle incoming messages
  for (int i = 0; i < numNewMessages; i++) {
    String incomingText = bot.messages[i].text;
    String current_chat_id = String(bot.messages[i].chat_id); // Get chat ID

    Serial.print("Received message: ");
    Serial.println(incomingText);
    Serial.print("Chat ID: ");
    Serial.println(current_chat_id); // Print the chat ID to the Serial Monitor

    // Respond to the /start command
    if (incomingText == "/start") {
      // Send welcome message to the user
      String welcomeMessage = "Welcome to the School Van Monitoring System!\nYou will receive notifications when the van arrives at school or home.";
      bot.sendMessage(current_chat_id, welcomeMessage, "");

      // Send chat ID to admin (only you will receive this information)
      if (current_chat_id != admin_chat_id) {
        String infoToAdmin = "New user started the bot. Chat ID: " + current_chat_id;
        bot.sendMessage(admin_chat_id, infoToAdmin, "");
      } else {
        Serial.println("This is the admin. Not sending chat ID.");
      }
    }
    
    // Check for arrival commands
    if (incomingText == "ARRIVED") {
      // Notify all authorized users
      for (const String& userId : authorized_chat_ids) {
        bot.sendMessage(userId, "The School Van has arrived at Sekolah Bandar Tasik Kesuma", "");
        Serial.printf("Notification sent to user ID: %s\n", userId.c_str()); // Debugging line
      }
      // Notify current user
      bot.sendMessage(current_chat_id, "The School Van has arrived at Sekolah Bandar Tasik Kesuma", "");
    }
    else if (incomingText == "ARRIVED00") {
      // Notify all authorized users
      for (const String& userId : authorized_chat_ids) {
        bot.sendMessage(userId, "The School Van has arrived at home", "");
        Serial.printf("Notification sent to user ID: %s\n", userId.c_str()); // Debugging line
      }
      // Notify current user
      bot.sendMessage(current_chat_id, "The School Van has arrived at home", "");
    }

    // Update last update ID
    lastUpdateId = bot.messages[i].update_id; // Store the latest update ID
  }

  // Add a delay to avoid hitting the Telegram API too frequently
  delay(1000); // Adjust as needed
}
