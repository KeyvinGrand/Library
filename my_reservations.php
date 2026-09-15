<?php
/*
Program description: Display user's reserved books with option to cancel
Author: Keyvin Grand
*/

include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$message = "";

// handle cancel action
if (isset($_GET['cancel'])) {
    $cancelId = (int)$_GET['cancel'];

    // check if this reservation belongs to THIS user
    $check = $pdo->prepare("SELECT id FROM reserved_books WHERE id = ? AND username = ?");
    $check->execute([$cancelId, $username]);

    if ($check->fetch()) {
        // delete reservation
        $del = $pdo->prepare("DELETE FROM reserved_books WHERE id = ?");
        $del->execute([$cancelId]);

        $message = "Reservation cancelled successfully.";
    } else {
        $message = "You cannot cancel this reservation.";
    }
}

// fetch all reservations for this user
$sql = "SELECT rb.id, rb.reservation_date, b.isbn, b.title, b.author
        FROM reserved_books rb
        JOIN books b ON rb.isbn = b.isbn
        WHERE rb.username = ?
        ORDER BY rb.reservation_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$username]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <h2 class="mb-3">My Reserved Books</h2>

    <?php if ($message !== "") { ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>

    <?php if (empty($reservations)) { ?>
        <div class="alert alert-warning">You have no current reservations.</div>
    <?php } else { ?>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Reserved On</th>
                <th>Cancel</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($reservations as $r) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['isbn']); ?></td>
                    <td><?php echo htmlspecialchars($r['title']); ?></td>
                    <td><?php echo htmlspecialchars($r['author']); ?></td>
                    <td><?php echo htmlspecialchars($r['reservation_date']); ?></td>
                    <td>
                        <!-- Cancel reservation link -->
                        <a href="my_reservations.php?cancel=<?php echo $r['id']; ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Cancel this reservation?');">
                        Cancel
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

    <?php } ?>

</div>
</body>
</html>