<?php
// Replace with your database credentials
$servername = "localhost";
$username = "amirzaim";
$password = "1234";
$dbname = "school_van_monitoring";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get latitude and longitude from GET request
$latitude = $_GET['latitude'];
$longitude = $_GET['longitude'];

// Fetch the coordinates from the database
$sql = "SELECT * FROM van_locations";
$result = $conn->query($sql);

// Check if any location matches
while($row = $result->fetch_assoc()) {
    // Compare received coordinates with stored coordinates
    if (abs($row['latitude'] - $latitude) < 0.0001 && abs($row['longitude'] - $longitude) < 0.0001) {
        // Coordinates match, send notification to user
        sendNotification($row['user_telegram_id']);
    }
}

$conn->close();

// Function to send a Telegram notification
function sendNotification($telegram_id) {
    $bot_token = "YOUR_BOT_TOKEN"; // Replace with your bot token
    $message = "Van has arrived!";
    $url = "https://api.telegram.org/bot$bot_token/sendMessage?chat_id=$telegram_id&text=$message";
    
    // Send the HTTP request to the Telegram API
    file_get_contents($url);
}
?>
