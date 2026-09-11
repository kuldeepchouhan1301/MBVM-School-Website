<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
mbvm_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

mbvm_verify_csrf();

$id = (int) ($_POST['id'] ?? 0);
$name = mbvm_clean((string) ($_POST['name'] ?? ''));
$designation = mbvm_clean((string) ($_POST['designation'] ?? ''));
$subject = mbvm_clean((string) ($_POST['subject'] ?? ''));
$qualification = mbvm_clean((string) ($_POST['qualification'] ?? ''));
$bio = trim((string) ($_POST['bio'] ?? ''));
$sortOrder = (int) ($_POST['sort_order'] ?? 0);
$isActive = isset($_POST['is_active']) ? 1 : 0;

$errors = mbvm_required($_POST, ['name', 'designation']);
if ($errors !== []) {
    header('Location: ../dashboard.php?' . http_build_query(['teacher_error' => implode(' ', $errors)]));
    exit;
}

try {
    $photoPath = mbvm_save_public_upload('teacher_photo', 'teacher');

    mbvm_save_teacher([
        'id' => $id,
        'name' => $name,
        'designation' => $designation,
        'subject' => $subject,
        'qualification' => $qualification,
        'bio' => $bio,
        'photo_path' => $photoPath,
        'sort_order' => $sortOrder,
        'is_active' => $isActive,
    ]);
} catch (Throwable $error) {
    header('Location: ../dashboard.php?' . http_build_query(['teacher_error' => $error->getMessage()]));
    exit;
}

$message = $id > 0 ? 'Teacher profile updated successfully.' : 'Teacher profile added successfully.';
header('Location: ../dashboard.php?' . http_build_query(['teacher_success' => $message]));
exit;
