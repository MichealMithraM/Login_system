<?php
session_start();

if(!isset($_SESSION['username'])){
header("Location: index.php");
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="dashboard">

<h1>Welcome <b><?php echo $_SESSION['username']; ?>!</b></h1>

<p>You are successfully logged in</p>
<p>Are you Like to see my previous work?</p>
<a href="https://www.kalpakaorganics.com" class="btn">View Previous Work</a>
<br>
<a href="logout.php" class="btn">Logout</a>

</div>

</body>
</html>