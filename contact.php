<?php
session_start();
include 'config/database.php';
$conn = connectDB();

// Process contact form submission
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message_content = $_POST['message'] ?? '';

    // Basic validation
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message_content)) {
        // In a real application, you would send an email or store in database
        // For now, just show a success message
        $message = '<div class="alert alert-success">Thank you for your message! We will get back to you soon.</div>';
    } else {
        $message = '<div class="alert alert-danger">Please fill in all fields.</div>';
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Programming Books Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/user/headers.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Contact Content -->
    <div class="container py-5">
        <h1 class="text-center mb-4">Contact Us</h1>

        <?php echo $message; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="mb-3">Get in Touch</h2>
                        <form method="post" action="contact.php">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="mb-3">Contact Information</h2>
                        <p><i class="fas fa-map-marker-alt me-2 text-primary"></i> 123 Book Street, Reading City, RC 12345</p>
                        <p><i class="fas fa-phone me-2 text-primary"></i> (123) 456-7890</p>
                        <p><i class="fas fa-envelope me-2 text-primary"></i> info@programmingbooksstore.com</p>

                        <h3 class="mt-4 mb-3">Business Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                        <p>Saturday: 10:00 AM - 4:00 PM</p>
                        <p>Sunday: Closed</p>

                        <h3 class="mt-4 mb-3">Follow Us</h3>
                        <div class="social-icons">
                            <a href="#" class="text-primary me-3"><i class="fab fa-facebook-f fa-2x"></i></a>
                            <a href="#" class="text-primary me-3"><i class="fab fa-twitter fa-2x"></i></a>
                            <a href="#" class="text-primary me-3"><i class="fab fa-instagram fa-2x"></i></a>
                            <a href="#" class="text-primary"><i class="fab fa-linkedin-in fa-2x"></i></a>
                        </div>
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