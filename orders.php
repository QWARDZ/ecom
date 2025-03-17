<?php
session_start();
include 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = connectDB();
$userId = $_SESSION['user_id'];

// Add error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug information
echo "<!-- Debug: User ID = " . $userId . " -->";

// Fetch user's orders
$sql = "SELECT o.*, COUNT(oi.item_id) as item_count, 
        SUM(oi.quantity * oi.price) as total_amount 
        FROM orders o 
        JOIN order_items oi ON o.order_id = oi.order_id 
        WHERE o.user_id = ? 
        GROUP BY o.order_id 
        ORDER BY o.order_date DESC";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];

    echo "<!-- Debug: Query executed, found " . $result->num_rows . " orders -->";

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}

// Get order details if an order is selected
$orderDetails = [];
if (isset($_GET['order_id']) && !empty($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    // Verify this order belongs to the current user
    $checkSql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
    try {
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $orderId, $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Fetch order items
            $detailsSql = "SELECT oi.*, b.title, b.image 
                          FROM order_items oi 
                          JOIN books b ON oi.book_id = b.book_id 
                          WHERE oi.order_id = ?";
            $detailsStmt = $conn->prepare($detailsSql);
            $detailsStmt->bind_param("i", $orderId);
            $detailsStmt->execute();
            $detailsResult = $detailsStmt->get_result();

            while ($row = $detailsResult->fetch_assoc()) {
                $orderDetails[] = $row;
            }
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Programming Books Store</title>
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

    <!-- Orders Content -->
    <div class="container py-5">
        <h1 class="mb-4">My Orders</h1>

        <div class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a href="profile.php" class="text-decoration-none">Profile Information</a></li>
                        <li class="list-group-item active">My Orders</li>
                        <li class="list-group-item"><a href="logout.php" class="text-decoration-none text-danger">Logout</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-9">
                <?php if (empty($orders)): ?>
                    <div class="alert alert-info">
                        <h4 class="alert-heading">No Orders Yet!</h4>
                        <p>You haven't placed any orders yet. Start shopping to see your orders here.</p>
                        <hr>
                        <p class="mb-0"><a href="books.php" class="btn btn-primary">Browse Books</a></p>
                    </div>
                <?php else: ?>
                    <?php if (!empty($orderDetails)): ?>
                        <div class="mb-4">
                            <a href="orders.php" class="btn btn-outline-primary mb-3"><i class="fas fa-arrow-left"></i> Back to All Orders</a>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Order #<?php echo htmlspecialchars($_GET['order_id']); ?> Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Book</th>
                                                    <th>Title</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $total = 0;
                                                foreach ($orderDetails as $item):
                                                    $subtotal = $item['price'] * $item['quantity'];
                                                    $total += $subtotal;
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <img src="assets/images/books/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="img-thumbnail" style="max-width: 50px;">
                                                        </td>
                                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" class="text-end">Total:</th>
                                                    <th>$<?php echo number_format($total, 2); ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order History</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Items</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                                    <td><?php echo htmlspecialchars($order['item_count']); ?> items</td>
                                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                    <td>
                                                        <?php
                                                        $statusClass = '';
                                                        switch ($order['status']) {
                                                            case 'Pending':
                                                                $statusClass = 'bg-warning';
                                                                break;
                                                            case 'Processing':
                                                                $statusClass = 'bg-info';
                                                                break;
                                                            case 'Shipped':
                                                                $statusClass = 'bg-primary';
                                                                break;
                                                            case 'Delivered':
                                                                $statusClass = 'bg-success';
                                                                break;
                                                            case 'Cancelled':
                                                                $statusClass = 'bg-danger';
                                                                break;
                                                            default:
                                                                $statusClass = 'bg-secondary';
                                                        }
                                                        ?>
                                                        <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($order['status']); ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="orders.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">View Details</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
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