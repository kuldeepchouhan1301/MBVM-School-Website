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
        mbvm_response(false, 'Invalid event.', 422);
    }

    header('Location: ../dashboard.php?event_error=Invalid event.');
    exit;
}

try {
    if (!mbvm_delete_event($id)) {
        throw new RuntimeException('Event was not found.');
    }
} catch (Throwable $error) {
    if (mbvm_wants_json()) {
        mbvm_response(false, $error->getMessage(), 500);
    }

    header('Location: ../dashboard.php?' . http_build_query(['event_error' => $error->getMessage()]));
    exit;
}

if (mbvm_wants_json()) {
    mbvm_response(true, 'Event deleted successfully.');
}

header('Location: ../dashboard.php?event_success=Event deleted successfully.');
exit;
