<!DOCTYPE html>
<html>
<body>

<?php

// Database configuration
$dbname = 'school_van_monitoring'; // Use your actual database name
$dbuser = 'root';                   // Your MySQL username
$dbpass = '';                       // Your MySQL password
$dbhost = 'localhost';              // Database host

// Create connection
$connect = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Check connection
if (!$connect) {
    echo "Error: " . mysqli_connect_error();
    exit();
}

// Check if latitude and longitude are set in the GET parameters
if (isset($_GET["latitude"]) && isset($_GET["longitude"])) {
    // Retrieve GPS data from GET parameters
    $latitude = $_GET["latitude"];
    $longitude = $_GET["longitude"];

    // Sanitize the input data to prevent SQL injection
    $latitude = mysqli_real_escape_string($connect, $latitude);
    $longitude = mysqli_real_escape_string($connect, $longitude);

    // SQL query to insert data into the location_storage table
    $query = "INSERT INTO location_storage (latitude, longitude) VALUES ('$latitude', '$longitude')";
    $result = mysqli_query($connect, $query);

    // Check if the insertion was successful
    if ($result) {
        echo "Insertion Success!<br>";
    } else {
        echo "Error: " . mysqli_error($connect) . "<br>";
    }
} else {
    // Debugging: Display the GET parameters
    echo "Error: Latitude and Longitude parameters are required.<br>";
    echo "Received parameters: ";
    print_r($_GET); // Output the contents of the GET array
}

// Close the database connection
mysqli_close($connect);

?>

<h2>Stored GPS Data</h2>
<table border="1">
    <tr>
        <th>Latitude</th>
        <th>Longitude</th>
    </tr>
    <?php
    // Reconnect to the database to fetch stored data
    $connect = @mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    $query = "SELECT * FROM location_storage";
    $result = mysqli_query($connect, $query);

    // Fetch and display the stored data
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>" . $row['latitude'] . "</td><td>" . $row['longitude'] . "</td></tr>";
    }

    // Close the database connection
    mysqli_close($connect);
    ?>
</table>

</body>
</html>
