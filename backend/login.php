<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.html');
    exit;
}

$username = mbvm_clean((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

if (hash_equals(mbvm_admin_username(), $username) && mbvm_verify_admin_password($password)) {
    session_regenerate_id(true);
    $_SESSION['mbvm_admin'] = $username;
    header('Location: ../dashboard.php');
    exit;
}

header('Location: ../login.html?error=1');
exit;
