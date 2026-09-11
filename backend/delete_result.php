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
        mbvm_response(false, 'Invalid result.', 422);
    }

    header('Location: ../dashboard.php?result_error=Invalid result.');
    exit;
}

try {
    if (!mbvm_delete_result($id)) {
        throw new RuntimeException('Result was not found.');
    }
} catch (Throwable $error) {
    if (mbvm_wants_json()) {
        mbvm_response(false, $error->getMessage(), 500);
    }

    header('Location: ../dashboard.php?' . http_build_query(['result_error' => $error->getMessage()]));
    exit;
}

if (mbvm_wants_json()) {
    mbvm_response(true, 'Result deleted successfully.');
}

header('Location: ../dashboard.php?result_success=Result deleted successfully.');
exit;
