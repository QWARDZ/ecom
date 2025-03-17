-- Database creation
CREATE DATABASE IF NOT EXISTS programming_books_ecommerce;
USE programming_books_ecommerce;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE IF NOT EXISTS books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    stock INT NOT NULL DEFAULT 0,
    publication_date DATE,
    added_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    book_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id) ON DELETE CASCADE
);

-- Insert sample admin (using hashed password for security)
INSERT INTO admins (username, email, password, full_name) 
VALUES ('admin', 'admin@books.com', '$2y$10$8WxhJz0q.XPJ5KjeEZgPXOUxvwqRDKCBgw6XlKVlS.XQFRoLWJ.Oa', 'System Administrator');
-- Note: The hashed password above is for 'admin123'

-- Insert sample users (using hashed passwords for security)
INSERT INTO users (username, email, password, name, address, phone) VALUES
('johndoe', 'john@example.com', '$2y$10$8WxhJz0q.XPJ5KjeEZgPXOUxvwqRDKCBgw6XlKVlS.XQFRoLWJ.Oa', 'John Doe', '123 Main St, Anytown, USA', '555-123-4567'),
('janedoe', 'jane@example.com', '$2y$10$8WxhJz0q.XPJ5KjeEZgPXOUxvwqRDKCBgw6XlKVlS.XQFRoLWJ.Oa', 'Jane Doe', '456 Oak Ave, Somewhere, USA', '555-987-6543');
-- Note: The hashed passwords above are for 'password123'

-- Insert sample books
INSERT INTO books (title, author, description, price, image, category, stock, publication_date) VALUES
('PHP for Beginners', 'John Smith', 'A comprehensive guide to PHP programming for beginners. This book covers all the basics of PHP development and will help you build your first dynamic website.', 29.99, 'php_beginners.jpg', 'PHP', 50, '2022-01-15'),
('Advanced JavaScript', 'Emily Johnson', 'Master advanced JavaScript concepts and techniques. Learn about closures, prototypes, async programming, and modern ES6+ features to take your JavaScript skills to the next level.', 34.99, 'advanced_js.jpg', 'JavaScript', 30, '2021-11-20'),
('Python Data Science Handbook', 'Michael Brown', 'Learn data science using Python libraries like NumPy, Pandas, Matplotlib, and Scikit-learn. Perfect for aspiring data scientists and analysts.', 39.99, 'python_ds.jpg', 'Python', 25, '2022-03-10'),
('Web Development with Bootstrap', 'Sarah Wilson', 'Create responsive websites with Bootstrap framework. This practical guide will teach you how to build modern, mobile-first websites using Bootstrap 5.', 27.99, 'bootstrap_web.jpg', 'Web Development', 40, '2022-02-05'),
('MySQL Database Design', 'David Thompson', 'Best practices for MySQL database design and optimization. Learn how to create efficient schemas, write optimized queries, and scale your database applications.', 32.99, 'mysql_design.jpg', 'Database', 35, '2021-10-12'),
('React.js Essentials', 'Alex Turner', 'Build modern user interfaces with React.js. This book covers components, state management, hooks, and integration with backend services.', 36.99, 'react_essentials.jpg', 'JavaScript', 45, '2022-04-18'),
('Machine Learning with Python', 'Lisa Chen', 'An introduction to machine learning algorithms and techniques using Python. Covers supervised and unsupervised learning, neural networks, and more.', 42.99, 'ml_python.jpg', 'Python', 20, '2022-02-28'),
('Docker for Developers', 'Robert Miller', 'Learn how to containerize your applications with Docker. This guide covers Docker basics, Docker Compose, and deployment strategies.', 31.99, 'docker_dev.jpg', 'DevOps', 30, '2021-12-05'),
('Node.js in Action', 'James Wilson', 'Build scalable server-side applications with Node.js. Learn about Express, middleware, authentication, and database integration.', 33.99, 'nodejs_action.jpg', 'JavaScript', 25, '2022-01-30'),
('CSS Mastery', 'Jennifer Lee', 'Advanced CSS techniques for modern web development. Learn about Flexbox, Grid, animations, and responsive design patterns.', 28.99, 'css_mastery.jpg', 'Web Development', 35, '2021-11-15');

-- Insert sample orders
INSERT INTO orders (user_id, total_amount, status, shipping_address, payment_method) VALUES
(1, 64.98, 'Delivered', '123 Main St, Anytown, USA', 'Credit Card'),
(1, 39.99, 'Shipped', '123 Main St, Anytown, USA', 'PayPal'),
(2, 94.97, 'Processing', '456 Oak Ave, Somewhere, USA', 'Credit Card');

-- Insert sample order items
INSERT INTO order_items (order_id, book_id, quantity, price) VALUES
(1, 1, 1, 29.99),
(1, 4, 1, 27.99),
(2, 3, 1, 39.99),
(3, 2, 1, 34.99),
(3, 5, 1, 32.99),
(3, 4, 1, 27.99); 