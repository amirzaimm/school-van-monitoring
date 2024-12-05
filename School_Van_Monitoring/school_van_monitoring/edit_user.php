<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection file
include('db_connection.php'); // Ensure this file exists and is correctly set up

// Get user ID from the URL
$user_id = $_GET['user_id'] ?? null;

// Initialize variables
$name = $telegram_id = $phone_number = $address = $school_name = "";
$home_longitude = $home_latitude = $school_longitude = $school_latitude = "";

// Fetch user data based on the user ID
if ($user_id) {
    $sql = "SELECT * FROM users WHERE user_id = ?"; 
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $name = $user['name'];
        $telegram_id = $user['telegram_id'];
        $phone_number = $user['phone_number'];
        $address = $user['address'];
        $school_name = $user['school_name'];

        // Set default values for longitude and latitude if they are null
        $home_longitude = $user['home_longitude'] ?? 0.0;
        $home_latitude = $user['home_latitude'] ?? 0.0;
        $school_longitude = $user['school_longitude'] ?? 0.0;
        $school_latitude = $user['school_latitude'] ?? 0.0;

        // Format the longitude and latitude values to 8 decimal places
        $home_longitude = number_format($home_longitude, 8, '.', '');
        $home_latitude = number_format($home_latitude, 8, '.', '');
        $school_longitude = number_format($school_longitude, 8, '.', '');
        $school_latitude = number_format($school_latitude, 8, '.', '');
    } else {
        echo "User not found.";
        exit();
    }
} else {
    echo "No user ID specified.";
    exit();
}

// Update the user data if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $name = $_POST['name'] ?? '';
    $telegram_id = $_POST['telegram_id'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $address = $_POST['address'] ?? '';
    $school_name = $_POST['school_name'] ?? '';
    $home_longitude = $_POST['home_longitude'] ?? '';
    $home_latitude = $_POST['home_latitude'] ?? '';
    $school_longitude = $_POST['school_longitude'] ?? '';
    $school_latitude = $_POST['school_latitude'] ?? '';

    // Basic validation
    if (empty($name) || empty($telegram_id) || empty($phone_number) || empty($address) || empty($school_name) ||
        empty($home_longitude) || empty($home_latitude) || empty($school_longitude) || empty($school_latitude)) {
        $error = "All fields are required.";
    } else {
        // Prepare and bind the SQL statement
        $update_sql = "UPDATE users SET name = ?, telegram_id = ?, phone_number = ?, address = ?, school_name = ?, home_longitude = ?, home_latitude = ?, school_longitude = ?, school_latitude = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssssssi", $name, $telegram_id, $phone_number, $address, $school_name, $home_longitude, $home_latitude, $school_longitude, $school_latitude, $user_id);

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: view_data.php?success=User updated successfully!");
            exit();
        } else {
            $error = "Error updating user: " . $stmt->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link your CSS file here -->
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container label {
            display: block;
            margin: 15px 0 5px;
        }
        .form-container input[type="text"],
        .form-container input[type="tel"],
        .form-container textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-container button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit User</h2>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="edit_user.php?user_id=<?php echo htmlspecialchars($user_id); ?>" method="post">
            <label for="name">Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label for="telegram_id">Telegram ID</label>
            <input type="text" name="telegram_id" value="<?php echo htmlspecialchars($telegram_id); ?>" required>

            <label for="phone_number">Phone Number</label>
            <input type="tel" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>

            <label for="address">Address</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>" required>

            <label for="school_name">School Name</label>
            <input type="text" name="school_name" value="<?php echo htmlspecialchars($school_name); ?>" required>

            <!-- New fields for home and school latitude/longitude -->
            <label for="home_longitude">Home Longitude</label>
            <input type="text" name="home_longitude" value="<?php echo htmlspecialchars($home_longitude); ?>" required>

            <label for="home_latitude">Home Latitude</label>
            <input type="text" name="home_latitude" value="<?php echo htmlspecialchars($home_latitude); ?>" required>

            <label for="school_longitude">School Longitude</label>
            <input type="text" name="school_longitude" value="<?php echo htmlspecialchars($school_longitude); ?>" required>

            <label for="school_latitude">School Latitude</label>
            <input type="text" name="school_latitude" value="<?php echo htmlspecialchars($school_latitude); ?>" required>

            <button type="submit">Update User</button>
        </form>
    </div>
</body>
</html>
