<?php
/*
Program description: Reserve a book by ISBN if not already reserved, if successful redirect to 'My Reservations'
Author: Keyvin Grand
*/

//check if user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db_connect.php';

$username = $_SESSION['username'];

// check if ISBN is provided from search page
if (!isset($_GET['isbn'])) {
    header("Location: search.php");
    exit();
}

$isbn = $_GET['isbn'];

// check if book exists in 'books' table
$stmt = $pdo->prepare("SELECT isbn FROM books WHERE isbn = ?");
$stmt->execute([$isbn]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header("Location: search.php?error=book_not_found");
    exit();
}

// checks if book is already reserved
$check = $pdo->prepare("SELECT id FROM reserved_books WHERE isbn = ?");
$check->execute([$isbn]);

if ($check->fetch()) {
    // already taken
    header("Location: search.php?already_reserved=1");
    exit();
}

// else insert book into reservation
$insert = $pdo->prepare("INSERT INTO reserved_books (username, isbn, reservation_date)
                        VALUES (?, ?, CURDATE())");
$insert->execute([$username, $isbn]);

header("Location: my_reservations.php?reserved=1");
exit();