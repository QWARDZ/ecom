<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programming Books</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/user/headers.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg custom-navbar">
            <div class="container-fluid px-5">
                <!-- Navbar Brand -->
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-book-reader me-2"></i>
                    Programming Books
                </a>

                <!-- Navbar Toggler -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar Content -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="books.php">Books</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categories
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <?php
                                $conn = connectDB();
                                $sql = "SELECT DISTINCT category FROM books ORDER BY category";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<li><a class="dropdown-item" href="books.php?category=' . urlencode($row['category']) . '">' . $row['category'] . '</a></li>';
                                    }
                                }
                                closeDB($conn);
                                ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                    </ul>

                    <!-- Search and User Section -->
                    <div class="d-flex align-items-center">
                        <form class="d-flex me-2 search-form" action="books.php" method="GET">
                            <input class="form-control me-2" type="search" name="query" placeholder="Search books..." aria-label="Search">
                            <button class="btn btn-outline-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <a href="cart.php" class="btn btn-outline-light position-relative me-2 cart-btn">
                            <i class="fas fa-shopping-cart"></i>
                            <?php
                            if (isset($_SESSION['user_id'])) {
                                $conn = connectDB();
                                $userId = $_SESSION['user_id'];
                                $sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = $userId";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();
                                $cartCount = $row['count'];
                                closeDB($conn);

                                if ($cartCount > 0) {
                                    echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">' . $cartCount . '</span>';
                                }
                            }
                            ?>
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle user-btn" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo $_SESSION['username']; ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                                    <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-light me-2 login-btn">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                            <a href="register.php" class="btn btn-light register-btn">
                                <i class="fas fa-user-plus me-1"></i> Register
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap JS and jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>