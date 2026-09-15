<?php
/*
Program description: Library main menu after user logs in, access to search and reserving books, view reserved books
Author: Keyvin Grand
*/

include 'header.php';

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

<div class="container mt-5" style="max-width: 600px;">

    <h2 class="mb-4">Library Menu</h2>

    <div class="list-group">
        <a href="search.php" class="list-group-item list-group-item-action">
            Search / List Books
        </a>
        <a href="my_reservations.php" class="list-group-item list-group-item-action">
            View My Reserved Books
        </a>
    </div>

</div>

</body>
</html>