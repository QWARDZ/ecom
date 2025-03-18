<div class="sidebar">
    <div class="sidebar-header">
        <h5><i class="fas fa-book"></i> <span>Admin Panel</span></h5>
    </div>

    <div class="admin-profile">
        <img src="../assets/images/admin-avatar.jpg" alt="Admin"
            onerror="this.src='../assets/images/default-avatar.png'"
            class="admin-avatar">
        <span><?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'admin'; ?></span>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-books.php' ? 'active' : ''; ?>" href="manage-books.php">
                <i class="fas fa-book"></i> <span>Books</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-orders.php' ? 'active' : ''; ?>" href="manage-orders.php">
                <i class="fas fa-shopping-cart"></i> <span>Orders</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-users.php' ? 'active' : ''; ?>" href="manage-users.php">
                <i class="fas fa-users"></i> <span>Users</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sales-report.php' ? 'active' : ''; ?>" href="sales-report.php">
                <i class="fas fa-chart-bar"></i> <span>Sales Report</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
        </li>
        <li class="nav-item mt-3">
            <a class="nav-link" href="../index.php" target="_blank">
                <i class="fas fa-external-link-alt"></i> <span>View Website</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </li>
    </ul>
</div>

<!-- Mobile navbar toggle - Updated -->
<nav class="navbar navbar-dark bg-dark d-md-none fixed-top">
    <div class="container-fluid px-3">
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="index.php">Admin Panel</a>
        <div class="dropdown">
            <button class="btn btn-link text-white p-0" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
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

<!-- Mobile offcanvas sidebar -->
<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileSidebarLabel"><i class="fas fa-book"></i> Admin Panel</h5>
        <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="admin-profile" style="padding: 15px;">
            <img src="../assets/images/admin-avatar.jpg" alt="Admin"
                onerror="this.src='../assets/images/default-avatar.png'"
                class="admin-avatar">
            <span><?php echo isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'admin'; ?></span>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php" data-bs-dismiss="offcanvas">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-books.php' ? 'active' : ''; ?>" href="manage-books.php" data-bs-dismiss="offcanvas">
                    <i class="fas fa-book"></i> <span>Books</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-orders.php' ? 'active' : ''; ?>" href="manage-orders.php" data-bs-dismiss="offcanvas">
                    <i class="fas fa-shopping-cart"></i> <span>Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-users.php' ? 'active' : ''; ?>" href="manage-users.php" data-bs-dismiss="offcanvas">
                    <i class="fas fa-users"></i> <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'sales-report.php' ? 'active' : ''; ?>" href="sales-report.php" data-bs-dismiss="offcanvas">
                    <i class="fas fa-chart-bar"></i> <span>Sales Report</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php" data-bs-dismiss="offcanvas">
                    <i class="fas fa-cog"></i> <span>Settings</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link" href="../index.php" target="_blank" data-bs-dismiss="offcanvas">
                    <i class="fas fa-external-link-alt"></i> <span>View Website</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-danger" href="logout.php" data-bs-dismiss="offcanvas">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
    .admin-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        background-color: #3a3f44;
        border: 2px solid #4e5359;
    }

    /* Add spacing when mobile navbar is present */
    @media (max-width: 767px) {
        body {
            padding-top: 56px;
            /* Height of the mobile navbar */
        }
    }
</style>