<?php
// Include the database connection file first
include 'config/database.php';

// Then include the header
include 'includes/header.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>privacy-policy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link href="assets/css/user/headers.css" rel="stylesheet">
    <link href="assets/css/user/footer.css" rel="stylesheet">
</head>
<body>
    

    <div class="container py-5">
        <h1>Privacy Policy</h1>
        <div class="card shadow-sm">
            <div class="card-body">
                <h2>1. Information We Collect</h2>
                <p>We collect information when you register on our site, place an order, or fill out a form. When ordering or registering, we may ask you for your name, email address, mailing address, phone number, or other information.</p>

                <h2>2. How We Use Your Information</h2>
                <p>We may use the information we collect from you in the following ways:</p>
                <ul>
                    <li>To personalize your experience</li>
                    <li>To improve our website</li>
                    <li>To process transactions</li>
                    <li>To send periodic emails</li>
                </ul>

                <h2>3. How We Protect Your Information</h2>
                <p>We implement a variety of security measures to maintain the safety of your personal information when you place an order or enter, submit, or access your personal information.</p>

                <h2>4. Cookies</h2>
                <p>We use cookies to help us remember and process the items in your shopping cart and understand your preferences based on previous or current site activity.</p>

                <h2>5. Third-Party Disclosure</h2>
                <p>We do not sell, trade, or otherwise transfer to outside parties your Personally Identifiable Information.</p>
            </div>
        </div>

    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


