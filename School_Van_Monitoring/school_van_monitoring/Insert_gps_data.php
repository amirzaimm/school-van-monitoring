<?php
// Database credentials
$servername = "localhost";
$username = "amirzaim";  // Replace with your database username
$password = "1234";      // Replace with your database password
$dbname = "school_van_monitoring"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if GPS data is sent via GET
if (isset($_GET['latitude']) && isset($_GET['longitude'])) {
    $latitude = $_GET['latitude'];
    $longitude = $_GET['longitude'];
    $timestamp = date("Y-m-d H:i:s"); // Current timestamp

    // Prepared statement to insert data
    $stmt = $conn->prepare("INSERT INTO gps_data (latitude, longitude, timestamp) VALUES (?, ?, ?)");
    $stmt->bind_param("dds", $latitude, $longitude, $timestamp); // 'd' for double, 's' for string (timestamp)

    // Execute the statement
    if ($stmt->execute()) {
        echo "New GPS record created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No GPS data received.";
}

// SQL query to select all rows from the gps_data table
$sql = "SELECT id, latitude, longitude, timestamp FROM gps_data";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Data Display</title>
    <style>
        /* Styling for the page (your CSS) */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #4CAF50;
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .delete-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .reset-button {
            background-color: #FF9800;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
        }

        table td a {
            text-decoration: none;
        }
    </style>
</head>
<body>

<header>
    <h1>GPS Data Display</h1>
</header>

<div class="container">
    <!-- Display GPS Data Table -->
    <?php
    // Check if there are results from the query
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Latitude</th><th>Longitude</th><th>Timestamp</th><th>Actions</th></tr>";
        
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["latitude"] . "</td>
                    <td>" . $row["longitude"] . "</td>
                    <td>" . $row["timestamp"] . "</td>
                    <td>
                        <a href='Insert_gps_data.php?delete_id=" . $row["id"] . "'>
                            <button class='delete-button'>Delete</button>
                        </a>
                    </td>
                  </tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No results found.</p>";
    }
    ?>

    <!-- Reset All Button -->
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete all records?');">
        <button type="submit" name="reset_all" class="reset-button">Reset All Data</button>
    </form>
</div>

<footer>
    <p>&copy; 2024 School Van Monitoring System</p>
</footer>

<!-- JavaScript to automatically refresh the page every 10 seconds -->
<script>
    setInterval(function() {
        window.location.reload();  // Refreshes the page
    }, 10000); // 10000 milliseconds = 10 seconds
</script>

<?php
// Close the database connection
$conn->close();
?>

</body>
</html>
