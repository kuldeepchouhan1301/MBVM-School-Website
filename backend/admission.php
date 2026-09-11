<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    mbvm_response(false, 'Invalid request method.', 405);
}

if (!empty($_POST['_honey'] ?? '')) {
    mbvm_response(true, 'Thank you. Your admission enquiry has been received.');
}

$required = [
    'fullname',
    'nationality',
    'dob',
    'class',
    'session',
    'fatherName',
    'fatherNameNumber',
    'motherName',
    'motherNameNumber',
    'email',
];

$errors = mbvm_required($_POST, $required);
$email = mbvm_clean((string) ($_POST['email'] ?? ''));

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid parent email address.';
}

$idCardPath = '';
if (!isset($_FILES['id_card']) || $_FILES['id_card']['error'] === UPLOAD_ERR_NO_FILE) {
    $errors[] = 'ID card file is required.';
} elseif ($_FILES['id_card']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'ID card file could not be uploaded.';
} else {
    $maxBytes = 5 * 1024 * 1024;
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'application/pdf' => 'pdf',
    ];

    if ($_FILES['id_card']['size'] > $maxBytes) {
        $errors[] = 'ID card file must be 5MB or smaller.';
    }

    $mimeType = mime_content_type($_FILES['id_card']['tmp_name']);
    if (!isset($allowedTypes[$mimeType])) {
        $errors[] = 'ID card file must be a JPG, PNG, WebP, or PDF file.';
    }

    if ($errors === []) {
        mbvm_ensure_storage();
        $fileName = date('YmdHis') . '-' . bin2hex(random_bytes(6)) . '.' . $allowedTypes[$mimeType];
        $target = MBVM_UPLOAD_DIR . '/' . $fileName;

        if (!move_uploaded_file($_FILES['id_card']['tmp_name'], $target)) {
            $errors[] = 'ID card file could not be saved.';
        } else {
            $idCardPath = 'backend/storage/uploads/admissions/' . $fileName;
        }
    }
}

if ($errors !== []) {
    mbvm_response(false, implode(' ', $errors), 422, '../admission.html');
}

try {
    mbvm_save_admission([
        'fullname' => mbvm_clean((string) $_POST['fullname']),
        'nationality' => mbvm_clean((string) $_POST['nationality']),
        'dob' => mbvm_clean((string) $_POST['dob']),
        'class' => mbvm_clean((string) $_POST['class']),
        'session' => mbvm_clean((string) $_POST['session']),
        'father_name' => mbvm_clean((string) $_POST['fatherName']),
        'father_mobile' => mbvm_clean((string) $_POST['fatherNameNumber']),
        'mother_name' => mbvm_clean((string) $_POST['motherName']),
        'mother_mobile' => mbvm_clean((string) $_POST['motherNameNumber']),
        'email' => $email,
        'id_card' => $idCardPath,
    ]);
} catch (Throwable $error) {
    mbvm_response(false, 'Database is not ready. Please run backend/schema.sql in phpMyAdmin.', 500, '../admission.html');
}

mbvm_response(true, 'Thank you. Your admission enquiry has been received.', 200, '../admission.html');
