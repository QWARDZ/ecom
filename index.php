<?php
session_start();
include 'config/database.php';
$conn = connectDB();

// Fetch featured books
$sql = "SELECT * FROM books ORDER BY added_date DESC LIMIT 3";
$result = $conn->query($sql);
$featuredBooks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $featuredBooks[] = $row;
    }
}

// Fetch book categories
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
    <title>Programming Books Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/user/headers.css" rel="stylesheet">
    <!-- <link href="assets/css/footer.css" rel="stylesheet"> -->
    <link href="assets/css/user/user_index.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <div class="container-fluid hero-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold ">Programming Books for Every Developer</h1>
                    <p class="lead">Discover the best programming books to enhance your coding skills and stay ahead in the tech world.</p>
                    <a href="books.php" class="btn btn-primary btn-lg">Browse Books</a>
                </div>
                <div class="col-md-6">
                    <img src="assets/images/z1.png" alt="Programming Books" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <hr>

    <!-- Featured Books Section -->
    <div class="container py-5">
        <h2 class="text-center mb-4">Featured Books</h2>
        <div class="row">
            <?php foreach ($featuredBooks as $book): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="book-image-container">
                            <img src="assets/images/books/<?php echo $book['image']; ?>" class="card-img-top book-cover-image" alt="<?php echo $book['title']; ?>">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title book-title"><?php echo $book['title']; ?></h5>
                            <p class="card-text text-muted">By <?php echo $book['author']; ?></p>
                            <p class="card-text book-description flex-grow-1"><?php echo substr($book['description'], 0, 100) . '...'; ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="h5 text-primary">$<?php echo $book['price']; ?></span>
                                <a href="book-details.php?id=<?php echo $book['book_id']; ?>" class="btn btn-outline-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="books.php" class="btn btn-primary">View All Books</a>
        </div>
    </div>


    <hr>

    <!-- Why Choose Us Section -->
    <div class="container py-5">
        <h2 class="text-center mb-4">Why Choose Us</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-book fa-3x text-primary mb-3"></i>
                        <h4>Quality Selection</h4>
                        <p>Carefully curated collection of the best programming books from trusted publishers.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                        <h4>Fast Delivery</h4>
                        <p>Quick and reliable shipping to get your books to you as soon as possible.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                        <h4>Customer Support</h4>
                        <p>Dedicated support team to assist you with any questions or concerns.</p>
                    </div>
                </div>
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