PHP User Authentication & File Upload System

This is a simple user authentication system with CSV/Excel file upload functionality built using PHP and MySQL. It includes user registration, login, session management, and a dashboard for file uploads.

Features

User Registration & Login

Password Hashing for Security

CSV/Excel File Upload & Import

Display Imported Data in a Table

Logout Functionality

Prerequisites

Make sure you have the following installed on your system:

XAMPP (or any other local server with PHP & MySQL)

PHP 7 or higher

MySQL Database

Composer (optional for dependency management)

Setup Instructions

1. Clone the Repository

git clone https://github.com/your-username/your-repository.git
cd your-repository

2. Start XAMPP

Open XAMPP Control Panel and start Apache and MySQL.

3. Create a Database

Open phpMyAdmin (http://localhost/phpmyadmin)

Create a new database named user_system.

Import the database.sql file (if available) to set up the tables.

4. Configure Database Connection

Edit the database.php file and update the database credentials:

$servername = "localhost";
$username = "root";
$password = "";
$database = "user_system";
$conn = new mysqli($servername, $username, $password, $database);

5. Run the Application

Move the project folder to htdocs (C:\xampp\htdocs\your-repository).

Open the browser and visit http://localhost/your-repository/.

6. Register & Login

Register a new user via registration.php.

Log in using the credentials to access the dashboard.

File Structure

/
├── database.php  # Database Connection File
├── index.php     # User Dashboard
├── login.php     # Login Page
├── registration.php  # Registration Page
├── upload.php    # File Upload Script
├── logout.php    # Logout Script
├── style.css     # Stylesheet
└── README.md     # Documentation

Usage

Upload a CSV/Excel file via the dashboard.

View imported data in a tabular format.

Logout anytime to end the session.

License

This project is open-source and free to use under the MIT License.

Made with ❤️ by [Your Name]

