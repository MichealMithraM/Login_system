<?php
session_start();
require_once "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email    = htmlspecialchars(trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($username === "" || $email === "" || $password === "") {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username=? OR email=?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $message = "Username or Email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insertStmt, "sss", $username, $email, $hashedPassword);

            if (mysqli_stmt_execute($insertStmt)) {
                $message = "Account created successfully! You can login now.";
            } else {
                $message = "Something went wrong. Please try again.";
            }

            mysqli_stmt_close($insertStmt);
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Signup</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-box">

<h2>Create Account</h2>

<form method="POST">
    <input type="text" name="username" placeholder="Enter Username" required>
    <input type="email" name="email" placeholder="Enter Email" required>
    <input type="password" name="password" placeholder="Enter Password" required>
    <button type="submit">Signup</button>
    <p class="error"><?php echo $message; ?></p>
    <p>Already have an account? <a href="index.php">Login</a></p>
</form>

</div>

</body>
</html>