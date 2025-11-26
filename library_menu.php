<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

// block access if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">Library System</span>
        <span class="navbar-text">
            Logged in as <?php echo htmlspecialchars($username); ?> |
            <a href="logout.php" class="text-decoration-none text-light">Logout</a>
        </span>
    </div>
</nav>

<div class="container mt-5" style="max-width: 600px;">

    <h2 class="mb-4">Library Menu</h2>

    <div class="list-group">
        <a href="search.php" class="list-group-item list-group-item-action">
            Search / List Books
        </a>
        <a href="my_reservations.php" class="list-group-item list-group-item-action">
            View My Reserved Books
        </a>
        <a href="#" class="list-group-item list-group-item-action disabled">
            Reserve a Book (via search page)
        </a>
    </div>

</div>

</body>
</html>