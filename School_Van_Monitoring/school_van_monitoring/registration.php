<?php
// Telegram Bot API token
$token = 'Y7232670812:AAGbC9uNE2Rgb-zbWFGbsPKWMYdnP6ZBgrs
';

// Define the URL for your webhook or the method to receive updates
$webhookUrl = "https://api.telegram.org/bot$token/getUpdates";

// Fetch updates from Telegram
$response = file_get_contents($webhookUrl);
$updates = json_decode($response, true);

// Check for new updates
if (isset($updates['result'])) {
    foreach ($updates['result'] as $update) {
        $chat_id = $update['message']['chat']['id'];
        $text = $update['message']['text'];

        // Handle /start command
        if ($text === '/start') {
            $message = "Welcome! Click the link to register: [Register Here](http://localhost/school_van_monitoring/submit.php)";
            sendMessage($chat_id, $message);
        }
    }
}

// Function to send messages
function sendMessage($chat_id, $message) {
    global $token;
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown' // Allows you to use Markdown for formatting
    ];

    // Use http_build_query to format the data
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    file_get_contents($url, false, $context);
}
?>
