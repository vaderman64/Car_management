<?php

// User Authentication
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

include 'db.php';

// Check if the session is valid
$session_id = $_SESSION['admin_id'] ?? null;
if (!$session_id || session_id() !== $session_id) {
    header('Location: logout.php'); // Log out if session ID doesn't match
    exit;
}

// Add a new car, fetching from add car form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_car'])) {
    $vin = $_POST['vin'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $rate = $_POST['rate'];
    $availability = isset($_POST['availability']) ? 'Available' : 'Not Available';

    $stmt = $conn->prepare("INSERT INTO cars (VIN, Make, Model, Year, RatePerDay, Availability) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdss", $vin, $make, $model, $year, $rate, $availability);
    $stmt->execute();
    $stmt->close();
}

// Fetch Filter Options

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$license_search = isset($_GET['license']) ? $_GET['license'] : '';

// Apply Filter options to list

$query = "SELECT * FROM cars";
if ($filter == 'available') {
    $query .= " WHERE Availability='Available'";
} elseif ($filter == 'occupied') {
    $query .= " WHERE Availability='Not Available'";
} elseif ($license_search != '') {
    $query .= " WHERE Availability='Not Available' AND VIN IN (SELECT VIN FROM rental_history WHERE DrivingLicense='$license_search')";
}

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Car Management</h2>
    <p>Session ID: <?= htmlspecialchars($session_id) ?></p>
    <a href="logout.php">Logout</a>
    
    <form method="get" action="">
        <label for="filter">Filter:</label>
        <select name="filter" id="filter">
            <option value="all">All</option>
            <option value="available">Available</option>
            <option value="occupied">Occupied</option>
        </select>
        <label for="license">Search by Driver's License:</label>
        <input type="text" id="license" name="license" value="<?= htmlspecialchars($license_search) ?>">
        <input type="submit" value="Apply Filter">
    </form>
    <table border="1">
        <tr>
            <th>VIN</th>
            <th>Make</th>
            <th>Model</th>
            <th>Year</th>
            <th>Rate Per Day</th>
            <th>Availability</th>
            <th>Action</th>
            <th>Driver's License</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <!-- Strips and displays car info by row -->
                <td><?= htmlspecialchars($row['VIN']) ?></td>
                <td><?= htmlspecialchars($row['Make']) ?></td>
                <td><?= htmlspecialchars($row['Model']) ?></td>
                <td><?= htmlspecialchars($row['Year']) ?></td>
                <td><?= htmlspecialchars($row['RatePerDay']) ?></td>
                <td><?= htmlspecialchars($row['Availability']) ?></td>
                <td>
                    <a href="rental_history.php?vin=<?= htmlspecialchars($row['VIN']) ?>">View Rental History</a> |
                    <?php if ($row['Availability'] == 'Available') { ?>
                        <a href="checkout.php?vin=<?= htmlspecialchars($row['VIN']) ?>">Check Out</a>
                    <?php } else { ?>
                        <a href="checkin.php?vin=<?= htmlspecialchars($row['VIN']) ?>">Check In</a>
                    <?php } ?>
                </td>
                <td>
                    <?php
                    $vin = $row['VIN'];
                    // Filter to only show drivers licences when applicable
                    if ($row['Availability'] == 'Not Available') {
                        $license_result = $conn->query("SELECT DrivingLicense FROM rental_history WHERE VIN='$vin' ORDER BY id DESC LIMIT 1");
                        $license_row = $license_result->fetch_assoc();
                        
                        if ($license_row && isset($license_row['DrivingLicense'])) {
                            echo htmlspecialchars($license_row['DrivingLicense']);
                        }
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
    <h3>Add New Car</h3>
    <!-- Add new car form -->
    <form method="post" action="">
        <label for="vin">VIN:</label>
        <input type="text" id="vin" name="vin" required><br>
        <label for="make">Make:</label>
        <input type="text" id="make" name="make" required><br>
        <label for="model">Model:</label>
        <input type="text" id="model" name="model" required><br>
        <label for="year">Year:</label>
        <input type="number" id="year" name="year" required><br>
        <label for="rate">Rate Per Day:</label>
        <input type="text" id="rate" name="rate" required><br>
        <label for="availability">Available:</label>
        <input type="checkbox" id="availability" name="availability"><br>
        <input type="submit" name="add_car" value="Add Car">
    </form>
</body>
</html>
<?php $conn->close(); ?>
