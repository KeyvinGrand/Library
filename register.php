<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $fullname = trim($_POST['fullname'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validation
    if ($username === '' || $fullname === '' || $phone === '' || $password === '' || $confirm === '') {
        $errors[] = 'All fields are required.';
    }

    if (!ctype_digit($phone) || strlen($phone) !== 10) {
        $errors[] = 'Phone must be exactly 10 digits.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    // Check unique username
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT username FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Username already taken.';
        }
    }

    // Insert user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password, fullname, phone) VALUES (?, ?, ?, ?)');
        $stmt->execute([$username, $hashed, $fullname, $phone]);
        header('Location: index.php?registered=1');
        exit;
    }
}
?>