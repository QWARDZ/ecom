<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <title>footer</title>
    <link href="assets/css/footer.css" rel="stylesheet">

</head>
<body>
    <footer class="py-3" style="background: linear-gradient(135deg, #3a6186, #89253e); color: #f8f9fa;">
        <div class="container">
            <div class="row g-3">
                <!-- Main content in 3 columns -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold mb-2" style="color: #ffc107;">Programming Books</h6>
                    <p class="small mb-2 text-light opacity-75">Your one-stop shop for programming and technology books.</p>
                    <div class="mb-2">
                        <a href="#" class="text-light me-2 fa-lg"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-light me-2 fa-lg"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light me-2 fa-lg"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light fa-lg"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="row">
                        <!-- Quick Links -->
                        <div class="col-6">
                            <h6 class="fw-bold mb-2" style="color: #ffc107;">Quick Links</h6>
                            <ul class="list-unstyled small">
                                <li><a href="index.php" class="text-decoration-none text-light opacity-75">Home</a></li>
                                <li><a href="books.php" class="text-decoration-none text-light opacity-75">Books</a></li>
                                <li><a href="about.php" class="text-decoration-none text-light opacity-75">About Us</a></li>
                                <li><a href="contact.php" class="text-decoration-none text-light opacity-75">Contact</a></li>
                            </ul>
                        </div>

                        <!-- Categories -->
                        <div class="col-6">
                            <h6 class="fw-bold mb-2" style="color: #ffc107;">Categories</h6>
                            <ul class="list-unstyled small">
                                <?php
                                $conn = connectDB();
                                $sql = "SELECT DISTINCT category FROM books ORDER BY category LIMIT 4";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<li><a href="books.php?category=' . urlencode($row['category']) . '" class="text-decoration-none text-light opacity-75">' . $row['category'] . '</a></li>';
                                    }
                                }
                                closeDB($conn);
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contact -->
                <div class="col-lg-4">
                    <h6 class="fw-bold mb-2" style="color: #ffc107;">Contact Us</h6>
                    <ul class="list-unstyled small">
                        <li class="text-light opacity-75"><i class="fas fa-map-marker-alt me-2"></i>123 Book Street, Reading City</li>
                        <li class="text-light opacity-75"><i class="fas fa-phone me-2"></i>(123) 456-7890</li>
                        <li class="text-light opacity-75"><i class="fas fa-envelope me-2"></i>info@programmingbooks.com</li>
                    </ul>
                </div>
            </div>

            <!-- Copyright bar -->
            <div class="row mt-2">
                <div class="col-md-6 small">
                    <p class="mb-0 text-light opacity-75">&copy; <?php echo date('Y'); ?> Programming Books. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end small">
                    <a href="privacy-policy.php" class="text-decoration-none text-light opacity-75 me-3">Privacy</a>
                    <a href="terms-of-service.php" class="text-decoration-none text-light opacity-75">Terms</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


