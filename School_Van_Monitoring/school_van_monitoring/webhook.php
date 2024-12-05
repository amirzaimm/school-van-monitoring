<?php
// Database connection
include('db_connection.php'); // Include your database connection file

// Get the incoming updates from Telegram
$update = json_decode(file_get_contents("php://input"), TRUE);

// Check if the message is from a user
if (isset($update['message'])) {
    $chat_id = $update['message']['chat']['id']; // Get chat ID
    $username = $update['message']['chat']['username']; // Get username if available
    $name = $update['message']['chat']['first_name']; // Optional: Get the user's first name

    // Store the chat_id and username in your database
    storeChatID($chat_id, $username, $name);
    
    // Send a welcome message
    $message = "Welcome to the bot, " . ($username ? "@" . $username : "User") . "!";
    sendMessage($chat_id, $message);
}

// Function to store the chat ID and username in the database
function storeChatID($chat_id, $username, $name) {
    global $conn;
    // Check if the user already exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE telegram_id = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // If user does not exist, insert a new record
        $stmt = $conn->prepare("INSERT INTO users (telegram_id, telegram_chat_id, name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $chat_id, $name);
        $stmt->execute();
    } else {
        // Update the existing record with the chat ID
        $stmt = $conn->prepare("UPDATE users SET telegram_chat_id = ? WHERE telegram_id = ?");
        $stmt->bind_param("ss", $chat_id, $username);
        $stmt->execute();
    }

    $stmt->close();
}

// Function to send a message
function sendMessage($chat_id, $message) {
    $bot_token = "7232670812:AAGbC9uNE2Rgb-zbWFGbsPKWMYdnP6ZBgrs"; // Replace with your bot token
    $url = "https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$chat_id&text=" . urlencode($message);
    file_get_contents($url);
}
?>
