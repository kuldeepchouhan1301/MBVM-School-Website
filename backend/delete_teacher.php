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
        mbvm_response(false, 'Invalid teacher profile.', 422);
    }

    header('Location: ../dashboard.php?teacher_error=Invalid teacher profile.');
    exit;
}

try {
    if (!mbvm_delete_teacher($id)) {
        throw new RuntimeException('Teacher profile was not found.');
    }
} catch (Throwable $error) {
    if (mbvm_wants_json()) {
        mbvm_response(false, $error->getMessage(), 500);
    }

    header('Location: ../dashboard.php?' . http_build_query(['teacher_error' => $error->getMessage()]));
    exit;
}

if (mbvm_wants_json()) {
    mbvm_response(true, 'Teacher profile removed successfully.');
}

header('Location: ../dashboard.php?teacher_success=Teacher profile removed successfully.');
exit;
