# Programming Books E-commerce Website

A professional e-commerce website for selling programming books, built with PHP, Bootstrap, and MySQL.

## Features

- **User System**
  - User registration and login
  - Profile management
  - Order history

- **Admin System**
  - Separate admin login
  - Dashboard with statistics
  - Book management (add, edit, delete)
  - Order management
  - User management

- **Shopping Features**
  - Browse books by category
  - Search functionality
  - Book details with related books
  - Shopping cart
  - Checkout process

- **Responsive Design**
  - Mobile-friendly interface
  - Bootstrap 5 framework
  - Clean and modern UI

## Setup Instructions

### Prerequisites

- XAMPP (or any PHP/MySQL environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation Steps

1. **Clone or download the repository**
   - Place the files in your XAMPP `htdocs` folder (e.g., `C:/xampp/htdocs/ecommerce`).

2. **Set up the database**
   - Start Apache and MySQL in XAMPP.
   - Open phpMyAdmin (http://localhost/phpmyadmin).
   - Create a new database named `programming_books_ecommerce`.
   - Import the `database.sql` file to set up the tables and sample data.

3. **Configure the database connection**
   - Open `config/database.php`.
   - Update the database credentials if needed (default is root with no password).

4. **Access the website**
   - Open your browser and navigate to `http://localhost/ecommerce`.

### Default Login Credentials

#### Admin Login
- **Username**: admin
- **Password**: admin123
