<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve GPS data from POST request
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $satellites = $_POST['satellites'];
    $hdop = $_POST['hdop'];
    $altitude = $_POST['altitude'];

    // Database connection
    $conn = new mysqli("localhost", "your_username", "your_password", "school_van_monitoring");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to insert data into the database
    $sql = "INSERT INTO gps_data (latitude, longitude, satellites, hdop, altitude)
            VALUES ('$latitude', '$longitude', '$satellites', '$hdop', '$altitude')";

    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    echo "No data received.";
}
?>
