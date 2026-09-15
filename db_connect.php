<?php
/*
Program description: Database connection setup
Author: Keyvin Grand
*/

$dsn = 'mysql:host=localhost;dbname=librarydb;charset=utf8mb4';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Database connection successful.";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>