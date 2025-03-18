<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$conn = connectDB();

// Process delete action
$message = '';
$messageType = '';

if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $bookId = $_GET['delete'];

    // Check if book exists
    $sql = "SELECT * FROM books WHERE book_id = $bookId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Delete book
        $sql = "DELETE FROM books WHERE book_id = $bookId";

        if ($conn->query($sql) === TRUE) {
            $message = "Book deleted successfully!";
            $messageType = "success";
        } else {
            $message = "Error deleting book: " . $conn->error;
            $messageType = "danger";
        }
    } else {
        $message = "Book not found!";
        $messageType = "danger";
    }
}

// Get search query
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get category filter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 10;
$offset = ($page - 1) * $recordsPerPage;

// Build query
$sql = "SELECT * FROM books WHERE 1=1";
$countSql = "SELECT COUNT(*) as total FROM books WHERE 1=1";

// Add search filter
if (!empty($search)) {
    $sql .= " AND (title LIKE '%$search%' OR author LIKE '%$search%' OR description LIKE '%$search%')";
    $countSql .= " AND (title LIKE '%$search%' OR author LIKE '%$search%' OR description LIKE '%$search%')";
}

// Add category filter
if (!empty($category)) {
    $sql .= " AND category = '$category'";
    $countSql .= " AND category = '$category'";
}

// Add sorting and pagination
$sql .= " ORDER BY book_id DESC LIMIT $offset, $recordsPerPage";

// Execute queries
$result = $conn->query($sql);
$books = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

// Get total records for pagination
$countResult = $conn->query($countSql);
$totalRecords = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $recordsPerPage);

// Get all categories for filter
$categorySql = "SELECT DISTINCT category FROM books ORDER BY category";
$categoryResult = $conn->query($categorySql);
$categories = [];

if ($categoryResult->num_rows > 0) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/admin/sidebar.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Books</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add-book.php" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Add New Book
                        </a>
                    </div>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="d-flex">
                                    <input type="text" name="search" class="form-control me-2" placeholder="Search books..." value="<?php echo $search; ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="dropdown d-inline-block">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php echo empty($category) ? 'All Categories' : $category; ?>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="categoryDropdown">
                                        <li><a class="dropdown-item <?php echo empty($category) ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . (empty($search) ? '' : '?search=' . urlencode($search)); ?>">All Categories</a></li>
                                        <?php foreach ($categories as $cat): ?>
                                            <li><a class="dropdown-item <?php echo $category == $cat ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?category=' . urlencode($cat) . (empty($search) ? '' : '&search=' . urlencode($search)); ?>"><?php echo $cat; ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($books)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No books found</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($books as $book): ?>
                                            <tr>
                                                <td><?php echo $book['book_id']; ?></td>
                                                <td>
                                                    <img src="../assets/images/books/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" class="img-thumbnail" style="width: 50px;">
                                                </td>
                                                <td><?php echo $book['title']; ?></td>
                                                <td><?php echo $book['author']; ?></td>
                                                <td><span class="badge bg-primary"><?php echo $book['category']; ?></span></td>
                                                <td>$<?php echo number_format($book['price'], 2); ?></td>
                                                <td>
                                                    <?php if ($book['stock'] > 10): ?>
                                                        <span class="badge bg-success"><?php echo $book['stock']; ?></span>
                                                    <?php elseif ($book['stock'] > 0): ?>
                                                        <span class="badge bg-warning"><?php echo $book['stock']; ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger"><?php echo $book['stock']; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="view-book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit-book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?delete=' . $book['book_id']; ?>" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Are you sure you want to delete this book?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?page=' . ($page - 1) . (empty($category) ? '' : '&category=' . urlencode($category)) . (empty($search) ? '' : '&search=' . urlencode($search)); ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                            <a class="page-link" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?page=' . $i . (empty($category) ? '' : '&category=' . urlencode($category)) . (empty($search) ? '' : '&search=' . urlencode($search)); ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?page=' . ($page + 1) . (empty($category) ? '' : '&category=' . urlencode($category)) . (empty($search) ? '' : '&search=' . urlencode($search)); ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/script.js"></script>
</body>

</html>