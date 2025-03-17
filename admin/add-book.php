<?php
session_start();
include '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$conn = connectDB();

// Process form submission
$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $publicationDate = $_POST['publication_date'];

    // Default image if not uploaded
    $image = 'default_book.jpg';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (in_array($_FILES['image']['type'], $allowedTypes) && $_FILES['image']['size'] <= $maxSize) {
            $fileName = time() . '_' . $_FILES['image']['name'];
            $uploadPath = '../assets/images/books/' . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = $fileName;
            } else {
                $message = "Error uploading image. Using default image.";
                $messageType = "warning";
            }
        } else {
            $message = "Invalid image format or size too large. Using default image.";
            $messageType = "warning";
        }
    }

    // Insert book into database
    $sql = "INSERT INTO books (title, author, description, price, image, category, stock, publication_date) 
            VALUES ('$title', '$author', '$description', $price, '$image', '$category', $stock, '$publicationDate')";

    if ($conn->query($sql) === TRUE) {
        $message = "Book added successfully!";
        $messageType = "success";
    } else {
        $message = "Error adding book: " . $conn->error;
        $messageType = "danger";
    }
}

// Get all categories for dropdown
$sql = "SELECT DISTINCT category FROM books ORDER BY category";
$result = $conn->query($sql);
$categories = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}

closeDB($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book - Admin Panel</title>
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
                    <h1 class="h2">Add New Book</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="manage-books.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Books
                        </a>
                    </div>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Book Title *</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                        <div class="invalid-feedback">
                                            Please enter a book title.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="author" class="form-label">Author *</label>
                                        <input type="text" class="form-control" id="author" name="author" required>
                                        <div class="invalid-feedback">
                                            Please enter an author name.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description *</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                                        <div class="invalid-feedback">
                                            Please enter a description.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Book Cover Image</label>
                                        <input type="file" class="form-control" id="image" name="image">
                                        <div class="form-text">Max file size: 2MB. Supported formats: JPG, PNG, GIF.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price ($) *</label>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                                        <div class="invalid-feedback">
                                            Please enter a valid price.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category *</label>
                                        <div class="input-group">
                                            <select class="form-select" id="category" name="category" required>
                                                <option value="" selected disabled>Select a category</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                                                <?php endforeach; ?>
                                                <option value="new_category">Add New Category</option>
                                            </select>
                                            <button class="btn btn-outline-secondary" type="button" id="showNewCategoryInput">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div id="newCategoryInput" class="mt-2 d-none">
                                            <input type="text" class="form-control" id="new_category" placeholder="Enter new category">
                                        </div>
                                        <div class="invalid-feedback">
                                            Please select a category.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Stock *</label>
                                        <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                                        <div class="invalid-feedback">
                                            Please enter stock quantity.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="publication_date" class="form-label">Publication Date *</label>
                                        <input type="date" class="form-control" id="publication_date" name="publication_date" required>
                                        <div class="invalid-feedback">
                                            Please select a publication date.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="reset" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Add Book
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
    <script src="../assets/js/script.js"></script>
    <script>
        // Show/hide new category input
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');
            const newCategoryInput = document.getElementById('newCategoryInput');
            const newCategoryField = document.getElementById('new_category');
            const showNewCategoryBtn = document.getElementById('showNewCategoryInput');

            // Show new category input when "Add New Category" is selected
            categorySelect.addEventListener('change', function() {
                if (this.value === 'new_category') {
                    newCategoryInput.classList.remove('d-none');
                    newCategoryField.setAttribute('required', 'required');
                } else {
                    newCategoryInput.classList.add('d-none');
                    newCategoryField.removeAttribute('required');
                }
            });

            // Show new category input when button is clicked
            showNewCategoryBtn.addEventListener('click', function() {
                categorySelect.value = 'new_category';
                newCategoryInput.classList.remove('d-none');
                newCategoryField.setAttribute('required', 'required');
                newCategoryField.focus();
            });

            // Handle form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                if (categorySelect.value === 'new_category') {
                    e.preventDefault();
                    const newCategory = newCategoryField.value.trim();

                    if (newCategory) {
                        // Create new option
                        const newOption = document.createElement('option');
                        newOption.value = newCategory;
                        newOption.textContent = newCategory;

                        // Insert before "Add New Category" option
                        categorySelect.insertBefore(newOption, categorySelect.options[categorySelect.options.length - 1]);

                        // Select the new option
                        categorySelect.value = newCategory;

                        // Hide the input
                        newCategoryInput.classList.add('d-none');
                        newCategoryField.removeAttribute('required');

                        // Submit the form
                        this.submit();
                    } else {
                        newCategoryField.classList.add('is-invalid');
                    }
                }
            });
        });
    </script>
</body>

</html>