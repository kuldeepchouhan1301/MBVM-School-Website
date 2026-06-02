<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
mbvm_require_admin();

$url = getenv('MBVM_SHEETS_WEBHOOK_URL') ?: '';

if ($url === '') {
    http_response_code(500);
    exit('MBVM_SHEETS_WEBHOOK_URL is missing in .env');
}

$payload = json_encode([
    'type' => 'contact',
    'submitted_at' => date('c'),
    'secret' => getenv('MBVM_SHEETS_WEBHOOK_SECRET') ?: '',
    'record' => [
        'name' => 'Webhook Test',
        'email' => 'test@example.com',
        'subject' => 'Google Sheets sync test',
        'message' => 'This row was sent from backend/test_sheets_sync.php.',
    ],
], JSON_UNESCAPED_SLASHES);

if ($payload === false) {
    http_response_code(500);
    exit('Could not build test payload.');
}

$result = mbvm_post_json($url, $payload);

header('Content-Type: application/json');
echo json_encode([
    'success' => $result['ok'],
    'status' => $result['status'],
    'error' => $result['error'],
    'body' => $result['body'],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
