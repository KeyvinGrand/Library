<?php
/*
Program description: Search and list books with reservation option 
Author: Keyvin Grand
*/

include 'header.php';
include 'db_connect.php';

//check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}


$username = $_SESSION['username'];

// search filters
$q = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';

// pagination
$perPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

// build WHERE clause
$where = [];
$params = [];

if ($q !== '') {
    $where[] = "(b.title LIKE ? OR b.author LIKE ?)";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
}

if ($category !== '' && ctype_digit($category)) {
    $where[] = "b.category_code = ?";
    $params[] = (int)$category;
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

// get total rows for pagination
$sqlCount = "SELECT COUNT(*) FROM books b $whereSql";
$stmtCount = $pdo->prepare($sqlCount);
$stmtCount->execute($params);
$totalRows = (int)$stmtCount->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

// get categories for dropdown
$catStmt = $pdo->query("SELECT category_code, description FROM categories ORDER BY description");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// get actual books page
$sqlBooks = "SELECT b.isbn, b.title, b.author, c.description AS category,
    (SELECT COUNT(*) FROM reserved_books rb WHERE rb.isbn = b.isbn) AS reserved_count
    FROM books b
    JOIN categories c ON b.category_code = c.category_code
    $whereSql
    ORDER BY b.title
    LIMIT $perPage OFFSET $offset";

$stmtBooks = $pdo->prepare($sqlBooks);
$stmtBooks->execute($params);
$books = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

    <h2 class="mb-3">Search / List Books</h2>

    <form method="GET" action="search.php" class="row g-3 mb-4">
        <div class="col-md-5">
            <label class="form-label">Title or Author</label>
            <input type="text" name="q" class="form-control"
                   value="<?php echo htmlspecialchars($q); ?>">
        </div>
        <!-- Category dropdown menu -->
        <div class="col-md-4">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option value="">All</option>
                <?php foreach ($categories as $cat) { ?>
                    <option value="<?php echo $cat['category_code']; ?>"
                        <?php if ($category !== '' && (int)$category === (int)$cat['category_code']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['description']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <?php if ($totalRows === 0) { ?>
        <div class="alert alert-info">No books found.</div>
    <?php } else { ?>

        <table class="table table-striped">
            <thead>
            <tr>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($books as $b) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($b['isbn']); ?></td>
                    <td><?php echo htmlspecialchars($b['title']); ?></td>
                    <td><?php echo htmlspecialchars($b['author']); ?></td>
                    <td><?php echo htmlspecialchars($b['category']); ?></td>
                    <td>
                        <?php if ((int)$b['reserved_count'] === 0) { ?>
                            <a href="reserve.php?isbn=<?php echo urlencode($b['isbn']); ?>"
                               class="btn btn-sm btn-success">
                                Reserve
                            </a>
                        <?php } else { ?>
                            <button class="btn btn-sm btn-secondary" disabled>Reserved</button>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <!-- Pages of books -->
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <?php
                $queryParams = $_GET;
                for ($p = 1; $p <= $totalPages; $p++) {
                    $queryParams['page'] = $p;
                    $link = 'search.php?' . http_build_query($queryParams);
                    ?>
                    <li class="page-item <?php if ($p === $page) echo 'active'; ?>">
                        <a class="page-link" href="<?php echo $link; ?>">
                            <?php echo $p; ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </nav>

    <?php } ?>

</div>
</body>
</html>
