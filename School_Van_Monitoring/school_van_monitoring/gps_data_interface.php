<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Data Records</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>GPS Data Records</h1>
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

    // Query to select data from gps_data table
    $sql = "SELECT * FROM gps_data";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Display data in an HTML table
        echo "<table>";
        echo "<tr><th>ID</th><th>Latitude</th><th>Longitude</th><th>Timestamp</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["latitude"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["longitude"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["timestamp"]) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data found</p>";
    }

    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
