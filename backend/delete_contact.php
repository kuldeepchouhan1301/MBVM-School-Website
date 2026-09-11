<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
mbvm_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (mbvm_wants_json()) {
        mbvm_response(false, 'Invalid request method.', 405);
    }

    header('Location: ../dashboard.php');
    exit;
}

mbvm_verify_csrf();

$id = (int) ($_POST['id'] ?? 0);
if ($id < 1) {
    if (mbvm_wants_json()) {
        mbvm_response(false, 'Invalid contact message.', 422);
    }

    header('Location: ../dashboard.php?contact_error=Invalid contact message.');
    exit;
}

try {
    if (!mbvm_delete_contact($id)) {
        throw new RuntimeException('Contact message was not found.');
    }
} catch (Throwable $error) {
    if (mbvm_wants_json()) {
        mbvm_response(false, $error->getMessage(), 500);
    }

    header('Location: ../dashboard.php?' . http_build_query(['contact_error' => $error->getMessage()]));
    exit;
}

if (mbvm_wants_json()) {
    mbvm_response(true, 'Contact message deleted successfully.');
}

header('Location: ../dashboard.php?contact_success=Contact message deleted successfully.');
exit;
