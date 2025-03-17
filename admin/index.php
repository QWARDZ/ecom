<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$conn = connectDB();

// Get total number of books
$sql = "SELECT COUNT(*) as total_books FROM books";
$result = $conn->query($sql);
$totalBooks = $result->fetch_assoc()['total_books'];

// Get total number of users
$sql = "SELECT COUNT(*) as total_users FROM users";
$result = $conn->query($sql);
$totalUsers = $result->fetch_assoc()['total_users'];

// Get total number of orders
$sql = "SELECT COUNT(*) as total_orders FROM orders";
$result = $conn->query($sql);
$totalOrders = $result->fetch_assoc()['total_orders'];

// Get total revenue
$sql = "SELECT SUM(total_amount) as total_revenue FROM orders";
$result = $conn->query($sql);
$totalRevenue = $result->fetch_assoc()['total_revenue'] ?? 0;

// Get recent orders
$sql = "SELECT o.*, u.username, u.name, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        ORDER BY o.order_date DESC 
        LIMIT 5";
$result = $conn->query($sql);
$recentOrders = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recentOrders[] = $row;
    }
}

// Get low stock books
$sql = "SELECT * FROM books WHERE stock < 10 ORDER BY stock ASC LIMIT 5";
$result = $conn->query($sql);
$lowStockBooks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lowStockBooks[] = $row;
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Programming Books Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="add-book.php" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-plus"></i> Add New Book
                            </a>
                            <a href="manage-orders.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-list"></i> View All Orders
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Books</h6>
                                        <h3 class="mb-0"><?php echo $totalBooks; ?></h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-book fa-2x text-primary"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="manage-books.php" class="text-decoration-none">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Users</h6>
                                        <h3 class="mb-0"><?php echo $totalUsers; ?></h3>
                                    </div>
                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-users fa-2x text-success"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="manage-users.php" class="text-decoration-none">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Orders</h6>
                                        <h3 class="mb-0"><?php echo $totalOrders; ?></h3>
                                    </div>
                                    <div class="bg-info bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-shopping-cart fa-2x text-info"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="manage-orders.php" class="text-decoration-none">View Details <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="text-muted">Total Revenue</h6>
                                        <h3 class="mb-0">$<?php echo number_format($totalRevenue, 2); ?></h3>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                                        <i class="fas fa-dollar-sign fa-2x text-warning"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="sales-report.php" class="text-decoration-none">View Report <i class="fas fa-arrow-right ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-md-8 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Orders</h5>
                                    <a href="manage-orders.php" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($recentOrders)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No orders found</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($recentOrders as $order): ?>
                                                    <tr>
                                                        <td>#<?php echo $order['order_id']; ?></td>
                                                        <td><?php echo $order['name']; ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                        <td>
                                                            <?php
                                                            $statusClass = '';
                                                            switch ($order['status']) {
                                                                case 'pending':
                                                                    $statusClass = 'bg-warning';
                                                                    break;
                                                                case 'processing':
                                                                    $statusClass = 'bg-info';
                                                                    break;
                                                                case 'shipped':
                                                                    $statusClass = 'bg-primary';
                                                                    break;
                                                                case 'delivered':
                                                                    $statusClass = 'bg-success';
                                                                    break;
                                                                case 'cancelled':
                                                                    $statusClass = 'bg-danger';
                                                                    break;
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $statusClass; ?>"><?php echo ucfirst($order['status']); ?></span>
                                                        </td>
                                                        <td>
                                                            <a href="view-order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Low Stock Books -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Low Stock Books</h5>
                                    <a href="manage-books.php" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php if (empty($lowStockBooks)): ?>
                                        <li class="list-group-item text-center">No low stock books found</li>
                                    <?php else: ?>
                                        <?php foreach ($lowStockBooks as $book): ?>
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo $book['title']; ?></h6>
                                                        <small class="text-muted">By <?php echo $book['author']; ?></small>
                                                    </div>
                                                    <span class="badge bg-danger"><?php echo $book['stock']; ?> left</span>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/script.js"></script>
</body>

</html>