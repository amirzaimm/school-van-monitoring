<?php
// Database connection
$servername = "localhost";
$username = "amirzaim"; // Adjust this if necessary
$password = "1234"; // Adjust this if necessary
$dbname = "school_van_monitoring"; // The database you are using

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data with default values
    $name = $_POST['name'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $address = $_POST['address'] ?? '';
    $telegram_id = $_POST['telegram_id'] ?? ''; // Capturing Telegram ID
    $school_address = $_POST['school_address'] ?? '';

    // Default values for home latitude and longitude (null for now)
    $home_latitude = NULL;
    $home_longitude = NULL;

    // Prepare SQL statement with placeholders
    $stmt = $conn->prepare("INSERT INTO users (name, phone_number, address, telegram_id, school_address, home_latitude, home_longitude) VALUES (?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters to placeholders
    $stmt->bind_param("sssssss", $name, $phone_number, $address, $telegram_id, $school_address, $home_latitude, $home_longitude);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        .form-container {
            max-width: 500px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="tel"]:focus,
        input[type="email"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Register User</h2>
        <form action="submit.php" method="post">
            <label for="name">Name</label>
            <input type="text" name="name" required>

            <label for="telegram_id">Telegram ID</label> <!-- Updated label for Telegram ID -->
            <input type="text" name="telegram_id" required> <!-- New field for Telegram ID -->

            <label for="phone_number">Phone Number</label>
            <input type="tel" name="phone_number" required>

            <label for="address">Home Address</label>
            <input type="text" name="address" required>

            <label for="school_address">School Name</label>
            <input type="text" name="school_address" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
