<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <h5>Programming Books</h5>
                <p>Your one-stop shop for the best programming and technology books to enhance your skills and advance your career.</p>
                <div class="social-icons">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-md-2 mb-4 mb-md-0">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-white text-decoration-none">Home</a></li>
                    <li><a href="books.php" class="text-white text-decoration-none">Books</a></li>
                    <li><a href="about.php" class="text-white text-decoration-none">About Us</a></li>
                    <li><a href="contact.php" class="text-white text-decoration-none">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <h5>Categories</h5>
                <ul class="list-unstyled">
                    <?php
                    $conn = connectDB();
                    $sql = "SELECT DISTINCT category FROM books ORDER BY category LIMIT 5";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<li><a href="books.php?category=' . urlencode($row['category']) . '" class="text-white text-decoration-none">' . $row['category'] . '</a></li>';
                        }
                    }
                    closeDB($conn);
                    ?>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact Us</h5>
                <address>
                    <p><i class="fas fa-map-marker-alt me-2"></i> 123 Book Street, Reading City</p>
                    <p><i class="fas fa-phone me-2"></i> (123) 456-7890</p>
                    <p><i class="fas fa-envelope me-2"></i> info@programmingbooks.com</p>
                </address>
            </div>
        </div>
        <hr class="my-3 bg-secondary">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Programming Books. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="privacy-policy.php" class="text-white text-decoration-none me-3">Privacy Policy</a>
                <a href="terms-of-service.php" class="text-white text-decoration-none">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>