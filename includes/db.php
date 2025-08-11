<?php
// Include configuration file
require_once __DIR__ . '/../config.php';

// Create database connection using configuration constants
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Set charset
$conn->set_charset(DB_CHARSET);

// Check connection
if ($conn->connect_error) {
    if (ENVIRONMENT === 'development') {
        die("Connection failed: " . $conn->connect_error);
    } else {
        die("Database connection failed. Please try again later.");
    }
}

// Create the database if it doesn't exist (for development)
if (ENVIRONMENT === 'development') {
    $createDbQuery = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET " . DB_CHARSET . " COLLATE " . DB_CHARSET . "_unicode_ci";
    $conn->query($createDbQuery);
    $conn->select_db(DB_NAME);
}
?>