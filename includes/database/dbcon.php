<?php
try {
    // Directly include database connection parameters
    $dsn = "mysql:host=localhost;dbname=higher_studies";
    $username = "root";
    $password = "";

    // Create a new PDO instance
    $dbh = new PDO($dsn, $username, $password, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
    ));
    
    // Set error mode to exception
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Display a user-friendly error message
    echo "Looks like you don't have any database/connection for this project. Please check your Database Connection and Try Again! </br>";
    // Log the detailed error message
    exit("Error: " . $e->getMessage());
}

