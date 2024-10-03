<?php

// Authentication

session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit;
}

// Database connection
include 'db.php';

// Updates car as being checked out by drivers licence id

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vin = $_POST['vin'];
    $license = $_POST['license'];

    $stmt = $conn->prepare("UPDATE cars SET Availability='Not Available' WHERE VIN=?");
    $stmt->bind_param("s", $vin);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO rental_history (VIN, DrivingLicense) VALUES (?, ?)");
    $stmt->bind_param("ss", $vin, $license);
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
    <title>Check Out Car</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Check Out Car</h2>
    <form method="post" action="">
        <input type="hidden" name="vin" value="<?= $vin ?>">
        <label for="license">Driver's License:</label>
        <input type="text" id="license" name="license" required><br>
        <input type="submit" value="Check Out">
    </form>
    <a href="management.php">Back to Management Page</a>
</body>
</html>
<?php $conn->close(); ?>
