<?php
session_start();

if(!isset($_SESSION['username'])){
header("Location: index.php");
exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="dashboard.css">

</head>

<body>


<header class="header">
    <div class="logo">
        <span>Admin Panel</span>
    </div>

    <div class="mobile-toggle" onclick="toggleSidebar()">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>

    <div class="user-info">
        <h1>Welcome <b><?php echo $_SESSION['username']; ?>!</b></h1>
    </div>
</header>


  
    <aside class="sidebar" id="sidebar">
        <a href="dashboard.php" class="sidebar-link">Dashboard</a>
        
        <a href="blog.php" class="sidebar-link">Blog</a>
        <a href="logout.php" class="sidebar-link logout">Logout</a>
    </aside>

    <main class="main">
        <div class="dashboard-card">
            <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <p>This is your blog admin panel. You can insert, view, and manage all your blog posts from here.</p>

            <div class="buttons">
                <a href="blog.php" class="btn">New Blog</a>
                <a href="logout.php" class="btn logout-btn">Logout</a>
            </div>
        </div>
    </main>
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}
</script>
</body>
</html>