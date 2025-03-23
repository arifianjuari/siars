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

    // Query role_module_permissions table
    $stmt = $pdo->query("SELECT role_module_permissions.*, roles.name as role_name, modules.name as module_name 
                         FROM role_module_permissions 
                         JOIN roles ON role_module_permissions.role_id = roles.id
                         JOIN modules ON role_module_permissions.module_id = modules.id");
    $permissions = $stmt->fetchAll();

    // Output result
    echo "Total role module permissions: " . count($permissions) . "\n\n";
    echo "Role Module Permissions data:\n";
    echo json_encode($permissions, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
