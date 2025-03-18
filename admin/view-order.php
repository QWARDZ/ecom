<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage-orders.php");
    exit();
}

$orderId = $_GET['id'];
$conn = connectDB();

// Get order details
$sql = "SELECT o.*, u.username, u.name, u.email, u.phone, u.address 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        WHERE o.order_id = $orderId";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: manage-orders.php");
    exit();
}

$order = $result->fetch_assoc();

// Get order items
$sql = "SELECT oi.*, b.title, b.author, b.image, b.price 
        FROM order_items oi 
        JOIN books b ON oi.book_id = b.book_id 
        WHERE oi.order_id = $orderId";
$result = $conn->query($sql);
$orderItems = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orderItems[] = $row;
    }
}

// Handle order status update
if (isset($_POST['update_status']) && isset($_POST['status'])) {
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status = '$status' WHERE order_id = $orderId";
    if ($conn->query($sql) === TRUE) {
        $statusMessage = "Order status updated successfully!";
        $statusType = "success";

        // Refresh order data
        $sql = "SELECT status FROM orders WHERE order_id = $orderId";
        $result = $conn->query($sql);
        $order['status'] = $result->fetch_assoc()['status'];
    } else {
        $statusMessage = "Error updating order status: " . $conn->error;
        $statusType = "danger";
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $orderId; ?> - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <link href="../assets/css/admin/sidebar.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-body">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Order #<?php echo $orderId; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="manage-orders.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Orders
                        </a>
                    </div>
                </div>

                <?php if (isset($statusMessage)): ?>
                    <div class="alert alert-<?php echo $statusType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $statusMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Order Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Order Date:</strong>
                                    <p><?php echo date('F j, Y H:i:s', strtotime($order['order_date'])); ?></p>
                                </div>
                                <div class="mb-3">
                                    <strong>Status:</strong>
                                    <p>
                                        <span class="badge bg-<?php
                                                                echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'processing' ? 'info' : ($order['status'] == 'shipped' ? 'primary' : ($order['status'] == 'delivered' ? 'success' : 'danger')));
                                                                ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <strong>Payment Method:</strong>
                                    <p><?php echo ucfirst($order['payment_method']); ?></p>
                                </div>
                                <div>
                                    <strong>Total Amount:</strong>
                                    <p class="text-primary fw-bold">$<?php echo number_format($order['total_amount'], 2); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Customer Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Name:</strong>
                                    <p><?php echo $order['name']; ?></p>
                                </div>
                                <div class="mb-3">
                                    <strong>Email:</strong>
                                    <p><?php echo $order['email']; ?></p>
                                </div>
                                <div class="mb-3">
                                    <strong>Phone:</strong>
                                    <p><?php echo $order['phone']; ?></p>
                                </div>
                                <div>
                                    <strong>Shipping Address:</strong>
                                    <p><?php echo nl2br($order['address']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Image</th>
                                        <th>Book</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <img src="../assets/images/books/<?php echo $item['image']; ?>" class="img-thumbnail" alt="<?php echo $item['title']; ?>" style="width: 60px;">
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?php echo $item['title']; ?></div>
                                                <small class="text-muted">By <?php echo $item['author']; ?></small>
                                            </td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total:</td>
                                        <td class="fw-bold">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Update Order Status</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $orderId; ?>" class="row g-3">
                            <div class="col-md-6">
                                <select class="form-select" name="status" id="status">
                                    <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" name="update_status" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/admin.js"></script>
</body>

</html>