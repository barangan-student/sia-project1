<?php
// src/db.php

// Define the path to the SQLite database file.
// The path is relative to the project root.
$db_path = __DIR__ . '/../database/barangan_friends.db';

try {
    // Create a new PDO instance to connect to the SQLite database.
    // The 'new PDO(...)' constructor will create the database file if it doesn't exist.
    $pdo = new PDO('sqlite:' . $db_path);

    // Set the PDO error mode to exception to catch and handle errors gracefully.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL statement to create the 'barangan_friends' table if it doesn't already exist.
    // This schema is based on the PRD.
    $create_table_query = "
    CREATE TABLE IF NOT EXISTS barangan_friends (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        phone TEXT UNIQUE,
        url TEXT
    );";

    // Execute the SQL query to create the table.
    $pdo->exec($create_table_query);

} catch (PDOException $e) {
    // In a real-world application, you would log this error.
    // For this project, we will suppress the detailed error message from being sent to the user,
    // as per the security requirements.
    // You could set a generic error message here if needed.
    // For example: header('HTTP/1.1 500 Internal Server Error'); echo "Database error.";
    die("Database connection failed. Please check the server configuration.");
}
?>
