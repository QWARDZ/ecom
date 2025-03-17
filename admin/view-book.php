<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if book ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage-books.php");
    exit();
}

$bookId = $_GET['id'];
$conn = connectDB();

// Get book details
$sql = "SELECT * FROM books WHERE book_id = $bookId";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    // Book not found
    closeDB($conn);
    header("Location: manage-books.php");
    exit();
}

$book = $result->fetch_assoc();
closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Book - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
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
                    <h1 class="h2">Book Details</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="manage-books.php" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Back to Books
                        </a>
                        <a href="edit-book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Book
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <img src="../assets/images/books/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" class="img-fluid rounded mb-3" style="max-height: 300px;">
                                <div class="d-grid gap-2">
                                    <a href="edit-book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-primary">
                                        <i class="fas fa-edit me-1"></i> Edit Book
                                    </a>
                                    <a href="manage-books.php?delete=<?php echo $book['book_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book?');">
                                        <i class="fas fa-trash me-1"></i> Delete Book
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h3 class="mb-3"><?php echo $book['title']; ?></h3>

                                <div class="mb-3">
                                    <strong>Author:</strong> <?php echo $book['author']; ?>
                                </div>

                                <div class="mb-3">
                                    <strong>Category:</strong>
                                    <span class="badge bg-primary"><?php echo $book['category']; ?></span>
                                </div>

                                <div class="mb-3">
                                    <strong>Price:</strong>
                                    <span class="text-primary fw-bold">$<?php echo number_format($book['price'], 2); ?></span>
                                </div>

                                <div class="mb-3">
                                    <strong>Stock:</strong>
                                    <?php if ($book['stock'] > 10): ?>
                                        <span class="badge bg-success"><?php echo $book['stock']; ?> in stock</span>
                                    <?php elseif ($book['stock'] > 0): ?>
                                        <span class="badge bg-warning"><?php echo $book['stock']; ?> left in stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Out of stock</span>
                                    <?php endif; ?>
                                </div>

                                <?php if (isset($book['publication_date']) && !empty($book['publication_date'])): ?>
                                    <div class="mb-3">
                                        <strong>Publication Date:</strong>
                                        <?php echo date('F j, Y', strtotime($book['publication_date'])); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <strong>Added Date:</strong>
                                    <?php echo date('F j, Y', strtotime($book['added_date'])); ?>
                                </div>

                                <div class="mb-4">
                                    <strong>Description:</strong>
                                    <p class="mt-2"><?php echo nl2br($book['description']); ?></p>
                                </div>

                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Book Stats</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><i class="fas fa-shopping-cart me-2 text-primary"></i> Sales: Coming soon</p>
                                                <p><i class="fas fa-eye me-2 text-primary"></i> Views: Coming soon</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><i class="fas fa-star me-2 text-primary"></i> Rating: Coming soon</p>
                                                <p><i class="fas fa-comment me-2 text-primary"></i> Reviews: Coming soon</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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