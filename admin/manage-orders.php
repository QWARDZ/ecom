<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$conn = connectDB();

// Handle order status update
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status = '$status' WHERE order_id = $orderId";
    if ($conn->query($sql) === TRUE) {
        $statusMessage = "Order status updated successfully!";
        $statusType = "success";
    } else {
        $statusMessage = "Error updating order status: " . $conn->error;
        $statusType = "danger";
    }
}

// Get orders with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Filter by status if provided
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$whereClause = $statusFilter ? "WHERE o.status = '$statusFilter'" : "";

// Get total orders count
$countSql = "SELECT COUNT(*) as total FROM orders o $whereClause";
$countResult = $conn->query($countSql);
$totalOrders = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalOrders / $limit);

// Get orders
$sql = "SELECT o.*, u.username, u.name, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        $whereClause
        ORDER BY o.order_date DESC 
        LIMIT $offset, $limit";
$result = $conn->query($sql);
$orders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin Panel</title>
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
                    <h1 class="h2">Manage Orders</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $statusFilter ? ucfirst($statusFilter) : 'All Orders'; ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                <li><a class="dropdown-item <?php echo $statusFilter == '' ? 'active' : ''; ?>" href="manage-orders.php">All Orders</a></li>
                                <li><a class="dropdown-item <?php echo $statusFilter == 'pending' ? 'active' : ''; ?>" href="manage-orders.php?status=pending">Pending</a></li>
                                <li><a class="dropdown-item <?php echo $statusFilter == 'processing' ? 'active' : ''; ?>" href="manage-orders.php?status=processing">Processing</a></li>
                                <li><a class="dropdown-item <?php echo $statusFilter == 'shipped' ? 'active' : ''; ?>" href="manage-orders.php?status=shipped">Shipped</a></li>
                                <li><a class="dropdown-item <?php echo $statusFilter == 'delivered' ? 'active' : ''; ?>" href="manage-orders.php?status=delivered">Delivered</a></li>
                                <li><a class="dropdown-item <?php echo $statusFilter == 'cancelled' ? 'active' : ''; ?>" href="manage-orders.php?status=cancelled">Cancelled</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if (isset($statusMessage)): ?>
                    <div class="alert alert-<?php echo $statusType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $statusMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (empty($orders)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No orders found.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td>
                                            <div><?php echo $order['name']; ?></div>
                                            <small class="text-muted"><?php echo $order['email']; ?></small>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                                    echo $order['status'] == 'pending' ? 'warning' : ($order['status'] == 'processing' ? 'info' : ($order['status'] == 'shipped' ? 'primary' : ($order['status'] == 'delivered' ? 'success' : 'danger')));
                                                                    ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="view-order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#updateStatusModal<?php echo $order['order_id']; ?>">
                                                <i class="fas fa-edit"></i> Status
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Update Status Modal -->
                                    <div class="modal fade" id="updateStatusModal<?php echo $order['order_id']; ?>" tabindex="-1" aria-labelledby="updateStatusModalLabel<?php echo $order['order_id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="updateStatusModalLabel<?php echo $order['order_id']; ?>">Update Order Status</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Status</label>
                                                            <select class="form-select" name="status" id="status">
                                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo "?page=" . ($page - 1) . ($statusFilter ? "&status=$statusFilter" : ''); ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo "?page=$i" . ($statusFilter ? "&status=$statusFilter" : ''); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo "?page=" . ($page + 1) . ($statusFilter ? "&status=$statusFilter" : ''); ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/admin.js"></script>
</body>

</html>