<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "amirzaim"; // XAMPP username
$password = "1234"; // Password for XAMPP
$database = "school_van_monitoring"; // Your database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input data
    $name = $_POST['name'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $address = $_POST['address'] ?? '';
    $school_name = $_POST['school_name'] ?? '';
    $telegram_id = $_POST['telegram_id'] ?? '';

    // Basic validation
    if (empty($name) || empty($phone_number) || empty($address) || empty($school_name) || empty($telegram_id)) {
        header("Location: user_form.php?error=All fields are required&name=" . urlencode($name) . "&phone_number=" . urlencode($phone_number) . "&address=" . urlencode($address) . "&school_name=" . urlencode($school_name) . "&telegram_id=" . urlencode($telegram_id));
        exit();
    }

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("INSERT INTO users (name, phone_number, address, school_name, telegram_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $phone_number, $address, $school_name, $telegram_id);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: user_form.php?success=Registration successful!");
        exit();
    } else {
        header("Location: user_form.php?error=Error: " . $stmt->error);
        exit();
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
    <title>User Registration</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <form action="user_form.php" method="post">
        <h2>School Van Monitoring</h2>

        <!-- Error or success message handling -->
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php } ?>

        <?php if (isset($_GET['success'])) { ?>
            <p class="success"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php } ?>

        <!-- Name Field -->
        <label for="name">Name</label>
        <input type="text" name="name" placeholder="Name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>" required><br>

        <!-- Telegram ID Field -->
        <label for="telegram_id">Telegram ID</label>
        <input type="text" name="telegram_id" placeholder="Telegram ID" value="<?php echo isset($_GET['telegram_id']) ? htmlspecialchars($_GET['telegram_id']) : ''; ?>" required><br>

        <!-- Phone Number Field -->
        <label for="phone_number">Phone Number</label>
        <input type="tel" name="phone_number" placeholder="Phone Number" value="<?php echo isset($_GET['phone_number']) ? htmlspecialchars($_GET['phone_number']) : ''; ?>" required><br>

        <!-- Address Field -->
        <label for="address">Address</label>
        <textarea name="address" placeholder="Enter your address" required><?php echo isset($_GET['address']) ? htmlspecialchars($_GET['address']) : ''; ?></textarea><br>

        <!-- School Name Field -->
        <label for="school_name">School Name</label>
        <input type="text" name="school_name" placeholder="School Name" value="<?php echo isset($_GET['school_name']) ? htmlspecialchars($_GET['school_name']) : ''; ?>" required><br>

        <!-- Submit Button -->
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
