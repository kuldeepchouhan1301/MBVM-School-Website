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
        mbvm_response(false, 'Invalid admission enquiry.', 422);
    }

    header('Location: ../dashboard.php?' . http_build_query(['admission_error' => 'Invalid admission enquiry.']));
    exit;
}

try {
    $deleted = mbvm_delete_admission($id);
} catch (Throwable $error) {
    if (mbvm_wants_json()) {
        mbvm_response(false, 'Admission enquiry could not be deleted.', 500);
    }

    header('Location: ../dashboard.php?' . http_build_query(['admission_error' => 'Admission enquiry could not be deleted.']));
    exit;
}

$message = $deleted ? 'Admission enquiry deleted successfully.' : 'Admission enquiry was not found.';
if (mbvm_wants_json()) {
    mbvm_response($deleted, $message, $deleted ? 200 : 404);
}

header('Location: ../dashboard.php?' . http_build_query([
    $deleted ? 'admission_success' : 'admission_error' => $deleted
        ? 'Admission enquiry deleted successfully.'
        : 'Admission enquiry was not found.',
]));
exit;
