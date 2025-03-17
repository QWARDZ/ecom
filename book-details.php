<?php
session_start();
include 'config/database.php';
$conn = connectDB();

// Get book ID
$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($book_id <= 0) {
    header('Location: books.php');
    exit;
}

// Fetch book details
$sql = "SELECT * FROM books WHERE book_id = $book_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header('Location: books.php');
    exit;
}

$book = $result->fetch_assoc();
$pageTitle = $book['title'];

// Fetch related books
$sql = "SELECT * FROM books WHERE category = '" . $conn->real_escape_string($book['category']) . "' AND book_id != $book_id LIMIT 3";
$result = $conn->query($sql);
$relatedBooks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $relatedBooks[] = $row;
    }
}

// Process add to cart
$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if user is not logged in
        header("Location: login.php?redirect=book-details.php?id=$book_id");
        exit();
    }

    $userId = $_SESSION['user_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Check if book is already in cart
    $sql = "SELECT * FROM cart WHERE user_id = $userId AND book_id = $book_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Update quantity
        $cartItem = $result->fetch_assoc();
        $newQuantity = $cartItem['quantity'] + $quantity;

        $sql = "UPDATE cart SET quantity = $newQuantity WHERE cart_id = " . $cartItem['cart_id'];

        if ($conn->query($sql) === TRUE) {
            $message = "Book quantity updated in your cart!";
            $messageType = "success";
        } else {
            $message = "Error updating cart: " . $conn->error;
            $messageType = "danger";
        }
    } else {
        // Add new item to cart
        $sql = "INSERT INTO cart (user_id, book_id, quantity) VALUES ($userId, $book_id, $quantity)";

        if ($conn->query($sql) === TRUE) {
            $message = "Book added to your cart!";
            $messageType = "success";
        } else {
            $message = "Error adding to cart: " . $conn->error;
            $messageType = "danger";
        }
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book['title']; ?> - Programming Books Store</title>
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
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1><?php echo $book['title']; ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item"><a href="books.php">Books</a></li>
                            <li class="breadcrumb-item"><a href="books.php?category=<?php echo urlencode($book['category']); ?>"><?php echo $book['category']; ?></a></li>
                            <li class="breadcrumb-item active"><?php echo $book['title']; ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Book Details Section -->
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="book-image">
                    <img src="assets/images/books/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" class="img-fluid">
                </div>
            </div>
            <div class="col-md-7">
                <div class="book-details">
                    <h2><?php echo $book['title']; ?></h2>
                    <p class="text-muted">By <?php echo $book['author']; ?></p>

                    <div class="book-meta">
                        <span><i class="fas fa-bookmark"></i> <?php echo $book['category']; ?></span>
                        <span><i class="fas fa-calendar-alt"></i> Published: <?php echo date('M Y', strtotime($book['published_date'])); ?></span>
                        <span><i class="fas fa-file-alt"></i> <?php echo $book['pages']; ?> pages</span>
                    </div>

                    <div class="book-price">
                        $<?php echo $book['price']; ?>
                    </div>

                    <p><?php echo $book['description']; ?></p>

                    <div class="d-grid gap-2 d-md-block mt-4">
                        <a href="cart.php?action=add&id=<?php echo $book['book_id']; ?>" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                        </a>
                        <a href="#" class="btn btn-outline-primary ms-md-2">
                            <i class="far fa-heart me-2"></i> Add to Wishlist
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Books Section -->
        <?php if (!empty($relatedBooks)): ?>
            <div class="mt-5">
                <h3 class="section-heading">Related Books</h3>
                <div class="row">
                    <?php foreach ($relatedBooks as $relatedBook): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <img src="assets/images/books/<?php echo $relatedBook['image']; ?>" class="card-img-top" alt="<?php echo $relatedBook['title']; ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $relatedBook['title']; ?></h5>
                                    <p class="card-text text-muted">By <?php echo $relatedBook['author']; ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="h5 text-primary">$<?php echo $relatedBook['price']; ?></span>
                                        <a href="book-details.php?id=<?php echo $relatedBook['book_id']; ?>" class="btn btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>