<?php

// Authentication
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

// Database connection
include 'db.php';

// Update car in database, with rental information and update that car is now available

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vin = $_POST['vin'];
    $days = $_POST['days'];

    $rate_result = $conn->query("SELECT RatePerDay FROM cars WHERE VIN='$vin'");
    $rate_row = $rate_result->fetch_assoc();
    $rate_per_day = $rate_row['RatePerDay'];
    $total_bill = $days * $rate_per_day;

    $stmt = $conn->prepare("UPDATE cars SET Availability='Available' WHERE VIN=?");
    $stmt->bind_param("s", $vin);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE rental_history SET DaysOfRent=?, TotalBill=? WHERE VIN=? AND DaysOfRent IS NULL");
    $stmt->bind_param("ids", $days, $total_bill, $vin);
    $stmt->execute();

    header('Location: management.php');
    exit;
}

$vin = $_GET['vin'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check In Car</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Check In Car</h2>
    <form method="post" action="">
        <input type="hidden" name="vin" value="<?= $vin ?>">
        <label for="days">Days of Rent:</label>
        <input type="number" id="days" name="days" required><br>
        <input type="submit" value="Check In">
    </form>
    <a href="management.php">Back to Management Page</a>
</body>
</html>
<?php $conn->close(); ?>
