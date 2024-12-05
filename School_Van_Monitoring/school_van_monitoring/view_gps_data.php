<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Data Monitoring</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<?php
$servername = "localhost";  // Change if necessary
$username = "amirzaim"; // Change if necessary
$password = "1234"; // Change if necessary
$dbname = "school_van_monitoring";  // Change to your database name

// Create connection to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if POST request contains GPS data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve GPS data from POST request
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $satellites = $_POST['satellites'];
    $hdop = $_POST['hdop'];
    $altitude = $_POST['altitude'];

    // Prepare SQL statement to insert data into the database
    $sql = "INSERT INTO gps_data (latitude, longitude, satellites, hdop, altitude)
            VALUES ('$latitude', '$longitude', '$satellites', '$hdop', '$altitude')";

    if ($conn->query($sql) === TRUE) {
        echo "<h2>GPS Data Stored Successfully</h2>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Retrieve all GPS data to display in a table
$sql = "SELECT * FROM gps_data ORDER BY timestamp DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>Stored GPS Data</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Latitude</th><th>Longitude</th><th>Satellites</th><th>HDOP</th><th>Altitude (m)</th><th>Timestamp</th></tr>";
    
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['id'] . "</td>
                <td>" . $row['latitude'] . "</td>
                <td>" . $row['longitude'] . "</td>
                <td>" . $row['satellites'] . "</td>
                <td>" . $row['hdop'] . "</td>
                <td>" . $row['altitude'] . "</td>
                <td>" . $row['timestamp'] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<h2>No GPS Data Found</h2>";
}

$conn->close();
?>

</body>
</html>
