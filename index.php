<?php

require 'db_connect.php';

$loginError = "";

// already logged in, go straight to menu
if (isset($_SESSION['username'])) {
    header("Location: library_menu.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $loginError = "Please enter username and password.";
    } else {

        $q = $pdo->prepare("SELECT username, password FROM users WHERE username = ?");
        $q->execute([$username]);
        $user = $q->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // TEMP DEBUG – to see what comes back
            // echo '<pre>'; var_dump($user); echo '</pre>'; exit;

            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                header("Location: library_menu.php");
                exit();
            } else {
                $loginError = "Incorrect username or password.";
            }
        } else {
            $loginError = "Incorrect username or password.";
        }
    }
}

$registered = isset($_GET['registered']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container" style="max-width: 500px; margin-top: 80px;">

    <h2 class="mb-4 text-center">Library Login</h2>

    <?php if ($registered) { ?>
        <div class="alert alert-success">
            Registration successful. You can log in now.
        </div>
    <?php } ?>

    <?php if (!empty($loginError)) { ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($loginError); ?>
        </div>
    <?php } ?>

    <form method="POST" action="index.php">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <p class="mt-3 text-center">
        No account? <a href="register.php">Register here</a>
    </p>
</div>

</body>
</html>
