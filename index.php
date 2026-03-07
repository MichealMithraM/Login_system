<?php
session_start();
require_once "config/db.php";

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $loginInput = htmlspecialchars(trim($_POST['login_input'] ?? ''));
    $password   = $_POST['password'] ?? '';

    if ($loginInput === "" || $password === "") {
        $errorMessage = "Please enter login details";
    } else {
        
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username=? OR email=?");
        mysqli_stmt_bind_param($stmt, "ss", $loginInput, $loginInput);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
               
                session_regenerate_id(true);
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $errorMessage = "Incorrect password";
            }
        } else {
            $errorMessage = "User not found";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="login-box">

<h2>Login</h2>

<form method="POST">
<input type="text" name="login_input" placeholder="Email or Username" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit" name="login">Login</button>
<p class="error"><?php echo $errorMessage; ?></p>
<p>Don't have account? <a href="signup.php">Signup</a></p>
</form>

</div>

</body>
</html>