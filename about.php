<?php
session_start();
include 'config/database.php';
$conn = connectDB();
// Any database operations if needed
closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Programming Books Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/user/headers.css" rel="stylesheet">
    <link href="assets/css/user/footer.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- About Us Content -->
    <div class="container py-5">
        <h1 class="text-center mb-4">About Us</h1>
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h2 class="mb-3">Our Story</h2>
                        <p>Welcome to Programming Books Store, your number one source for all programming and technology books. We're dedicated to providing you the very best of programming resources, with an emphasis on quality, selection, and customer service.</p>

                        <p>Founded in 2023, Programming Books Store has come a long way from its beginnings. We now serve customers all over the country, and are thrilled to be a part of the tech education industry.</p>

                        <h2 class="mb-3 mt-4">Our Mission</h2>
                        <p>Our mission is to help developers of all skill levels improve their craft through carefully selected programming books. We believe that knowledge should be accessible to everyone who wants to learn.</p>

                        <h2 class="mb-3 mt-4">Our Team</h2>
                        <p>Our team consists of passionate programmers and book lovers who are dedicated to bringing you the best selection of programming books. We carefully review and select each book in our collection to ensure it meets our high standards.</p>

                        <p>We hope you enjoy our products as much as we enjoy offering them to you. If you have any questions or comments, please don't hesitate to contact us.</p>
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