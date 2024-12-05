<?php
// Database connection settings
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

// Query to fetch the data
$sql = "SELECT * FROM gps_data";
$result = $conn->query($sql);

// Check if there are any results
if ($result->num_rows > 0) {
    // Output data of each row
    echo "<table border='1'><tr><th>ID</th><th>Latitude</th><th>Longitude</th><th>Timestamp</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"]. "</td><td>" . $row["latitude"]. "</td><td>" . $row["longitude"]. "</td><td>" . $row["timestamp"]. "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No data found";
}

// Close connection
$conn->close();
?>
