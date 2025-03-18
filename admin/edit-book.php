<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if book ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage-books.php");
    exit();
}

$bookId = $_GET['id'];
$conn = connectDB();

// Get book details
$sql = "SELECT * FROM books WHERE book_id = $bookId";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: manage-books.php");
    exit();
}

$book = $result->fetch_assoc();

// Get categories
$sql = "SELECT DISTINCT category FROM books ORDER BY category";
$result = $conn->query($sql);
$categories = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $publicationDate = $_POST['publication_date'];

    // Handle image upload
    $image = $book['image']; // Default to current image

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= $maxSize) {
            $fileName = time() . '_' . $_FILES['image']['name'];
            $uploadPath = '../assets/images/books/' . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Delete old image if it exists and is not the default image
                if ($book['image'] != 'default.jpg' && file_exists('../assets/images/books/' . $book['image'])) {
                    unlink('../assets/images/books/' . $book['image']);
                }

                $image = $fileName;
            } else {
                $uploadError = "Failed to upload image. Please try again.";
            }
        } else {
            $uploadError = "Invalid file. Please upload a JPG, PNG, or GIF image under 5MB.";
        }
    }

    // Update book in database
    $sql = "UPDATE books SET 
            title = '$title', 
            author = '$author', 
            description = '$description', 
            price = $price, 
            stock = $stock, 
            category = '$category', 
            publication_date = '$publicationDate', 
            image = '$image' 
            WHERE book_id = $bookId";

    if ($conn->query($sql) === TRUE) {
        $successMessage = "Book updated successfully!";

        // Refresh book data
        $sql = "SELECT * FROM books WHERE book_id = $bookId";
        $result = $conn->query($sql);
        $book = $result->fetch_assoc();
    } else {
        $errorMessage = "Error updating book: " . $conn->error;
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Admin Panel</title>
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
                    <h1 class="h2">Edit Book</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="manage-books.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Books
                        </a>
                    </div>
                </div>

                <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $successMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $errorMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($uploadError)): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?php echo $uploadError; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $bookId; ?>" enctype="multipart/form-data" class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $book['title']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="author" class="form-label">Author</label>
                                    <input type="text" class="form-control" id="author" name="author" value="<?php echo $book['author']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo $book['price']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="stock" name="stock" min="0" value="<?php echo $book['stock']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat; ?>" <?php echo $book['category'] == $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                        <?php endforeach; ?>
                                        <option value="other">Other (New Category)</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="newCategoryGroup" style="display: none;">
                                    <label for="new_category" class="form-label">New Category</label>
                                    <input type="text" class="form-control" id="new_category" name="new_category">
                                </div>
                                <div class="mb-3">
                                    <label for="publication_date" class="form-label">Publication Date</label>
                                    <input type="date" class="form-control" id="publication_date" name="publication_date" value="<?php echo $book['publication_date']; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $book['description']; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Book Cover Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    <div class="form-text">Leave empty to keep current image. Max file size: 5MB. Accepted formats: JPG, PNG, GIF.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Current Image</label>
                                    <div class="text-center">
                                        <img src="../assets/images/books/<?php echo $book['image']; ?>" class="img-thumbnail" alt="<?php echo $book['title']; ?>" style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="update_book" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Book
                                </button>
                                <a href="manage-books.php" class="btn btn-secondary">Cancel</a>
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
    <script>
        // Show/hide new category input based on selection
        document.getElementById('category').addEventListener('change', function() {
            const newCategoryGroup = document.getElementById('newCategoryGroup');
            const newCategoryInput = document.getElementById('new_category');

            if (this.value === 'other') {
                newCategoryGroup.style.display = 'block';
                newCategoryInput.setAttribute('required', 'required');
            } else {
                newCategoryGroup.style.display = 'none';
                newCategoryInput.removeAttribute('required');
            }
        });

        // Handle form submission with new category
        document.querySelector('form').addEventListener('submit', function(e) {
            const categorySelect = document.getElementById('category');
            const newCategoryInput = document.getElementById('new_category');

            if (categorySelect.value === 'other' && newCategoryInput.value.trim() !== '') {
                categorySelect.innerHTML += `<option value="${newCategoryInput.value}" selected>${newCategoryInput.value}</option>`;
            }
        });
    </script>
</body>

</html>