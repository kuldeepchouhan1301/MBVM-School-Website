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
    mbvm_apply_migrations($pdo);
    echo "Database setup completed.\n";
} catch (Throwable $error) {
    http_response_code(500);
    echo "Database setup failed: " . $error->getMessage() . "\n";
}

function mbvm_apply_migrations(PDO $pdo): void
{
    $database = getenv('MBVM_DB_NAME') ?: MBVM_DB_NAME;
    $pdo->exec('USE `' . str_replace('`', '``', $database) . '`');

    if (!mbvm_column_exists($pdo, 'admission_enquiries', 'id_card')) {
        $pdo->exec('ALTER TABLE admission_enquiries ADD COLUMN id_card VARCHAR(255) NOT NULL AFTER nationality');
    }

    if (mbvm_column_exists($pdo, 'admission_enquiries', 'photo')) {
        $pdo->exec('ALTER TABLE admission_enquiries DROP COLUMN photo');
    }
}

function mbvm_column_exists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare(
        'SELECT COUNT(*)
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = :table_name
           AND COLUMN_NAME = :column_name'
    );
    $stmt->execute([
        'table_name' => $table,
        'column_name' => $column,
    ]);

    return (int) $stmt->fetchColumn() > 0;
}
