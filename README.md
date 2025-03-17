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

   - Place the files in your XAMPP htdocs folder (e.g., `C:/xampp/htdocs/ecommerce`)

2. **Set up the database**

   - Start Apache and MySQL in XAMPP
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `programming_books_ecommerce`
   - Import the `database.sql` file to set up the tables and sample data

3. **Configure the database connection**

   - Open `config/database.php`
   - Update the database credentials if needed (default is root with no password)

4. **Access the website**
   - Open your browser and navigate to http://localhost/ecommerce

### Default Login Credentials

#### Admin Login

- Username: admin
- Password: admin123

#### Sample User

- You can register a new user account or create one directly in the database

## Directory Structure

```
ecommerce/
├── admin/                  # Admin panel files
│   ├── includes/           # Admin panel includes
│   ├── index.php           # Admin dashboard
│   ├── login.php           # Admin login
│   └── ...                 # Other admin pages
├── assets/                 # Static assets
│   ├── css/                # CSS files
│   ├── js/                 # JavaScript files
│   └── images/             # Image files
│       └── books/          # Book cover images
├── config/                 # Configuration files
│   └── database.php        # Database connection
├── includes/               # Common include files
│   ├── header.php          # Site header
│   └── footer.php          # Site footer
├── index.php               # Homepage
├── books.php               # Books listing
├── book-details.php        # Book details page
├── cart.php                # Shopping cart
├── checkout.php            # Checkout process
├── login.php               # User login
├── register.php            # User registration
├── database.sql            # Database structure and sample data
└── README.md               # This file
```

## Security Note

This implementation stores passwords as plain text for demonstration purposes. In a production environment, you should always use password hashing (e.g., password_hash() and password_verify() functions).

## License

This project is for educational purposes only.

## Credits

- Bootstrap 5: https://getbootstrap.com/
- Font Awesome: https://fontawesome.com/
- Sample book data is fictional and for demonstration only
