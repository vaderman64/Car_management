<?php

session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Login User
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Authenticate User and store session data

    if ($username == 'admin' && $password == '1234') {
        $_SESSION['loggedin'] = true;
        $_SESSION['admin_id'] = session_id(); // Store the session ID
        header('Location: management.php');
        exit;
    } else {
        $error = "Invalid login credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Login</h2>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
</body>
</html>
