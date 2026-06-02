<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
mbvm_require_admin();

$checks = [
    'php_version' => [
        'label' => 'PHP 8.0+',
        'ok' => version_compare(PHP_VERSION, '8.0.0', '>='),
        'detail' => PHP_VERSION,
    ],
    'database' => [
        'label' => 'MySQL connection',
        'ok' => false,
        'detail' => '',
    ],
    'storage' => [
        'label' => 'Private storage writable',
        'ok' => is_writable(MBVM_DATA_DIR),
        'detail' => MBVM_DATA_DIR,
    ],
    'admission_uploads' => [
        'label' => 'Admission uploads writable',
        'ok' => is_writable(MBVM_UPLOAD_DIR),
        'detail' => MBVM_UPLOAD_DIR,
    ],
    'public_uploads' => [
        'label' => 'Public uploads writable',
        'ok' => is_writable(MBVM_PUBLIC_UPLOAD_DIR),
        'detail' => MBVM_PUBLIC_UPLOAD_DIR,
    ],
    'setup_locked' => [
        'label' => 'Setup endpoint locked',
        'ok' => (getenv('MBVM_ALLOW_SETUP') ?: '') !== '1',
        'detail' => 'MBVM_ALLOW_SETUP',
    ],
    'admin_password' => [
        'label' => 'Admin password hash configured',
        'ok' => mbvm_admin_password_hash() !== '',
        'detail' => 'MBVM_ADMIN_PASSWORD_HASH',
    ],
];

try {
    mbvm_db()->query('SELECT 1');
    $checks['database']['ok'] = true;
    $checks['database']['detail'] = 'Connected';
} catch (Throwable $error) {
    $checks['database']['detail'] = $error->getMessage();
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MBVM | Hosting Health</title>
    <link rel="shortcut icon" href="/frontend/img/favicon.png">
    <link rel="stylesheet" href="/frontend/css/libs.css">
    <link rel="stylesheet" href="/frontend/css/modern.css">
    <style>
        .health-wrap { max-width: 920px; margin: 42px auto; padding: 0 18px; }
        .health-head { margin-bottom: 22px; }
        .health-list { display: grid; gap: 12px; }
        .health-item { display: grid; grid-template-columns: 1fr auto; gap: 14px; align-items: center; padding: 16px; border: 1px solid var(--mbvm-line); border-radius: 8px; background: #fff; }
        .health-item strong { display: block; color: var(--mbvm-text); }
        .health-item span { color: var(--mbvm-muted); word-break: break-word; }
        .health-status { padding: 5px 10px; border-radius: 999px; color: #fff; font-weight: 700; }
        .health-status.ok { background: var(--mbvm-success); }
        .health-status.fail { background: var(--mbvm-danger); }
    </style>
</head>
<body>
    <main class="health-wrap">
        <div class="health-head">
            <h1>Hosting Health Check</h1>
            <p>Use this after upload to confirm the server, database, security settings, and writable folders are ready.</p>
            <p><a href="../dashboard.php" class="cws-button bt-color-3 border-radius">Back to Dashboard</a></p>
        </div>
        <div class="health-list">
            <?php foreach ($checks as $check): ?>
                <div class="health-item">
                    <div>
                        <strong><?= e((string) $check['label']) ?></strong>
                        <span><?= e((string) $check['detail']) ?></span>
                    </div>
                    <div class="health-status <?= $check['ok'] ? 'ok' : 'fail' ?>">
                        <?= $check['ok'] ? 'OK' : 'Fix' ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
