<?php
session_start();
include("config/db.php"); 
if(!isset($_SESSION['username'])){
header("Location: index.php");
exit;
}

$message = "";
if(isset($_POST['submit'])){
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    if(!empty($title) && !empty($content)){
        $sql = "INSERT INTO blogs (title, content) VALUES ('$title', '$content')";
        if($conn->query($sql)){
            $message = "Blog inserted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Please fill in all fields.";
    }
}

if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM blogs WHERE id = $id");
    header("Location: blog.php");
    exit;
}

$blogs = [];
$res = $conn->query("SELECT * FROM blogs ORDER BY id DESC");
if($res){
    while($row = $res->fetch_assoc()){
        $blogs[] = $row;
    }
}
$editId = 0;
$editTitle = "";
$editContent = "";
if(isset($_GET['edit'])){
    $editId = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM blogs WHERE id=$editId");
    if($res && $res->num_rows > 0){
        $row = $res->fetch_assoc();
        $editTitle = $row['title'];
        $editContent = $row['content'];
    }
}
if(isset($_POST['update'])){
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    if(!empty($title) && !empty($content)){
        $conn->query("UPDATE blogs SET title='$title', content='$content' WHERE id=$id");
        $message = "Blog updated successfully!";
        $editId = 0;
        $editTitle = "";
        $editContent = "";
    } else {
        $message = "Please fill in all fields.";
    }
}
$viewTitle = "";
$viewContent = "";
if(isset($_GET['view'])){
    $viewId = intval($_GET['view']);
    $res = $conn->query("SELECT * FROM blogs WHERE id=$viewId");
    if($res && $res->num_rows > 0){
        $row = $res->fetch_assoc();
        $viewTitle = $row['title'];
        $viewContent = $row['content'];
    }
}
$filteredBlogs = $blogs;
if(isset($_GET['search']) && trim($_GET['search']) !== ""){
    $keyword = strtolower(trim($_GET['search']));
    $filteredBlogs = array_filter($blogs, function($b) use($keyword){
        return strpos(strtolower($b['title']), $keyword) !== false;
    });
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Blog</title>
<link rel="stylesheet" href="dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <h1>Manage Blogs</h1>

        <div class="manage-container">
            <div class="table-container two-container">
                <div class="search-bar">
                    <div class="search-bar">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by title" 
       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
       <button type="submit">Search</button>
    </form>
</div>
                </div>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    $sn = 1;
                    foreach($filteredBlogs as $blog){ ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td><?php echo htmlspecialchars($blog['title']); ?></td>
                           <td class="action-icons">
    <a href="blog.php?view=<?php echo $blog['id']; ?>" title="View Blog">
        <i class="fa-solid fa-eye"></i>
    </a>
    <a href="blog.php?edit=<?php echo $blog['id']; ?>" title="Edit Blog">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
    <a href="blog.php?delete=<?php echo $blog['id']; ?>" 
       onclick="return confirm('Are you sure you want to delete this blog?');" 
       class="delete" title="Delete Blog">
        <i class="fa-solid fa-trash"></i>
    </a>
</td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="form-container two-container">
    <?php if($viewTitle): ?>
        <div class="view-container" style="margin-top:20px; padding:15px; border:1px solid #ccc; border-radius:5px; background:#f9f9f9;">
            <h3><?php echo htmlspecialchars($viewTitle); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($viewContent)); ?></p>
            <a href="blog.php" class="btn" style="background:#888; text-decoration:none;">Close</a>
        </div>
    <?php else: ?>
        <h2><?php echo $editId ? "Edit Blog" : "Insert New Blog"; ?></h2>
        <form method="post">
            <?php if($editId): ?>
                <input type="hidden" name="id" value="<?php echo $editId; ?>">
            <?php endif; ?>

            <input type="text" name="title" placeholder="Title" 
                   value="<?php echo htmlspecialchars($editTitle); ?>" required
                   style="width:100%;padding:10px;margin-bottom:10px;border:1px solid #ccc;border-radius:5px;">

            <textarea name="content" placeholder="Content" rows="6" required
                      style="width:100%;padding:10px;margin-bottom:10px;border:1px solid #ccc;border-radius:5px;"><?php echo htmlspecialchars($editContent); ?></textarea>

            <?php if($editId): ?>
                <button type="submit" name="update" class="btn">Update Blog</button>
                <a href="blog.php" class="btn" style="background:#888; text-decoration:none;">Cancel</a>
            <?php else: ?>
                <button type="submit" name="submit" class="btn">Insert Blog</button>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>
        </div>
    </div>
</main>
<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}
function loadBlog(id, type) {
    fetch('get_blog.php?id='+id+'&type='+type)
    .then(res => res.json())
    .then(data => {
        if(type === 'edit'){
            document.querySelector('input[name="title"]').value = data.title;
            document.querySelector('textarea[name="content"]').value = data.content;
            document.querySelector('form button[name="update"]').style.display = 'inline-block';
            document.querySelector('form button[name="submit"]').style.display = 'none';
        }
        if(type === 'view'){
            document.querySelector('.view-container').innerHTML = `<h3>${data.title}</h3><p>${data.content}</p>`;
        }
    });
}
</script>
</body>
</html>