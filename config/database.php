<?php
/**
 * Database Configuration
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

$database_file = __DIR__ . '/../database/portfolio.db';

// Create database directory if it doesn't exist
if (!file_exists(dirname($database_file))) {
    mkdir(dirname($database_file), 0755, true);
}

try {
    $pdo = new PDO("sqlite:$database_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>