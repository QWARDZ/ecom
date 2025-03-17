<?php
session_start();
include 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=cart.php");
    exit();
}

$userId = $_SESSION['user_id'];
$conn = connectDB();

// Process cart actions
$message = '';
$messageType = '';

// Remove item from cart
if (isset($_GET['remove']) && !empty($_GET['remove'])) {
    $cartId = $_GET['remove'];

    $sql = "DELETE FROM cart WHERE cart_id = $cartId AND user_id = $userId";

    if ($conn->query($sql) === TRUE) {
        $message = "Item removed from cart successfully!";
        $messageType = "success";
    } else {
        $message = "Error removing item: " . $conn->error;
        $messageType = "danger";
    }
}

// Update cart quantities
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cartId => $quantity) {
        $quantity = (int)$quantity;

        if ($quantity > 0) {
            $sql = "UPDATE cart SET quantity = $quantity WHERE cart_id = $cartId AND user_id = $userId";
            $conn->query($sql);
        } else {
            $sql = "DELETE FROM cart WHERE cart_id = $cartId AND user_id = $userId";
            $conn->query($sql);
        }
    }

    $message = "Cart updated successfully!";
    $messageType = "success";
}

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
}

// Calculate totals
$tax = $subtotal * 0.1; // 10% tax
$shipping = ($subtotal > 0) ? 5.99 : 0; // Flat shipping rate
$total = $subtotal + $tax + $shipping;

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Programming Books Store</title>
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

    <!-- Cart Section -->
    <div class="container py-5">
        <h1 class="mb-4">Shopping Cart</h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">
                <i class="fas fa-shopping-cart me-2"></i> Your cart is empty.
            </div>
            <div class="text-center mt-4">
                <a href="books.php" class="btn btn-primary">
                    <i class="fas fa-book me-2"></i> Browse Books
                </a>
            </div>
        <?php else: ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cartItems as $item): ?>
                                                <tr class="cart-item">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="assets/images/books/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" class="img-thumbnail me-3" style="width: 60px;">
                                                            <div>
                                                                <h6 class="mb-0"><?php echo $item['title']; ?></h6>
                                                                <small class="text-muted">By <?php echo $item['author']; ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                                    <td>
                                                        <div class="quantity-control">
                                                            <button type="button" class="quantity-btn" data-action="decrease">-</button>
                                                            <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="form-control">
                                                            <button type="button" class="quantity-btn" data-action="increase">+</button>
                                                        </div>
                                                    </td>
                                                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                                    <td>
                                                        <a href="cart.php?remove=<?php echo $item['cart_id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to remove this item?');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="books.php" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                                    </a>
                                    <button type="submit" name="update_cart" class="btn btn-primary">
                                        <i class="fas fa-sync-alt me-2"></i> Update Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
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
                                <div class="d-grid">
                                    <a href="checkout.php" class="btn btn-primary">
                                        <i class="fas fa-credit-card me-2"></i> Proceed to Checkout
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="mb-3">Have a Coupon?</h5>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Enter coupon code">
                                    <button class="btn btn-outline-primary" type="button">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>

</html>