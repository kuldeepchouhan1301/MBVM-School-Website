<?php
declare(strict_types=1);

const MBVM_DATA_DIR = __DIR__ . '/storage';
const MBVM_UPLOAD_DIR = MBVM_DATA_DIR . '/uploads/admissions';
const MBVM_PUBLIC_UPLOAD_DIR = __DIR__ . '/../frontend/uploads/admin';
const MBVM_SESSION_DIR = MBVM_DATA_DIR . '/sessions';
const MBVM_DB_HOST = '127.0.0.1';
const MBVM_DB_NAME = 'mbvm_school';
const MBVM_DB_USER = 'root';
const MBVM_DB_PASS = '';

function mbvm_load_env(): void
{
    $path = __DIR__ . '/../.env';
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if ($key !== '' && getenv($key) === false) {
            putenv($key . '=' . $value);
            $_ENV[$key] = $value;
        }
    }
}

mbvm_load_env();

if (!is_dir(MBVM_SESSION_DIR)) {
    mkdir(MBVM_SESSION_DIR, 0755, true);
}

session_save_path(MBVM_SESSION_DIR);
session_start();

function mbvm_ensure_storage(): void
{
    foreach ([MBVM_DATA_DIR, MBVM_UPLOAD_DIR, MBVM_PUBLIC_UPLOAD_DIR, MBVM_SESSION_DIR] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

function mbvm_clean(string $value): string
{
    return trim(str_replace(["\r", "\n"], ' ', $value));
}

function mbvm_required(array $source, array $fields): array
{
    $errors = [];

    foreach ($fields as $field) {
        if (!isset($source[$field]) || mbvm_clean((string) $source[$field]) === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }

    return $errors;
}

function mbvm_save_record(string $fileName, array $record): void
{
    mbvm_ensure_storage();

    $record['submitted_at'] = date('c');
    $record['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    file_put_contents(
        MBVM_DATA_DIR . '/' . $fileName,
        json_encode($record, JSON_UNESCAPED_SLASHES) . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

function mbvm_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('MBVM_DB_HOST') ?: MBVM_DB_HOST;
    $database = getenv('MBVM_DB_NAME') ?: MBVM_DB_NAME;
    $username = getenv('MBVM_DB_USER') ?: MBVM_DB_USER;
    $password = getenv('MBVM_DB_PASS');
    $password = $password === false ? MBVM_DB_PASS : $password;

    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    return $pdo;
}

function mbvm_db_ready(): bool
{
    try {
        mbvm_db();
        return true;
    } catch (Throwable $error) {
        return false;
    }
}

function mbvm_save_contact(array $record): void
{
    $stmt = mbvm_db()->prepare(
        'INSERT INTO contact_enquiries (name, email, subject, message, ip_address)
         VALUES (:name, :email, :subject, :message, :ip_address)'
    );

    $stmt->execute([
        'name' => $record['name'],
        'email' => $record['email'],
        'subject' => $record['subject'],
        'message' => $record['message'],
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    ]);

    mbvm_sync_sheet('contact', $record);
}

function mbvm_save_admission(array $record): void
{
    $stmt = mbvm_db()->prepare(
        'INSERT INTO admission_enquiries
        (fullname, nationality, id_card, dob, class_name, session_year, father_name, father_mobile, mother_name, mother_mobile, email, ip_address)
        VALUES
        (:fullname, :nationality, :id_card, :dob, :class_name, :session_year, :father_name, :father_mobile, :mother_name, :mother_mobile, :email, :ip_address)'
    );

    $stmt->execute([
        'fullname' => $record['fullname'],
        'nationality' => $record['nationality'],
        'id_card' => $record['id_card'],
        'dob' => $record['dob'],
        'class_name' => $record['class'],
        'session_year' => $record['session'],
        'father_name' => $record['father_name'],
        'father_mobile' => $record['father_mobile'],
        'mother_name' => $record['mother_name'],
        'mother_mobile' => $record['mother_mobile'],
        'email' => $record['email'],
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    ]);

    mbvm_sync_sheet('admission', $record);
}

function mbvm_load_contacts(): array
{
    return mbvm_db()
        ->query('SELECT * FROM contact_enquiries ORDER BY created_at DESC')
        ->fetchAll();
}

function mbvm_delete_contact(int $id): bool
{
    $stmt = mbvm_db()->prepare('DELETE FROM contact_enquiries WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
}

function mbvm_load_admissions(): array
{
    return mbvm_db()
        ->query('SELECT * FROM admission_enquiries ORDER BY created_at DESC')
        ->fetchAll();
}

function mbvm_delete_admission(int $id): bool
{
    $pdo = mbvm_db();
    $stmt = $pdo->prepare('SELECT id_card FROM admission_enquiries WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $record = $stmt->fetch();

    if (!is_array($record)) {
        return false;
    }

    $delete = $pdo->prepare('DELETE FROM admission_enquiries WHERE id = :id');
    $delete->execute(['id' => $id]);

    if ($delete->rowCount() < 1) {
        return false;
    }

    $fileName = basename((string) ($record['id_card'] ?? ''));
    if ($fileName !== '') {
        $path = MBVM_UPLOAD_DIR . '/' . $fileName;
        $realPath = realpath($path);
        $uploadRoot = realpath(MBVM_UPLOAD_DIR);

        if ($realPath !== false && $uploadRoot !== false && strpos($realPath, $uploadRoot . DIRECTORY_SEPARATOR) === 0 && is_file($realPath)) {
            unlink($realPath);
        }
    }

    return true;
}

function mbvm_save_result(array $record): void
{
    $id = (int) ($record['id'] ?? 0);

    if ($id > 0) {
        $stmt = mbvm_db()->prepare(
            'UPDATE student_results
             SET student_name = :student_name,
                 registration_no = :registration_no,
                 class_name = :class_name,
                 session_year = :session_year,
                 roll_no = :roll_no,
                 total_marks = :total_marks,
                 obtained_marks = :obtained_marks,
                 percentage = :percentage,
                 grade = :grade,
                 status = :status,
                 remarks = :remarks
             WHERE id = :id'
        );

        $params = [
            'id' => $id,
            'student_name' => $record['student_name'],
            'registration_no' => $record['registration_no'],
            'class_name' => $record['class_name'],
            'session_year' => $record['session_year'],
            'roll_no' => $record['roll_no'],
            'total_marks' => $record['total_marks'],
            'obtained_marks' => $record['obtained_marks'],
            'percentage' => $record['percentage'],
            'grade' => $record['grade'],
            'status' => $record['status'],
            'remarks' => $record['remarks'],
        ];

        $stmt->execute($params);
        if ($stmt->rowCount() < 1) {
            $check = mbvm_db()->prepare('SELECT id FROM student_results WHERE id = :id LIMIT 1');
            $check->execute(['id' => $id]);
            if (!$check->fetch()) {
                throw new RuntimeException('Result was not found.');
            }
        }

        mbvm_sync_sheet('result', $record);
        return;
    }

    $stmt = mbvm_db()->prepare(
        'INSERT INTO student_results
        (student_name, registration_no, class_name, session_year, roll_no, total_marks, obtained_marks, percentage, grade, status, remarks)
        VALUES
        (:student_name, :registration_no, :class_name, :session_year, :roll_no, :total_marks, :obtained_marks, :percentage, :grade, :status, :remarks)
        ON DUPLICATE KEY UPDATE
            student_name = VALUES(student_name),
            roll_no = VALUES(roll_no),
            total_marks = VALUES(total_marks),
            obtained_marks = VALUES(obtained_marks),
            percentage = VALUES(percentage),
            grade = VALUES(grade),
            status = VALUES(status),
            remarks = VALUES(remarks)'
    );

    $stmt->execute([
        'student_name' => $record['student_name'],
        'registration_no' => $record['registration_no'],
        'class_name' => $record['class_name'],
        'session_year' => $record['session_year'],
        'roll_no' => $record['roll_no'],
        'total_marks' => $record['total_marks'],
        'obtained_marks' => $record['obtained_marks'],
        'percentage' => $record['percentage'],
        'grade' => $record['grade'],
        'status' => $record['status'],
        'remarks' => $record['remarks'],
    ]);

    mbvm_sync_sheet('result', $record);
}

function mbvm_sync_sheet(string $type, array $record): void
{
    $url = getenv('MBVM_SHEETS_WEBHOOK_URL') ?: '';

    if ($url === '') {
        return;
    }

    $payloadData = [
        'type' => $type,
        'submitted_at' => date('c'),
        'secret' => getenv('MBVM_SHEETS_WEBHOOK_SECRET') ?: '',
        'record' => $record,
    ];

    if ($type === 'admission') {
        $file = mbvm_sheet_file_payload((string) ($record['id_card'] ?? ''));
        if ($file !== null) {
            $payloadData['file'] = $file;
        }
    }

    $payload = json_encode($payloadData, JSON_UNESCAPED_SLASHES);

    if ($payload === false) {
        return;
    }

    mbvm_post_json($url, $payload);
}

function mbvm_post_json(string $url, string $payload): array
{
    if (function_exists('curl_init')) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 20,
        ]);

        $body = curl_exec($curl);
        $error = curl_error($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        $result = [
            'ok' => $body !== false && $status >= 200 && $status < 300,
            'status' => $status,
            'body' => is_string($body) ? $body : '',
            'error' => $error,
        ];

        mbvm_log_sheet_sync($result);

        return $result;
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => $payload,
            'timeout' => 20,
            'ignore_errors' => true,
        ],
    ]);

    $body = @file_get_contents($url, false, $context);
    $statusLine = $http_response_header[0] ?? '';
    preg_match('/\s(\d{3})\s/', $statusLine, $match);
    $status = isset($match[1]) ? (int) $match[1] : 0;
    $lastError = error_get_last();

    $result = [
        'ok' => $body !== false && $status >= 200 && $status < 300,
        'status' => $status,
        'body' => is_string($body) ? $body : '',
        'error' => $body === false ? (string) ($lastError['message'] ?? 'Request failed') : '',
    ];

    mbvm_log_sheet_sync($result);

    return $result;
}

function mbvm_log_sheet_sync(array $result): void
{
    mbvm_ensure_storage();

    $line = json_encode([
        'time' => date('c'),
        'ok' => $result['ok'] ?? false,
        'status' => $result['status'] ?? 0,
        'error' => $result['error'] ?? '',
        'body' => mb_substr((string) ($result['body'] ?? ''), 0, 500),
    ], JSON_UNESCAPED_SLASHES);

    if ($line !== false) {
        file_put_contents(MBVM_DATA_DIR . '/sheets-sync.log', $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function mbvm_sheet_file_payload(string $storedPath): ?array
{
    $fileName = basename($storedPath);

    if ($fileName === '') {
        return null;
    }

    $path = MBVM_UPLOAD_DIR . '/' . $fileName;
    $realPath = realpath($path);
    $uploadRoot = realpath(MBVM_UPLOAD_DIR);

    if ($realPath === false || $uploadRoot === false || strpos($realPath, $uploadRoot . DIRECTORY_SEPARATOR) !== 0 || !is_file($realPath)) {
        return null;
    }

    $contents = file_get_contents($realPath);
    if ($contents === false) {
        return null;
    }

    return [
        'name' => $fileName,
        'mime_type' => mime_content_type($realPath) ?: 'application/octet-stream',
        'contents_base64' => base64_encode($contents),
    ];
}

function mbvm_load_results(): array
{
    return mbvm_db()
        ->query('SELECT * FROM student_results ORDER BY updated_at DESC, created_at DESC')
        ->fetchAll();
}

function mbvm_delete_result(int $id): bool
{
    $stmt = mbvm_db()->prepare('DELETE FROM student_results WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
}

function mbvm_save_event(array $record): void
{
    $stmt = mbvm_db()->prepare(
        'INSERT INTO school_events (title, event_date, event_time, description, youtube_url, image_path)
         VALUES (:title, :event_date, :event_time, :description, :youtube_url, :image_path)'
    );

    $stmt->execute([
        'title' => $record['title'],
        'event_date' => $record['event_date'],
        'event_time' => $record['event_time'],
        'description' => $record['description'],
        'youtube_url' => $record['youtube_url'],
        'image_path' => $record['image_path'],
    ]);
}

function mbvm_load_events(): array
{
    return mbvm_db()
        ->query('SELECT * FROM school_events ORDER BY event_date DESC, created_at DESC')
        ->fetchAll();
}

function mbvm_delete_event(int $id): bool
{
    $pdo = mbvm_db();
    $stmt = $pdo->prepare('SELECT image_path FROM school_events WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $record = $stmt->fetch();

    if (!is_array($record)) {
        return false;
    }

    $delete = $pdo->prepare('DELETE FROM school_events WHERE id = :id');
    $delete->execute(['id' => $id]);

    if ($delete->rowCount() < 1) {
        return false;
    }

    mbvm_delete_public_upload((string) ($record['image_path'] ?? ''));
    return true;
}

function mbvm_save_gallery_item(array $record): void
{
    $stmt = mbvm_db()->prepare(
        'INSERT INTO gallery_items (title, category, image_path)
         VALUES (:title, :category, :image_path)'
    );

    $stmt->execute([
        'title' => $record['title'],
        'category' => $record['category'],
        'image_path' => $record['image_path'],
    ]);
}

function mbvm_load_gallery_items(): array
{
    return mbvm_db()
        ->query('SELECT * FROM gallery_items ORDER BY created_at DESC')
        ->fetchAll();
}

function mbvm_delete_gallery_item(int $id): bool
{
    $pdo = mbvm_db();
    $stmt = $pdo->prepare('SELECT image_path FROM gallery_items WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $record = $stmt->fetch();

    if (!is_array($record)) {
        return false;
    }

    $delete = $pdo->prepare('DELETE FROM gallery_items WHERE id = :id');
    $delete->execute(['id' => $id]);

    if ($delete->rowCount() < 1) {
        return false;
    }

    mbvm_delete_public_upload((string) ($record['image_path'] ?? ''));
    return true;
}

function mbvm_load_teachers(bool $activeOnly = false): array
{
    $sql = 'SELECT * FROM teacher_profiles';
    if ($activeOnly) {
        $sql .= ' WHERE is_active = 1';
    }

    return mbvm_db()
        ->query($sql . ' ORDER BY sort_order ASC, name ASC, id ASC')
        ->fetchAll();
}

function mbvm_find_teacher(int $id): ?array
{
    $stmt = mbvm_db()->prepare('SELECT * FROM teacher_profiles WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $teacher = $stmt->fetch();

    return is_array($teacher) ? $teacher : null;
}

function mbvm_save_teacher(array $record): void
{
    $id = (int) ($record['id'] ?? 0);
    $params = [
        'name' => $record['name'],
        'designation' => $record['designation'],
        'subject' => $record['subject'],
        'qualification' => $record['qualification'],
        'bio' => $record['bio'],
        'sort_order' => $record['sort_order'],
        'is_active' => $record['is_active'],
    ];

    if ($id > 0) {
        $teacher = mbvm_find_teacher($id);
        if ($teacher === null) {
            throw new RuntimeException('Teacher profile was not found.');
        }

        $params['id'] = $id;
        $params['photo_path'] = $record['photo_path'] !== '' ? $record['photo_path'] : (string) ($teacher['photo_path'] ?? '');

        $stmt = mbvm_db()->prepare(
            'UPDATE teacher_profiles
             SET name = :name,
                 designation = :designation,
                 subject = :subject,
                 qualification = :qualification,
                 bio = :bio,
                 photo_path = :photo_path,
                 sort_order = :sort_order,
                 is_active = :is_active
             WHERE id = :id'
        );
        $stmt->execute($params);
        return;
    }

    $params['photo_path'] = $record['photo_path'] !== '' ? $record['photo_path'] : '/frontend/uploads/teacher/210x220-img-1.jpg';

    $stmt = mbvm_db()->prepare(
        'INSERT INTO teacher_profiles
        (name, designation, subject, qualification, bio, photo_path, sort_order, is_active)
        VALUES
        (:name, :designation, :subject, :qualification, :bio, :photo_path, :sort_order, :is_active)'
    );
    $stmt->execute($params);
}

function mbvm_delete_teacher(int $id): bool
{
    $stmt = mbvm_db()->prepare('DELETE FROM teacher_profiles WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return $stmt->rowCount() > 0;
}

function mbvm_save_public_upload(string $fieldName, string $prefix): string
{
    mbvm_ensure_storage();

    if (empty($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        return '';
    }

    $file = $_FILES[$fieldName];
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed. Please try again.');
    }

    if ((int) ($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('Image must be 5MB or smaller.');
    }

    $tmpName = (string) ($file['tmp_name'] ?? '');
    $imageInfo = @getimagesize($tmpName);
    if ($imageInfo === false) {
        throw new RuntimeException('Please upload a valid image file.');
    }

    $allowed = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];
    $type = (int) ($imageInfo[2] ?? 0);
    if (!isset($allowed[$type])) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are allowed.');
    }

    $fileName = $prefix . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$type];
    $targetPath = MBVM_PUBLIC_UPLOAD_DIR . '/' . $fileName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        throw new RuntimeException('Image could not be saved.');
    }

    return '/frontend/uploads/admin/' . $fileName;
}

function mbvm_delete_public_upload(string $publicPath): void
{
    $fileName = basename($publicPath);
    if ($fileName === '') {
        return;
    }

    $path = MBVM_PUBLIC_UPLOAD_DIR . '/' . $fileName;
    $realPath = realpath($path);
    $uploadRoot = realpath(MBVM_PUBLIC_UPLOAD_DIR);

    if ($realPath !== false && $uploadRoot !== false && strpos($realPath, $uploadRoot . DIRECTORY_SEPARATOR) === 0 && is_file($realPath)) {
        unlink($realPath);
    }
}

function mbvm_find_result(string $className, string $sessionYear, string $registrationNo): ?array
{
    $stmt = mbvm_db()->prepare(
        'SELECT * FROM student_results
         WHERE class_name = :class_name
           AND session_year = :session_year
           AND registration_no = :registration_no
         LIMIT 1'
    );

    $stmt->execute([
        'class_name' => $className,
        'session_year' => $sessionYear,
        'registration_no' => $registrationNo,
    ]);

    $result = $stmt->fetch();

    return is_array($result) ? $result : null;
}

function mbvm_wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

    return strpos($accept, 'application/json') !== false || strtolower($requestedWith) === 'xmlhttprequest';
}

function mbvm_csrf_token(): string
{
    if (empty($_SESSION['mbvm_csrf_token']) || !is_string($_SESSION['mbvm_csrf_token'])) {
        $_SESSION['mbvm_csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['mbvm_csrf_token'];
}

function mbvm_csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars(mbvm_csrf_token(), ENT_QUOTES, 'UTF-8') .
        '">';
}

function mbvm_verify_csrf(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');

    if ($token === '' || empty($_SESSION['mbvm_csrf_token']) || !hash_equals((string) $_SESSION['mbvm_csrf_token'], $token)) {
        http_response_code(403);
        exit('Invalid security token. Please go back, refresh the page, and try again.');
    }
}

function mbvm_response(bool $success, string $message, int $status = 200, ?string $redirect = null): never
{
    http_response_code($status);

    if (mbvm_wants_json()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
        exit;
    }

    if ($redirect !== null) {
        header('Location: ' . $redirect . '?' . http_build_query([
            $success ? 'success' : 'error' => $message,
        ]));
        exit;
    }

    $title = $success ? 'Success' : 'Error';
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    echo "<!doctype html><html><head><meta charset=\"utf-8\"><title>{$title}</title></head><body>";
    echo "<p>{$safeMessage}</p><p><a href=\"../index.html\">Back to website</a></p>";
    echo '</body></html>';
    exit;
}

function mbvm_admin_username(): string
{
    return getenv('MBVM_ADMIN_USERNAME') ?: 'admin';
}

function mbvm_admin_password(): string
{
    return getenv('MBVM_ADMIN_PASSWORD') ?: 'admin123';
}

function mbvm_admin_password_hash(): string
{
    return getenv('MBVM_ADMIN_PASSWORD_HASH') ?: '';
}

function mbvm_verify_admin_password(string $password): bool
{
    $hash = mbvm_admin_password_hash();

    if ($hash !== '') {
        return password_verify($password, $hash);
    }

    return hash_equals(mbvm_admin_password(), $password);
}

function mbvm_require_admin(): void
{
    if (empty($_SESSION['mbvm_admin'])) {
        header('Location: login.html');
        exit;
    }
}

function mbvm_load_records(string $fileName): array
{
    $path = MBVM_DATA_DIR . '/' . $fileName;

    if (!is_file($path)) {
        return [];
    }

    $records = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $record = json_decode($line, true);
        if (is_array($record)) {
            $records[] = $record;
        }
    }

    return array_reverse($records);
}
