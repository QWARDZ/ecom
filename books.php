<?php
session_start();
include 'config/database.php';
$conn = connectDB();

// Initialize variables
$books = [];
$pageTitle = "All Books";
$whereClause = "";
$params = [];
$paramTypes = "";

// Handle category filter
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $_GET['category'];
    $whereClause = "WHERE category = ?";
    $params[] = $category;
    $paramTypes .= "s";
    $pageTitle = $category . " Books";
}

// Handle search
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    if (empty($whereClause)) {
        $whereClause = "WHERE (title LIKE ? OR author LIKE ? OR description LIKE ?)";
    } else {
        $whereClause .= " AND (title LIKE ? OR author LIKE ? OR description LIKE ?)";
    }
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
    $paramTypes .= "sss";
    $pageTitle = "Search Results: " . $_GET['search'];
}

// Pagination setup
$booksPerPage = 9; // Number of books per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Count total books for pagination
$countSql = "SELECT COUNT(*) as total FROM books $whereClause";
$countStmt = $conn->prepare($countSql);

if (!empty($params)) {
    $countStmt->bind_param($paramTypes, ...$params);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$totalBooks = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalBooks / $booksPerPage);

// Calculate offset for pagination
$offset = ($page - 1) * $booksPerPage;

// Fetch books
$sql = "SELECT * FROM books $whereClause ORDER BY title LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Add pagination parameters
$params[] = $booksPerPage;
$params[] = $offset;
$paramTypes .= "ii"; // i for integer type

if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

// Fetch categories for sidebar
$sql = "SELECT DISTINCT category FROM books ORDER BY category";
$result = $conn->query($sql);
$categories = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
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
    <title><?php echo $pageTitle; ?> - Programming Books Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/user/headers.css" rel="stylesheet">
    <link href="assets/css/user/book.css" rel="stylesheet">
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>


    <!-- Books Section -->
    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Categories</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item <?php echo !isset($_GET['category']) ? 'active' : ''; ?>">
                                <a href="books.php" class="<?php echo !isset($_GET['category']) ? 'active' : ''; ?>">All Categories</a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li class="list-group-item <?php echo (isset($_GET['category']) && $_GET['category'] == $cat) ? 'active' : ''; ?>">
                                    <a href="books.php?category=<?php echo urlencode($cat); ?>" class="<?php echo (isset($_GET['category']) && $_GET['category'] == $cat) ? 'active' : ''; ?>">
                                        <?php echo $cat; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <?php if (empty($books)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No books found. Please try a different search or category.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($books as $book): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100 book-card" data-category="<?php echo $book['category']; ?>">
                                    <img src="assets/images/books/<?php echo $book['image']; ?>" class="card-img-top" alt="<?php echo $book['title']; ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $book['title']; ?></h5>
                                        <p class="card-text text-muted">By <?php echo $book['author']; ?></p>
                                        <p class="card-text"><?php echo substr($book['description'], 0, 100) . '...'; ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="h5 text-primary">$<?php echo $book['price']; ?></span>
                                            <a href="book-details.php?id=<?php echo $book['book_id']; ?>" class="btn btn-outline-primary">View Details</a>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i> <?php echo $book['category']; ?>
                                        </small>
                                        <?php if ($book['stock'] > 0): ?>
                                            <span class="badge bg-success float-end">In Stock</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger float-end">Out of Stock</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="books.php?page=<?php echo $page - 1; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="books.php?page=<?php echo $i; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="books.php?page=<?php echo $page + 1; ?><?php echo !empty($category) ? '&category=' . urlencode($category) : ''; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>

</html>