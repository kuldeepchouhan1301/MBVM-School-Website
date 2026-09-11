<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
mbvm_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

mbvm_verify_csrf();

$title = mbvm_clean((string) ($_POST['title'] ?? ''));
$eventDate = mbvm_clean((string) ($_POST['event_date'] ?? ''));
$eventTime = mbvm_clean((string) ($_POST['event_time'] ?? ''));
$description = trim((string) ($_POST['description'] ?? ''));
$youtubeUrl = mbvm_clean((string) ($_POST['youtube_url'] ?? ''));

$errors = mbvm_required($_POST, ['title', 'event_date', 'description']);
if ($youtubeUrl !== '' && !filter_var($youtubeUrl, FILTER_VALIDATE_URL)) {
    $errors[] = 'Please enter a valid YouTube URL.';
}

if ($errors !== []) {
    header('Location: ../dashboard.php?' . http_build_query(['event_error' => implode(' ', $errors)]));
    exit;
}

try {
    $imagePath = mbvm_save_public_upload('event_image', 'event');

    mbvm_save_event([
        'title' => $title,
        'event_date' => $eventDate,
        'event_time' => $eventTime,
        'description' => $description,
        'youtube_url' => $youtubeUrl,
        'image_path' => $imagePath,
    ]);
} catch (Throwable $error) {
    header('Location: ../dashboard.php?' . http_build_query(['event_error' => $error->getMessage()]));
    exit;
}

header('Location: ../dashboard.php?event_success=Event added successfully.');
exit;
