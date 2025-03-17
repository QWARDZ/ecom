<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark admin-sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="px-3 py-4 d-flex justify-content-between align-items-center">
            <a href="index.php" class="text-decoration-none text-white">
                <h5 class="mb-0"><i class="fas fa-book-reader me-2"></i> Admin Panel</h5>
            </a>
            <button class="btn btn-link d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>

        <div class="px-3 mb-4">
            <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle p-2 me-2">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div>
                    <p class="text-white mb-0"><?php echo $_SESSION['admin_full_name']; ?></p>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-books.php' || basename($_SERVER['PHP_SELF']) == 'add-book.php' || basename($_SERVER['PHP_SELF']) == 'edit-book.php' ? 'active' : ''; ?>" href="manage-books.php">
                    <i class="fas fa-book"></i>
                    Books
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-orders.php' || basename($_SERVER['PHP_SELF']) == 'view-order.php' ? 'active' : ''; ?>" href="manage-orders.php">
                    <i class="fas fa-shopping-cart"></i>
                    Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-users.php' ? 'active' : ''; ?>" href="manage-users.php">
                    <i class="fas fa-users"></i>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sales-report.php' ? 'active' : ''; ?>" href="sales-report.php">
                    <i class="fas fa-chart-bar"></i>
                    Sales Report
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </li>
        </ul>

        <hr class="my-3 bg-secondary">

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="../index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    View Website
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Mobile navbar toggle -->
<nav class="navbar navbar-dark bg-dark d-md-none">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="index.php">Admin Panel</a>
        <div class="dropdown">
            <button class="btn btn-link text-white dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                <li><a class="dropdown-item" href="../index.php" target="_blank">View Website</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>