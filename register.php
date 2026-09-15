<?php
/*
Program description: User registration page, each user must have unique username
Author: Keyvin Grand
*/

include 'header.php';
include 'db_connect.php';

$errors = [];

//form submission and validation
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

    // Check username uniqueness
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT username FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Username already taken.';
        }
    }

    // Insert user into database
    if (count($errors) == 0) {
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);

        $insert = $pdo->prepare("INSERT INTO users (username, password, fullname, phone)
                                 VALUES (?, ?, ?, ?)");
        $insert->execute([$username, $hashedPass, $fullname, $phone]);

        header("Location: index.php?registered=1");
        exit();
    }
}
?>
<div class="container mt-5" style="max-width: 600px;">

    <h2 class="mb-4">Register</h2>
    
    <?php if (!empty($errors)) { ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $e) { echo "<p>$e</p>"; } ?>
        </div>
    <?php } ?>

    <form action="register.php" method="POST">

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control">
        </div>

        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="fullname" class="form-control">
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Sign Up</button>

    </form>

    <p class="mt-3">
        Already have an account? <a href="index.php">Login</a>
    </p>

</div>