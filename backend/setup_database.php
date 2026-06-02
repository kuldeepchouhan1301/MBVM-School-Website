<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if ((getenv('MBVM_ALLOW_SETUP') ?: '') !== '1') {
    http_response_code(403);
    echo "Database setup is disabled. Set MBVM_ALLOW_SETUP=1 in .env only while running setup, then remove it.\n";
    exit;
}

$host = getenv('MBVM_DB_HOST') ?: MBVM_DB_HOST;
$username = getenv('MBVM_DB_USER') ?: MBVM_DB_USER;
$password = getenv('MBVM_DB_PASS');
$password = $password === false ? MBVM_DB_PASS : $password;
$schema = file_get_contents(__DIR__ . '/schema.sql');

try {
    $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec($schema);
    echo "Database setup completed.\n";
} catch (Throwable $error) {
    http_response_code(500);
    echo "Database setup failed: " . $error->getMessage() . "\n";
}
