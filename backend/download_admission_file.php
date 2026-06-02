<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
mbvm_require_admin();

$fileName = basename((string) ($_GET['file'] ?? ''));

if ($fileName === '') {
    http_response_code(404);
    exit('File not found.');
}

$path = MBVM_UPLOAD_DIR . '/' . $fileName;
$realPath = realpath($path);
$uploadRoot = realpath(MBVM_UPLOAD_DIR);

if ($realPath === false || $uploadRoot === false || strpos($realPath, $uploadRoot . DIRECTORY_SEPARATOR) !== 0 || !is_file($realPath)) {
    http_response_code(404);
    exit('File not found.');
}

$mimeType = mime_content_type($realPath) ?: 'application/octet-stream';

header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . rawurlencode($fileName) . '"');
header('Content-Length: ' . (string) filesize($realPath));
header('X-Content-Type-Options: nosniff');

readfile($realPath);
exit;
