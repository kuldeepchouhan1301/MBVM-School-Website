<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
mbvm_require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

mbvm_verify_csrf();

$required = [
    'student_name',
    'registration_no',
    'class_name',
    'session_year',
    'total_marks',
    'obtained_marks',
    'grade',
    'status',
];

$errors = mbvm_required($_POST, $required);
$totalMarks = (float) ($_POST['total_marks'] ?? 0);
$obtainedMarks = (float) ($_POST['obtained_marks'] ?? 0);

if ($totalMarks <= 0) {
    $errors[] = 'Total marks must be greater than zero.';
}

if ($obtainedMarks < 0 || $obtainedMarks > $totalMarks) {
    $errors[] = 'Obtained marks must be between 0 and total marks.';
}

if ($errors !== []) {
    header('Location: ../dashboard.php?' . http_build_query(['result_error' => implode(' ', $errors)]));
    exit;
}

$percentage = round(($obtainedMarks / $totalMarks) * 100, 2);

try {
    mbvm_save_result([
        'id' => (int) ($_POST['result_id'] ?? 0),
        'student_name' => mbvm_clean((string) $_POST['student_name']),
        'registration_no' => mbvm_clean((string) $_POST['registration_no']),
        'class_name' => mbvm_clean((string) $_POST['class_name']),
        'session_year' => mbvm_clean((string) $_POST['session_year']),
        'roll_no' => mbvm_clean((string) ($_POST['roll_no'] ?? '')),
        'total_marks' => $totalMarks,
        'obtained_marks' => $obtainedMarks,
        'percentage' => $percentage,
        'grade' => mbvm_clean((string) $_POST['grade']),
        'status' => mbvm_clean((string) $_POST['status']),
        'remarks' => trim((string) ($_POST['remarks'] ?? '')),
    ]);
} catch (Throwable $error) {
    header('Location: ../dashboard.php?' . http_build_query(['result_error' => 'Result could not be saved. Please check database setup.']));
    exit;
}

header('Location: ../dashboard.php?result_success=Result saved successfully.');
exit;
