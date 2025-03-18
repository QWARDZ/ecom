<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$conn = connectDB();
$adminId = $_SESSION['admin_id'];

// Get admin details
$sql = "SELECT * FROM admins WHERE admin_id = $adminId";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];

    $sql = "UPDATE admins SET full_name = '$fullName', email = '$email', username = '$username' WHERE admin_id = $adminId";

    if ($conn->query($sql) === TRUE) {
        $profileMessage = "Profile updated successfully!";
        $profileType = "success";

        // Update session data
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_full_name'] = $fullName;

        // Refresh admin data
        $sql = "SELECT * FROM admins WHERE admin_id = $adminId";
        $result = $conn->query($sql);
        $admin = $result->fetch_assoc();
    } else {
        $profileMessage = "Error updating profile: " . $conn->error;
        $profileType = "danger";
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Verify current password
    if (password_verify($currentPassword, $admin['password'])) {
        // Check if new passwords match
        if ($newPassword === $confirmPassword) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password
            $sql = "UPDATE admins SET password = '$hashedPassword' WHERE admin_id = $adminId";

            if ($conn->query($sql) === TRUE) {
                $passwordMessage = "Password changed successfully!";
                $passwordType = "success";
            } else {
                $passwordMessage = "Error changing password: " . $conn->error;
                $passwordType = "danger";
            }
        } else {
            $passwordMessage = "New passwords do not match!";
            $passwordType = "danger";
        }
    } else {
        $passwordMessage = "Current password is incorrect!";
        $passwordType = "danger";
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
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
                    <h1 class="h2">Settings</h1>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Profile Settings</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($profileMessage)): ?>
                                    <div class="alert alert-<?php echo $profileType; ?> alert-dismissible fade show" role="alert">
                                        <?php echo $profileMessage; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $admin['full_name']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $admin['email']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $admin['username']; ?>" required>
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Change Password</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($passwordMessage)): ?>
                                    <div class="alert alert-<?php echo $passwordType; ?> alert-dismissible fade show" role="alert">
                                        <?php echo $passwordMessage; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-primary">
                                        <i class="fas fa-key me-1"></i> Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Security Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-success me-2"></i> Use a strong password with at least 8 characters, including uppercase letters, lowercase letters, numbers, and special characters.
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-success me-2"></i> Change your password regularly, at least once every 3 months.
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-success me-2"></i> Do not share your admin credentials with anyone.
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-success me-2"></i> Always log out when you're done using the admin panel, especially on shared computers.
                            </li>
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-success me-2"></i> Keep your browser and operating system updated to ensure security.
                            </li>
                        </ul>
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