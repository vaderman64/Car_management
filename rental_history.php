<?php
// Make sure user is logged in
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

// Include Database Connection
include 'db.php';

$vin = $_GET['vin'];

// Fetch car details
$car_result = $conn->query("SELECT Make, Model, Year FROM cars WHERE VIN='$vin'");
$car = $car_result->fetch_assoc();

// Fetch rental history for the car
$history_result = $conn->query("SELECT DrivingLicense, DaysOfRent, TotalBill FROM rental_history WHERE VIN='$vin' ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rental History for <?= htmlspecialchars($car['Make']) ?> <?= htmlspecialchars($car['Model']) ?> (<?= htmlspecialchars($vin) ?>)</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Rental History for <?= htmlspecialchars($car['Make']) ?> <?= htmlspecialchars($car['Model']) ?> (<?= htmlspecialchars($vin) ?>)</h2>
    
    <table border="1">
        <tr>
            <th>Driver's License</th>
            <th>Days of Rent</th>
            <th>Total Bill</th>
        </tr>
        <?php while ($row = $history_result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['DrivingLicense']) ?></td>
                <td><?= htmlspecialchars($row['DaysOfRent']) ?></td>
                <td>$<?= htmlspecialchars(number_format($row['TotalBill'], 2)) ?></td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <a href="management.php">Back to Management Page</a>

</body>
</html>
<?php $conn->close(); ?>
