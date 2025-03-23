<?php

// Define database connection parameters
$host = 'auth-db1151.hstgr.io';
$db   = 'u609399718_siars';
$user = 'u609399718_adminsiars';
$pass = 'Obgin@12345';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Connect to database
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Query modules table
    $stmt = $pdo->query("SELECT * FROM modules");
    $modules = $stmt->fetchAll();

    // Output result
    echo "Total modules: " . count($modules) . "\n\n";
    echo "Modules data:\n";
    echo json_encode($modules, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
