User Authentication and File Upload System

This project is a user authentication and file upload system built using PHP and MySQL. It includes user registration, login, session management, CSV/Excel file upload, and data display.

Features

User Registration and Login System

Secure Password Hashing

Session Management

CSV/Excel File Upload and Import

Display Imported Data in a Table

Logout Functionality

Prerequisites

Ensure you have the following installed:

XAMPP (for Apache, MySQL, and PHP)

PHP (version 7.4 or later)

MySQL Database

Git (for version control)

Installation and Setup

Step 1: Clone the Repository

git clone https://github.com/your-username/your-repo.git
cd your-repo

Step 2: Setup XAMPP

Start Apache and MySQL in the XAMPP Control Panel.

Open phpMyAdmin by visiting: http://localhost/phpmyadmin

Create a new database named user_system.

Import the provided database.sql file to create the necessary tables.

Step 3: Configure Database

Open database.php.

Update the database credentials:

$host = "localhost";
$user = "root";
$password = ""; // Leave blank for default XAMPP setup
$database = "user_system";
$conn = mysqli_connect($host, $user, $password, $database);

Step 4: Run the Project

Move the project folder to the XAMPP htdocs directory.

Open a browser and visit:

http://localhost/your-repo/login.php

Register a new user and log in.

Upload a CSV/Excel file and check the imported data.

Step 5: Push to GitHub (if needed)

git add .
git commit -m "Initial commit"
git push origin main

Usage

Register or login using your credentials.

Upload CSV/Excel files.

View imported data in a table.

Logout when finished.

License

This project is open-source and available under the MIT License.

Contact

For any queries, contact vineet somani.

