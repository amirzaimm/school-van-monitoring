<?php
// Include database connection file
include('db_connection.php'); // Ensure this file exists and is correctly set up

// Check if the form is submitted to update user data
if (isset($_POST['submit'])) {
    $user_id = $_POST['user_id']; // Ensure this is passed from the form
    $telegram_id = $_POST['telegram_id'];
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    // Format the longitude and latitude values to two decimal places
    $home_longitude = number_format((float)$_POST['home_longitude'], 8, '.', '');
    $home_latitude = number_format((float)$_POST['home_latitude'], 8, '.', '');
    $school_longitude = number_format((float)$_POST['school_longitude'], 8, '.', '');
    $school_latitude = number_format((float)$_POST['school_latitude'], 8, '.', '');
    $address = $_POST['address'];
    $school_name = $_POST['school_name'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE users SET telegram_id=?, name=?, phone_number=?, home_longitude=?, home_latitude=?, school_longitude=?, school_latitude=?, address=?, school_name=? WHERE user_id=?");
    $stmt->bind_param("ssssdddsds", $telegram_id, $name, $phone_number, $home_longitude, $home_latitude, $school_longitude, $school_latitude, $address, $school_name, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('Record updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating record: " . $conn->error . "');</script>";
    }

    // Close the statement
    $stmt->close();
}

// SQL query to fetch user data from the database
$sql = "SELECT * FROM users"; // Replace 'users' with your actual table name
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    die("Error: " . $conn->error);
}

// Start HTML output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
    <meta http-equiv="refresh" content="10"> <!-- Auto-refresh every 10 seconds -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #ddd;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Information</h1>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Telegram ID</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Home Longitude</th>
                    <th>Home Latitude</th>
                    <th>School Longitude</th>
                    <th>School Latitude</th>
                    <th>Address</th>
                    <th>School Name</th>
                    <th>Edit</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch data and populate the table
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["user_id"]) . "</td>
                            <td>" . htmlspecialchars($row["telegram_id"]) . "</td>
                            <td>" . htmlspecialchars($row["name"]) . "</td>
                            <td>" . htmlspecialchars($row["phone_number"]) . "</td>
                            <td>" . htmlspecialchars(number_format((float)$row["home_longitude"], 8, '.', '')) . "</td>
                            <td>" . htmlspecialchars(number_format((float)$row["home_latitude"], 8, '.', '')) . "</td>
                            <td>" . htmlspecialchars(number_format((float)$row["school_longitude"], 8, '.', '')) . "</td>
                            <td>" . htmlspecialchars(number_format((float)$row["school_latitude"], 8, '.', '')) . "</td>
                            <td>" . htmlspecialchars($row["address"]) . "</td>
                            <td>" . htmlspecialchars($row["school_name"]) . "</td>
                            <td><a href='edit_user.php?user_id=" . htmlspecialchars($row["user_id"]) . "'>Edit</a></td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <?php
        // Fetch the current user data for pre-filling the form if user_id is passed
        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result_edit = $stmt->get_result();
            $row_edit = $result_edit->fetch_assoc();

            // Display the edit form
            echo '<h2>Edit User Information</h2>';
            echo '<form method="POST" action="">';
            echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($user_id) . '">';
            echo '<label for="telegram_id">Telegram ID:</label>';
            echo '<input type="text" name="telegram_id" value="' . htmlspecialchars($row_edit['telegram_id']) . '" required>';
            echo '<label for="name">Name:</label>';
            echo '<input type="text" name="name" value="' . htmlspecialchars($row_edit['name']) . '" required>';
            echo '<label for="phone_number">Phone Number:</label>';
            echo '<input type="text" name="phone_number" value="' . htmlspecialchars($row_edit['phone_number']) . '" required>';
            echo '<label for="home_longitude">Home Longitude:</label>';
            echo '<input type="text" name="home_longitude" value="' . htmlspecialchars(number_format((float)$row_edit['home_longitude'], 8, '.', '')) . '" required>';
            echo '<label for="home_latitude">Home Latitude:</label>';
            echo '<input type="text" name="home_latitude" value="' . htmlspecialchars(number_format((float)$row_edit['home_latitude'], 8, '.', '')) . '" required>';
            echo '<label for="school_longitude">School Longitude:</label>';
            echo '<input type="text" name="school_longitude" value="' . htmlspecialchars(number_format((float)$row_edit['school_longitude'], 8, '.', '')) . '" required>';
            echo '<label for="school_latitude">School Latitude:</label>';
            echo '<input type="text" name="school_latitude" value="' . htmlspecialchars(number_format((float)$row_edit['school_latitude'], 8, '.', '')) . '" required>';
            echo '<label for="address">Address:</label>';
            echo '<input type="text" name="address" value="' . htmlspecialchars($row_edit['address']) . '" required>';
            echo '<label for="school_name">School Name:</label>';
            echo '<input type="text" name="school_name" value="' . htmlspecialchars($row_edit['school_name']) . '" required>';
            echo '<input type="submit" name="submit" value="Update">';
            echo '</form>';
        }
        ?>
    </div>
</body>
</html>
