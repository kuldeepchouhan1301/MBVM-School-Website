<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    mbvm_response(false, 'Invalid request method.', 405);
}

if (!empty($_POST['website'] ?? '')) {
    mbvm_response(true, 'Thank you. Your message has been received.');
}

$errors = mbvm_required($_POST, ['name', 'email', 'subject', 'message']);
$email = mbvm_clean((string) ($_POST['email'] ?? ''));

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if ($errors !== []) {
    mbvm_response(false, implode(' ', $errors), 400, '../contact-us.html');
}

try {
    mbvm_save_contact([
        'name' => mbvm_clean((string) $_POST['name']),
        'email' => $email,
        'subject' => mbvm_clean((string) $_POST['subject']),
        'message' => trim((string) $_POST['message']),
    ]);
} catch (Throwable $error) {
    mbvm_response(false, 'Unable to send your message right now. Please try again.', 500, '../contact-us.html');
}

mbvm_response(true, 'Thank you for contacting Madhusudan Bal Vidya Mandir. We have received your message and will get back to you soon.', 200, '../contact-us.html');
