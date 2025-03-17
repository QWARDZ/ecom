<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$conn = connectDB();

// Set default date range (last 30 days)
$endDate = date('Y-m-d');
$startDate = date('Y-m-d', strtotime('-30 days'));

// Handle date range filter
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
}

// Get sales data
$sql = "SELECT DATE(order_date) as date, COUNT(*) as order_count, SUM(total_amount) as revenue 
        FROM orders 
        WHERE order_date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        GROUP BY DATE(order_date)
        ORDER BY date";
$result = $conn->query($sql);
$salesData = [];
$totalOrders = 0;
$totalRevenue = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $salesData[] = $row;
        $totalOrders += $row['order_count'];
        $totalRevenue += $row['revenue'];
    }
}

// Get top selling books
$sql = "SELECT b.book_id, b.title, b.author, b.image, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as total_revenue
        FROM order_items oi
        JOIN books b ON oi.book_id = b.book_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.order_date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        GROUP BY b.book_id
        ORDER BY total_sold DESC
        LIMIT 5";
$result = $conn->query($sql);
$topBooks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topBooks[] = $row;
    }
}

// Get sales by category
$sql = "SELECT b.category, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as total_revenue
        FROM order_items oi
        JOIN books b ON oi.book_id = b.book_id
        JOIN orders o ON oi.order_id = o.order_id
        WHERE o.order_date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'
        GROUP BY b.category
        ORDER BY total_revenue DESC";
$result = $conn->query($sql);
$categoryData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categoryData[] = $row;
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report - Admin Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="admin-body">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 admin-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Sales Report</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <form class="d-flex" method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="input-group me-2">
                                <span class="input-group-text">From</span>
                                <input type="date" class="form-control" name="start_date" value="<?php echo $startDate; ?>">
                            </div>
                            <div class="input-group me-2">
                                <span class="input-group-text">To</span>
                                <input type="date" class="form-control" name="end_date" value="<?php echo $endDate; ?>">
                            </div>
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Orders</h6>
                                        <h2 class="mb-0"><?php echo $totalOrders; ?></h2>
                                    </div>
                                    <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Revenue</h6>
                                        <h2 class="mb-0">$<?php echo number_format($totalRevenue, 2); ?></h2>
                                    </div>
                                    <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Average Order Value</h6>
                                        <h2 class="mb-0">$<?php echo $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00'; ?></h2>
                                    </div>
                                    <i class="fas fa-chart-line fa-3x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales Chart -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Sales Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" height="300"></canvas>
                    </div>
                </div>

                <div class="row">
                    <!-- Top Selling Books -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Top Selling Books</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($topBooks)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> No sales data available for this period.
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 60px;">Image</th>
                                                    <th>Book</th>
                                                    <th>Sold</th>
                                                    <th>Revenue</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($topBooks as $book): ?>
                                                    <tr>
                                                        <td>
                                                            <img src="../assets/images/books/<?php echo $book['image']; ?>" class="img-thumbnail" alt="<?php echo $book['title']; ?>" style="width: 50px;">
                                                        </td>
                                                        <td>
                                                            <div class="fw-bold"><?php echo $book['title']; ?></div>
                                                            <small class="text-muted">By <?php echo $book['author']; ?></small>
                                                        </td>
                                                        <td><?php echo $book['total_sold']; ?></td>
                                                        <td>$<?php echo number_format($book['total_revenue'], 2); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sales by Category -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Sales by Category</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($categoryData)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> No sales data available for this period.
                                    </div>
                                <?php else: ?>
                                    <canvas id="categoryChart" height="300"></canvas>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Sales Data -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Daily Sales Data</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($salesData)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> No sales data available for this period.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Orders</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($salesData as $data): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($data['date'])); ?></td>
                                                <td><?php echo $data['order_count']; ?></td>
                                                <td>$<?php echo number_format($data['revenue'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/admin.js"></script>

    <!-- Chart.js Scripts -->
    <script>
        // Sales Chart
        <?php if (!empty($salesData)): ?>
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: [<?php echo implode(', ', array_map(function ($data) {
                                    return "'" . date('M d', strtotime($data['date'])) . "'";
                                }, $salesData)); ?>],
                    datasets: [{
                            label: 'Revenue ($)',
                            data: [<?php echo implode(', ', array_map(function ($data) {
                                        return $data['revenue'];
                                    }, $salesData)); ?>],
                            borderColor: 'rgba(40, 167, 69, 1)',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Orders',
                            data: [<?php echo implode(', ', array_map(function ($data) {
                                        return $data['order_count'];
                                    }, $salesData)); ?>],
                            borderColor: 'rgba(0, 123, 255, 1)',
                            backgroundColor: 'rgba(0, 123, 255, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Revenue ($)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            title: {
                                display: true,
                                text: 'Orders'
                            }
                        }
                    }
                }
            });
        <?php endif; ?>

        // Category Chart
        <?php if (!empty($categoryData)): ?>
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryChart = new Chart(categoryCtx, {
                type: 'pie',
                data: {
                    labels: [<?php echo implode(', ', array_map(function ($data) {
                                    return "'" . $data['category'] . "'";
                                }, $categoryData)); ?>],
                    datasets: [{
                        data: [<?php echo implode(', ', array_map(function ($data) {
                                    return $data['total_revenue'];
                                }, $categoryData)); ?>],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(199, 199, 199, 0.7)',
                            'rgba(83, 102, 255, 0.7)',
                            'rgba(40, 159, 64, 0.7)',
                            'rgba(210, 199, 199, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += '$' + parseFloat(context.raw).toFixed(2);
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>