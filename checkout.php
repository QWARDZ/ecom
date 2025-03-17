<?php
session_start();
include 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

$userId = $_SESSION['user_id'];
$conn = connectDB();

// Get user information
$sql = "SELECT * FROM users WHERE user_id = $userId";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Get cart items
$sql = "SELECT c.*, b.title, b.author, b.price, b.image, b.stock 
        FROM cart c 
        JOIN books b ON c.book_id = b.book_id 
        WHERE c.user_id = $userId";
$result = $conn->query($sql);
$cartItems = [];
$subtotal = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $subtotal += $row['price'] * $row['quantity'];
    }
} else {
    // Redirect to cart if it's empty
    header("Location: cart.php");
    exit();
}

// Calculate totals
$tax = $subtotal * 0.1; // 10% tax
$shipping = 5.99; // Flat shipping rate
$total = $subtotal + $tax + $shipping;

// Process checkout
$message = '';
$messageType = '';
$orderId = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    // Get form data
    $fullName = $_POST['full_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipCode = $_POST['zip_code'];
    $phone = $_POST['phone'];
    $paymentMethod = $_POST['payment_method'];

    // Create shipping address
    $shippingAddress = "$address, $city, $state $zipCode";

    // Start transaction
    $conn->begin_transaction();

    try {
        // Create order
        $sql = "INSERT INTO orders (user_id, order_date, total_amount, status, shipping_address, payment_method) 
                VALUES ($userId, NOW(), $total, 'pending', '$shippingAddress', '$paymentMethod')";

        if ($conn->query($sql) === TRUE) {
            $orderId = $conn->insert_id;

            // Add order items
            foreach ($cartItems as $item) {
                $bookId = $item['book_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];

                $sql = "INSERT INTO order_items (order_id, book_id, quantity, price) 
                        VALUES ($orderId, $bookId, $quantity, $price)";
                $conn->query($sql);

                // Update book stock
                $sql = "UPDATE books SET stock = stock - $quantity WHERE book_id = $bookId";
                $conn->query($sql);
            }

            // Clear cart
            $sql = "DELETE FROM cart WHERE user_id = $userId";
            $conn->query($sql);

            // Commit transaction
            $conn->commit();

            $message = "Order placed successfully! Your order ID is #$orderId";
            $messageType = "success";
        } else {
            throw new Exception("Error creating order: " . $conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();

        $message = $e->getMessage();
        $messageType = "danger";
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Programming Books Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Checkout Section -->
    <div class="container py-5">
        <h1 class="mb-4">Checkout</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                <?php echo $message; ?>
                <?php if ($messageType == "success"): ?>
                    <div class="mt-3">
                        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                        <a href="orders.php" class="btn btn-outline-primary ms-2">View Your Orders</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($messageType == "success"): ?>
                <!-- Hide the checkout form if order was successful -->
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>
<?php exit(); ?>
<?php endif; ?>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Shipping Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                            <div class="invalid-feedback">
                                Please enter your full name.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                            <div class="invalid-feedback">
                                Please enter your phone number.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo $user['address']; ?>" required>
                        <div class="invalid-feedback">
                            Please enter your address.
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                            <div class="invalid-feedback">
                                Please enter your city.
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state" required>
                            <div class="invalid-feedback">
                                Please enter your state.
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="zip_code" class="form-label">ZIP Code</label>
                            <input type="text" class="form-control" id="zip_code" name="zip_code" required>
                            <div class="invalid-feedback">
                                Please enter your ZIP code.
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Payment Method</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                            <label class="form-check-label" for="credit_card">
                                <i class="fab fa-cc-visa me-2"></i> Credit Card
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                            <label class="form-check-label" for="paypal">
                                <i class="fab fa-paypal me-2"></i> PayPal
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="cash_on_delivery" value="cash_on_delivery">
                            <label class="form-check-label" for="cash_on_delivery">
                                <i class="fas fa-money-bill-wave me-2"></i> Cash on Delivery
                            </label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="place_order" class="btn btn-primary btn-lg">
                            <i class="fas fa-check-circle me-2"></i> Place Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <img src="assets/images/books/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="img-thumbnail me-2" style="width: 40px;">
                                <div>
                                    <p class="mb-0"><?php echo $item['title']; ?></p>
                                    <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                </div>
                            </div>
                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax (10%)</span>
                    <span>$<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Shipping</span>
                    <span>$<?php echo number_format($shipping, 2); ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total</strong>
                    <strong class="text-primary">$<?php echo number_format($total, 2); ?></strong>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> This is a demo checkout. No real payment will be processed.
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Need Help?</h5>
                <p class="mb-2"><i class="fas fa-phone me-2"></i> (123) 456-7890</p>
                <p class="mb-0"><i class="fas fa-envelope me-2"></i> support@programmingbooks.com</p>
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