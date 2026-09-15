<?php
/*
Program description: Included on all pages for consistent navbar and session handling
Author: Keyvin Grand
*/ 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$username = $_SESSION['username'] ?? null;

if (!isset($pageTitle)) {
    $pageTitle = "Library System";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="library_menu.php">Library System</a>

        <?php if ($username) { ?>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Logged in as <?php echo htmlspecialchars($username); ?>
                </span>
                <a href="search.php" class="btn btn-light btn-sm me-2">Search</a>
                <a href="my_reservations.php" class="btn btn-light btn-sm me-2">My Reservations</a>
                <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        <?php } ?>
    </div>
</nav>