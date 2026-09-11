<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
mbvm_require_admin();

function mbvm_csv_value(mixed $value): string
{
    $text = (string) $value;

    if ($text !== '' && in_array($text[0], ['=', '+', '-', '@'], true)) {
        return "'" . $text;
    }

    return $text;
}

function mbvm_send_csv(string $fileName, array $headers, array $rows): never
{
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('X-Content-Type-Options: nosniff');

    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);

    foreach ($rows as $row) {
        $line = [];
        foreach ($headers as $key) {
            $line[] = mbvm_csv_value($row[$key] ?? '');
        }
        fputcsv($output, $line);
    }

    fclose($output);
    exit;
}

$type = mbvm_clean((string) ($_GET['type'] ?? 'admissions'));
$date = date('Y-m-d');

try {
    if ($type === 'contacts') {
        mbvm_send_csv(
            'mbvm-contact-messages-' . $date . '.csv',
            ['id', 'name', 'email', 'subject', 'message', 'ip_address', 'created_at'],
            mbvm_load_contacts()
        );
    }

    if ($type === 'results') {
        mbvm_send_csv(
            'mbvm-student-results-' . $date . '.csv',
            ['id', 'student_name', 'registration_no', 'class_name', 'session_year', 'roll_no', 'total_marks', 'obtained_marks', 'percentage', 'grade', 'status', 'remarks', 'created_at', 'updated_at'],
            mbvm_load_results()
        );
    }

    mbvm_send_csv(
        'mbvm-admission-enquiries-' . $date . '.csv',
        ['id', 'fullname', 'nationality', 'id_card', 'dob', 'class_name', 'session_year', 'father_name', 'father_mobile', 'mother_name', 'mother_mobile', 'email', 'ip_address', 'created_at'],
        mbvm_load_admissions()
    );
} catch (Throwable $error) {
    http_response_code(500);
    exit('Export failed. Please check database setup.');
}
