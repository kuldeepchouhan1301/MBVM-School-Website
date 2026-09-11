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
$category = mbvm_clean((string) ($_POST['category'] ?? ''));

try {
    $imagePath = mbvm_save_public_upload('gallery_image', 'gallery');
    if ($imagePath === '') {
        throw new RuntimeException('Please choose a gallery image.');
    }

    mbvm_save_gallery_item([
        'title' => $title,
        'category' => $category,
        'image_path' => $imagePath,
    ]);
} catch (Throwable $error) {
    header('Location: ../dashboard.php?' . http_build_query(['gallery_error' => $error->getMessage()]));
    exit;
}

header('Location: ../dashboard.php?gallery_success=Gallery image added successfully.');
exit;
