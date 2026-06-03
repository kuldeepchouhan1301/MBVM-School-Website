<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';
mbvm_require_admin();

$dbError = '';
$contacts = [];
$admissions = [];
$results = [];
$events = [];
$galleryItems = [];
$teachers = [];
$editingTeacher = null;

try {
    $contacts = mbvm_load_contacts();
    $admissions = mbvm_load_admissions();
    $results = mbvm_load_results();
    $events = mbvm_load_events();
    $galleryItems = mbvm_load_gallery_items();
    $teachers = mbvm_load_teachers();
    if (!empty($_GET['edit_teacher'])) {
        $editingTeacher = mbvm_find_teacher((int) $_GET['edit_teacher']);
    }
} catch (Throwable $error) {
    $dbError = 'Database is not ready. Run backend/schema.sql in phpMyAdmin, or open backend/setup_database.php once.';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
    <title>MBVM | Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="description" content="School Website.">
    <meta name="keywords" content="school,college,management,result,exam,attendace,hostel,admission,events">
    <meta name="author" content="H.R.Shadhin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

    <link rel="shortcut icon" href="/frontend/img/favicon.png">
    <link rel="stylesheet" href="/frontend/css/libs.css">
    <link rel="stylesheet" href="/frontend/css/modern.css">
    <style>
        .dashboard-wrap {
            padding: 55px 0;
        }

        .dashboard-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .dashboard-toolbar h2 {
            margin: 0;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .dashboard-stat {
            background: #fff;
            border: 1px solid #e8edf2;
            padding: 22px;
        }

        .dashboard-stat strong {
            display: block;
            color: #43a9d8;
            font-size: 34px;
            line-height: 1;
            margin-bottom: 8px;
        }

        .dashboard-section {
            margin-bottom: 42px;
        }

        .dashboard-form {
            background: #fff;
            border: 1px solid #e8edf2;
            padding: 24px;
            margin-bottom: 28px;
        }

        .dashboard-form-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .dashboard-form label {
            display: block;
            margin-bottom: 7px;
            color: #555;
            font-weight: 600;
        }

        .dashboard-form input,
        .dashboard-form select,
        .dashboard-form textarea {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ddd;
            padding: 11px 12px;
            background: #fff;
        }

        .dashboard-form input,
        .dashboard-form select {
            min-height: 46px;
            line-height: 1.25;
        }

        .dashboard-form select {
            appearance: auto;
            cursor: pointer;
        }

        .dashboard-form textarea {
            min-height: 92px;
            resize: vertical;
        }

        .dashboard-form .full-field {
            grid-column: 1 / -1;
        }

        #result-form .dashboard-form-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            align-items: end;
        }

        #result-form .full-field {
            align-self: stretch;
        }

        .dashboard-message {
            margin-bottom: 18px;
            padding: 12px 14px;
            border: 1px solid #b7eb8f;
            background: #f6ffed;
            color: #237804;
        }

        .dashboard-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px;
        }

        .dashboard-export-links {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .dashboard-export-links .cws-button {
            padding: 8px 13px;
            font-size: 13px;
            line-height: 1.2;
        }

        .delete-btn {
            border: 0;
            background: #d92d20;
            color: #fff;
            cursor: pointer;
            padding: 8px 13px;
        }

        .delete-btn:hover {
            background: #b42318;
        }

        .inline-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .inline-actions form {
            margin: 0;
        }

        .teacher-thumb {
            width: 58px;
            height: 62px;
            object-fit: cover;
            border: 1px solid #e8edf2;
            background: #f7f9fb;
        }

        .teacher-status {
            display: inline-block;
            padding: 4px 8px;
            color: #237804;
            background: #f6ffed;
            border: 1px solid #b7eb8f;
            font-size: 12px;
        }

        .teacher-status.inactive {
            color: #8c1d18;
            background: #fff1f0;
            border-color: #ffccc7;
        }

        .dashboard-table-wrap {
            overflow-x: auto;
            background: #fff;
            border: 1px solid #e8edf2;
        }

        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 860px;
        }

        .dashboard-table th,
        .dashboard-table td {
            padding: 13px 14px;
            border-bottom: 1px solid #edf0f5;
            text-align: left;
            vertical-align: top;
        }

        .dashboard-table th {
            color: #fff;
            background: #43a9d8;
            font-weight: 600;
        }

        .dashboard-empty,
        .dashboard-error {
            background: #fff;
            border: 1px solid #e8edf2;
            padding: 18px;
        }

        .dashboard-error {
            color: #b42318;
            background: #fff1f0;
            border-color: #ffccc7;
            margin-bottom: 25px;
        }

        @media (max-width: 767px) {
            .dashboard-toolbar,
            .dashboard-section-head,
            .dashboard-stats {
                display: block;
            }

            .dashboard-form-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-stat,
            .dashboard-export-links,
            .dashboard-toolbar .cws-button {
                margin-top: 15px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="page-header-top">
            <div class="grid-row clear-fix">
                <address>
                    <a href="tel:+919982754110" class="phone-number">
                        <i class="fa fa-phone"></i>+91 9982754110</a>
                    <a href="mailto:mbvm.mungthala1@gmail.com" class="email">
                        <i class="fa fa-envelope-o"></i>
                        <span>mbvm.mungthala1@gmail.com</span>
                    </a>
                </address>
                <div class="header-top-panel">
                    <a href="backend/logout.php" class="fa fa-sign-out login-icon" title="Logout"></a>
                    <div id="top_social_links_wrapper">
                        <div class="share-toggle-button">
                            <i class="share-icon fa fa-share-alt"></i>
                        </div>
                        <div class="cws_social_links">
                            <a href="https://www.youtube.com/@hindimid.mbvmschoolmoongth834/" class="cws_social_link" title="Youtube">
                                <i class="share-icon fa fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sticky-wrapper">
            <div class="sticky-menu">
                <div class="grid-row clear-fix">
                    <a href="index.html" class="logo">
                        <img src="./frontend/img/logo.png" alt="Madhusudan Bal Vidya Mandir">
                        <h1>Madhusudan Bal Vidya Mandir</h1>
                    </a>
                    <nav class="main-nav">
                        <ul class="clear-fix">
                            <li><a href="index.html">Home</a></li>
                            <li><a href="class.html">Class</a></li>
                            <li><a href="teachers.php">Teachers</a></li>
                            <li><a href="events.php">Events</a></li>
                            <li><a href="gallery.php">Gallery</a></li>
                            <li><a href="contact-us.html">Contact Us</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="page-content">
        <main class="grid-row dashboard-wrap">
            <div class="dashboard-toolbar">
                <div>
                    <h2>Admin Dashboard</h2>
                    <p>Admission enquiries and contact messages from the website.</p>
                </div>
                <div class="dashboard-export-links">
                    <a href="backend/health.php" class="cws-button bt-color-3 border-radius">Hosting Health</a>
                    <a href="backend/test_sheets_sync.php" class="cws-button bt-color-3 border-radius" target="_blank" rel="noopener">Test Google Sheets Sync</a>
                    <a href="backend/logout.php" class="cws-button bt-color-3 border-radius">Logout</a>
                </div>
            </div>

            <?php if ($dbError !== ''): ?>
                <div class="dashboard-error"><?= e($dbError) ?></div>
            <?php endif; ?>

            <div class="dashboard-stats">
                <div class="dashboard-stat corner-radius">
                    <strong id="admission-count"><?= count($admissions) ?></strong>
                    Admission Enquiries
                </div>
                <div class="dashboard-stat corner-radius">
                    <strong id="contact-count"><?= count($contacts) ?></strong>
                    Contact Messages
                </div>
                <div class="dashboard-stat corner-radius">
                    <strong id="result-count"><?= count($results) ?></strong>
                    Student Results
                </div>
                <div class="dashboard-stat corner-radius">
                    <strong id="event-count"><?= count($events) ?></strong>
                    Events
                </div>
                <div class="dashboard-stat corner-radius">
                    <strong id="gallery-count"><?= count($galleryItems) ?></strong>
                    Gallery Photos
                </div>
                <div class="dashboard-stat corner-radius">
                    <strong id="teacher-count"><?= count($teachers) ?></strong>
                    Teachers
                </div>
            </div>

            <section class="dashboard-section">
                <div class="dashboard-section-head">
                    <h2><?= $editingTeacher ? 'Edit Teacher' : 'Add Teacher' ?></h2>

                    <?php if ($editingTeacher): ?>
                        <a href="dashboard.php" class="cws-button bt-color-3 border-radius">Cancel Edit</a>
                    <?php endif; ?>
                </div>
                <?php if (!empty($_GET['teacher_success'])): ?>
                    <div class="dashboard-message"><?= e((string) $_GET['teacher_success']) ?></div>
                <?php endif; ?>
                <?php if (!empty($_GET['teacher_error'])): ?>
                    <div class="dashboard-error"><?= e((string) $_GET['teacher_error']) ?></div>
                <?php endif; ?>

                <form class="dashboard-form corner-radius" method="post" action="backend/save_teacher.php" enctype="multipart/form-data">
                    <?= mbvm_csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) ($editingTeacher['id'] ?? 0) ?>">
                    <div class="dashboard-form-grid">
                        <div>
                            <label for="teacher_name">Teacher Name</label>
                            <input type="text" id="teacher_name" name="name" value="<?= e((string) ($editingTeacher['name'] ?? '')) ?>" required>
                        </div>
                        <div>
                            <label for="teacher_designation">Designation</label>
                            <input type="text" id="teacher_designation" name="designation" value="<?= e((string) ($editingTeacher['designation'] ?? 'Teacher')) ?>" required>
                        </div>
                        <div>
                            <label for="teacher_subject">Subject</label>
                            <input type="text" id="teacher_subject" name="subject" value="<?= e((string) ($editingTeacher['subject'] ?? '')) ?>">
                        </div>
                        <div>
                            <label for="teacher_qualification">Qualification</label>
                            <input type="text" id="teacher_qualification" name="qualification" value="<?= e((string) ($editingTeacher['qualification'] ?? '')) ?>">
                        </div>
                        <div>
                            <label for="teacher_sort_order">Display Order</label>
                            <input type="number" id="teacher_sort_order" name="sort_order" value="<?= e((string) ($editingTeacher['sort_order'] ?? '0')) ?>">
                        </div>
                        <div>
                            <label for="teacher_photo">Photo</label>
                            <input type="file" id="teacher_photo" name="teacher_photo" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="full-field">
                            <label for="teacher_bio">Profile Summary</label>
                            <textarea id="teacher_bio" name="bio"><?= e((string) ($editingTeacher['bio'] ?? 
                            'Dedicated faculty supporting students with discipline, care, and regular guidance.')) ?></textarea>
                        </div>
                        <div class="full-field">
                            <label>
                                <input type="checkbox" name="is_active" value="1" <?= (int) ($editingTeacher['is_active'] ?? 1) === 1 ? 'checked' : '' ?> style="width:auto;">
                                Show this teacher on website
                            </label>
                        </div>
                        <div class="full-field">
                            <button type="submit" class="cws-button bt-color-3 border-radius"><?= $editingTeacher ? 'Update Teacher' : 'Add Teacher' ?></button>
                        </div>
                    </div>
                </form>

                <?php if ($teachers === []): ?>
                    <div class="dashboard-empty">No teacher profiles yet.</div>
                <?php else: ?>
                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($teachers as $row): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($row['photo_path'])): ?>
                                                <img class="teacher-thumb" src="<?= e((string) $row['photo_path']) ?>" alt="">
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= e((string) ($row['name'] ?? '')) ?></strong><br>
                                            <?= e((string) ($row['designation'] ?? '')) ?><br>
                                            <?= e((string) ($row['qualification'] ?? '')) ?>
                                        </td>
                                        <td><?= e((string) ($row['subject'] ?? '')) ?></td>
                                        <td><?= e((string) ($row['sort_order'] ?? '0')) ?></td>
                                        <td>
                                            <span class="teacher-status <?= (int) ($row['is_active'] ?? 1) === 1 ? '' : 'inactive' ?>">
                                                <?= (int) ($row['is_active'] ?? 1) === 1 ? 'Active' : 'Hidden' ?>
                                            </span>
                                        </td>
                                        <td>

                                            <div class="inline-actions">
                                                <a href="dashboard.php?edit_teacher=<?= (int) $row['id'] ?>" class="cws-button bt-color-3 border-radius">Edit</a>
                                                <form action="backend/delete_teacher.php" method="post" class="js-dashboard-delete-form" 
                                                data-confirm="Remove this teacher profile?" data-empty="No teacher profiles yet." data-counter="teacher-count">
                                                    <?= mbvm_csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                    <button type="submit" class="delete-btn">Remove</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="dashboard-section">
                <h2>Add Event</h2>

                <?php if (!empty($_GET['event_success'])): ?>
                    <div class="dashboard-message"><?= e((string) $_GET['event_success']) ?></div>
                <?php endif; ?>
                <?php if (!empty($_GET['event_error'])): ?>
                    <div class="dashboard-error"><?= e((string) $_GET['event_error']) ?></div>
                <?php endif; ?>

                <form class="dashboard-form corner-radius" method="post" action="backend/save_event.php" enctype="multipart/form-data">
                    <?= mbvm_csrf_field() ?>
                    <div class="dashboard-form-grid">
                        <div>
                            <label for="event_title">Event Title</label>
                            <input type="text" id="event_title" name="title" required>
                        </div>
                        <div>
                            <label for="event_date">Event Date</label>
                            <input type="date" id="event_date" name="event_date" required>
                        </div>
                        <div>
                            <label for="event_time">Event Time</label>
                            <input type="text" id="event_time" name="event_time" placeholder="10:30 AM">
                        </div>
                        <div class="full-field">
                            <label for="youtube_url">YouTube Video URL</label>
                            <input type="url" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                        <div class="full-field">
                            <label for="event_image">Event Image</label>
                            <input type="file" id="event_image" name="event_image" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="full-field">
                            <label for="event_description">Description</label>
                            <textarea id="event_description" name="description" required></textarea>
                        </div>
                        <div class="full-field">
                            <button type="submit" class="cws-button bt-color-3 border-radius">Add Event</button>
                        </div>
                    </div>
                </form>

                <?php if ($events !== []): ?>
                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Media</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($events as $row): ?>
                                    <tr>
                                        <td><?= e((string) ($row['event_date'] ?? '')) ?><br><?= e((string) ($row['event_time'] ?? '')) ?></td>
                                        <td><strong><?= e((string) ($row['title'] ?? '')) ?></strong></td>
                                        <td>
                                            <?php if (!empty($row['youtube_url'])): ?>
                                                <a href="<?= e((string) $row['youtube_url']) ?>" target="_blank" rel="noopener">Video</a><br>
                                            <?php endif; ?>
                                            <?php if (!empty($row['image_path'])): ?>
                                                <a href="<?= e((string) $row['image_path']) ?>" target="_blank" rel="noopener">Image</a>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= nl2br(e((string) ($row['description'] ?? ''))) ?></td>
                                        <td>
                                            <form action="backend/delete_event.php" method="post" 
                                                class="js-dashboard-delete-form" data-confirm="Delete this event?" 
                                                data-empty="No events yet." data-counter="event-count">
                                                <?= mbvm_csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                <button type="submit" class="delete-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="dashboard-section">
                <h2>Add Gallery Photo</h2>
                <?php if (!empty($_GET['gallery_success'])): ?>
                    <div class="dashboard-message"><?= e((string) $_GET['gallery_success']) ?></div>
                <?php endif; ?>
                <?php if (!empty($_GET['gallery_error'])): ?>
                    <div class="dashboard-error"><?= e((string) $_GET['gallery_error']) ?></div>
                <?php endif; ?>

                <form class="dashboard-form corner-radius" method="post" action="backend/save_gallery.php" enctype="multipart/form-data">
                    <?= mbvm_csrf_field() ?>
                    <div class="dashboard-form-grid">
                        <div>
                            <label for="gallery_title">Photo Title</label>
                            <input type="text" id="gallery_title" name="title">
                        </div>
                        <div>
                            <label for="gallery_category">Category</label>
                            <input type="text" id="gallery_category" name="category" placeholder="students, teachers">
                        </div>
                        <div>
                            <label for="gallery_image">Photo</label>
                            <input type="file" id="gallery_image" name="gallery_image" accept="image/jpeg,image/png,image/webp" required>
                        </div>
                        <div class="full-field">
                            <button type="submit" class="cws-button bt-color-3 border-radius">Add Photo</button>
                        </div>
                    </div>
                </form>

                <?php if ($galleryItems !== []): ?>
                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Added</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($galleryItems as $row): ?>
                                    <tr>
                                        <td><?= e((string) ($row['created_at'] ?? '')) ?></td>
                                        <td><?= e((string) ($row['title'] ?? '')) ?></td>
                                        <td><?= e((string) ($row['category'] ?? '')) ?></td>
                                        <td><a href="<?= e((string) ($row['image_path'] ?? '')) ?>" target="_blank" rel="noopener">View</a></td>
                                        <td>
                                            <form action="backend/delete_gallery.php" method="post" 
                                                class="js-dashboard-delete-form" data-confirm="Delete this gallery photo?" 
                                                data-empty="No gallery photos yet." data-counter="gallery-count">
                                                <?= mbvm_csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                <button type="submit" class="delete-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            </section>

            <section class="dashboard-section">
                <div class="dashboard-section-head">
                    <h2>Add / Update Student Result</h2>
                    <div class="dashboard-export-links">
                        <a href="backend/export.php?type=results" class="cws-button bt-color-3 border-radius">Export Results CSV</a>
                    </div>
                </div>

                <?php if (!empty($_GET['result_success'])): ?>
                    <div class="dashboard-message"><?= e((string) $_GET['result_success']) ?></div>
                <?php endif; ?>
                <?php if (!empty($_GET['result_error'])): ?>
                    <div class="dashboard-error"><?= e((string) $_GET['result_error']) ?></div>
                <?php endif; ?>

                <form class="dashboard-form corner-radius" method="post" action="backend/save_result.php" id="result-form">
                    <?= mbvm_csrf_field() ?>
                    <input type="hidden" name="result_id" id="result_id" value="0">
                    <div class="dashboard-form-grid">
                        <div>
                            <label for="student_name">Student Name</label>
                            <input type="text" id="student_name" name="student_name" required>
                        </div>
                        <div>
                            <label for="registration_no">Registration No.</label>
                            <input type="text" id="registration_no" name="registration_no" required>
                        </div>
                        <div>
                            <label for="roll_no">Roll No.</label>
                            <input type="text" id="roll_no" name="roll_no">
                        </div>
                        <div>
                            <label for="class_name">Class</label>
                            <select id="class_name" name="class_name" required>
                                <option value="">Select Class</option>
                                <option value="1">One</option>
                                <option value="2">Two</option>
                                <option value="3">Three</option>
                                <option value="4">Four</option>
                                <option value="5">Five</option>
                                <option value="6">Six</option>
                                <option value="7">Seven</option>
                                <option value="8">Eight</option>
                                <option value="9">Nine</option>
                                <option value="10">Ten</option>
                            </select>
                        </div>
                        <div>
                            <label for="session_year">Session</label>
                            <input type="text" id="session_year" name="session_year" placeholder="2025-2026" required>
                        </div>
                        <div>
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="Pass">Pass</option>
                                <option value="Fail">Fail</option>
                                <option value="Promoted">Promoted</option>
                                <option value="Withheld">Withheld</option>
                            </select>
                        </div>
                        <div>
                            <label for="total_marks">Total Marks</label>
                            <input type="number" id="total_marks" name="total_marks" min="1" step="0.01" required>
                        </div>
                        <div>
                            <label for="obtained_marks">Obtained Marks</label>
                            <input type="number" id="obtained_marks" name="obtained_marks" min="0" step="0.01" required>
                        </div>
                        <div>
                            <label for="grade">Grade</label>
                            <input type="text" id="grade" name="grade" placeholder="A+" required>
                        </div>
                        <div class="full-field">
                            <label for="remarks">Remarks</label>
                            <textarea id="remarks" name="remarks"></textarea>
                        </div>
                        <div class="full-field">
                            <button type="submit" class="cws-button bt-color-3 border-radius" id="result-submit">Save Result</button>
                            <button type="button" class="cws-button bt-color-3 border-radius" id="result-cancel-edit" style="display:none;">Cancel Edit</button>
                        </div>
                    </div>
                </form>

                <?php if ($results !== []): ?>
                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Updated</th>
                                    <th>Student</th>
                                    <th>Class / Session</th>
                                    <th>Marks</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $row): ?>
                                    <tr data-result-id="<?= (int) $row['id'] ?>"
                                        data-student-name="<?= e((string) ($row['student_name'] ?? '')) ?>"
                                        data-registration-no="<?= e((string) ($row['registration_no'] ?? '')) ?>"
                                        data-roll-no="<?= e((string) ($row['roll_no'] ?? '')) ?>"
                                        data-class-name="<?= e((string) ($row['class_name'] ?? '')) ?>"
                                        data-session-year="<?= e((string) ($row['session_year'] ?? '')) ?>"
                                        data-total-marks="<?= e((string) ($row['total_marks'] ?? '')) ?>"
                                        data-obtained-marks="<?= e((string) ($row['obtained_marks'] ?? '')) ?>"
                                        data-grade="<?= e((string) ($row['grade'] ?? '')) ?>"
                                        data-status="<?= e((string) ($row['status'] ?? '')) ?>"
                                        data-remarks="<?= e((string) ($row['remarks'] ?? '')) ?>">
                                        <td><?= e((string) ($row['updated_at'] ?? '')) ?></td>

                                        <td>
                                            <strong><?= e((string) ($row['student_name'] ?? '')) ?></strong><br>
                                            Reg: <?= e((string) ($row['registration_no'] ?? '')) ?><br>
                                            Roll: <?= e((string) ($row['roll_no'] ?? '')) ?>
                                        </td>
                                        <td><?= e((string) ($row['class_name'] ?? '')) ?><br><?= e((string) ($row['session_year'] ?? '')) ?></td>
                                        <td>
                                            <?= e((string) ($row['obtained_marks'] ?? '')) ?> /
                                            <?= e((string) ($row['total_marks'] ?? '')) ?><br>
                                            <?= e((string) ($row['percentage'] ?? '')) ?>%
                                        </td>
                                        <td><?= e((string) ($row['grade'] ?? '')) ?></td>
                                        <td><?= e((string) ($row['status'] ?? '')) ?></td>
                                        <td>
                                            <div class="inline-actions">
                                                <button type="button" class="cws-button bt-color-3 border-radius js-edit-result">Edit</button>
                                                <form action="backend/delete_result.php" method="post" 
                                                    class="js-dashboard-delete-form" data-confirm="Delete this student result?"
                                                    data-empty="No student results yet." data-counter="result-count">
                                                    <?= mbvm_csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                    <button type="submit" class="delete-btn">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="dashboard-section">
            <div class="dashboard-section-head">
                <h2>Admission Enquiries</h2>
                <div class="dashboard-export-links">
                    <a href="backend/export.php?type=admissions" class="cws-button bt-color-3 border-radius">Export Admissions CSV</a>
                </div>
            </div>

            <?php if (!empty($_GET['admission_success'])): ?>
                <div class="dashboard-message"><?= e((string) $_GET['admission_success']) ?></div>
            <?php endif; ?>
            <?php if (!empty($_GET['admission_error'])): ?>
                <div class="dashboard-error"><?= e((string) $_GET['admission_error']) ?></div>
            <?php endif; ?>

            <?php if ($admissions === []): ?>
                <div class="dashboard-empty">No admission enquiries yet.</div>
                    <?php else: ?>
                        <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Parent Contact</th>
                                <th>ID Card</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                <tbody>
                    <?php foreach ($admissions as $row): ?>
                        <tr>
                            <td><?= e((string) ($row['created_at'] ?? '')) ?></td>

                            <td>
                                <strong><?= e((string) ($row['fullname'] ?? '')) ?></strong><br>

                                DOB: <?= e((string) ($row['dob'] ?? '')) ?><br>

                                Nationality:
                                <?= e((string) ($row['nationality'] ?? '')) ?>
                            </td>

                            <td>
                                <?= e((string) ($row['class_name'] ?? '')) ?><br>

                                Session:
                                <?= e((string) ($row['session_year'] ?? '')) ?>
                            </td>

                            <td>
                                Father:
                                <?= e((string) ($row['father_name'] ?? '')) ?>,

                                <?= e((string) ($row['father_mobile'] ?? '')) ?><br>

                                Mother:
                                <?= e((string) ($row['mother_name'] ?? '')) ?>,

                                <?= e((string) ($row['mother_mobile'] ?? '')) ?><br>

                                <?= e((string) ($row['email'] ?? '')) ?>
                            </td>

                            <td>
                                <?php if (!empty($row['id_card'])): ?>
                                    <a href="backend/download_admission_file.php?file=
                                    <?= e(rawurlencode(basename((string) $row['id_card']))) ?>"
                                       target="_blank"
                                       rel="noopener">
                                        View
                                    </a>
                                <?php endif; ?>
                            </td>

                                <td>
                                    <form action="backend/delete_admission.php"
                                        method="POST"
                                        class="js-dashboard-delete-form"
                                        data-confirm="Are you sure you want to delete this enquiry?"
                                        data-empty="No admission enquiries yet."
                                        data-counter="admission-count">

                                        <?= mbvm_csrf_field() ?>
                                        <input type="hidden"
                                           name="id"
                                           value="<?= (int) $row['id'] ?>">

                                        <button type="submit" class="delete-btn">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    </table>
                   </div>
                <?php endif; ?>
            </section>

            <section class="dashboard-section">
                <div class="dashboard-section-head">
                    <h2>Contact Messages</h2>
                    <div class="dashboard-export-links">
                        <a href="backend/export.php?type=contacts" class="cws-button bt-color-3 border-radius">Export Contacts CSV</a>
                    </div>
                </div>
                <?php if (!empty($_GET['contact_success'])): ?>
                    <div class="dashboard-message"><?= e((string) $_GET['contact_success']) ?></div>
                <?php endif; ?>
                <?php if (!empty($_GET['contact_error'])): ?>
                    <div class="dashboard-error"><?= e((string) $_GET['contact_error']) ?></div>
                <?php endif; ?>
                <?php if ($contacts === []): ?>
                    <div class="dashboard-empty">No contact messages yet.</div>
                <?php else: ?>

                    <div class="dashboard-table-wrap">
                        <table class="dashboard-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                <?php foreach ($contacts as $row): ?>
                                    <tr>
                                        <td><?= e((string) ($row['created_at'] ?? '')) ?></td>
                                        <td><?= e((string) ($row['name'] ?? '')) ?></td>
                                        <td><?= e((string) ($row['email'] ?? '')) ?></td>
                                        <td><?= e((string) ($row['subject'] ?? '')) ?></td>
                                        <td><?= nl2br(e((string) ($row['message'] ?? ''))) ?></td>
                                        <td>
                                            <form action="backend/delete_contact.php" method="post" 
                                                class="js-dashboard-delete-form" data-confirm="Delete this contact message?" 
                                                data-empty="No contact messages yet." data-counter="contact-count">
                                                <?= mbvm_csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                <button type="submit" class="delete-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <footer>
		<div class="grid-row">
			<div class="grid-col-row clear-fix">
				<section class="grid-col grid-col-6 footer-about">
					<h2 class="corner-radius">About Us</h2>
					<div>
						<p>MADHUSUDAN BAL VIDYA MANDIR MUNGTHALA was established in 2004 and it is managed by the Pvt. Unaided.
							It is located at Rural area of  ABU-ROAD block of SIROHI district of Rajasthan. 
							The school consists of Grades from Nursary to 10. The school is Co-educational and it have an attached pre-primary section.
							The school is Non-Ashram type (Govt.) in nature and is not using school building as a shift-school. 
							Hindi is the medium of instructions in this school. This school is approachable by all weather road. 
							In this school academic session starts in April.
         					</p>
					</div>
					<address>
						<p></p>
						<a href="tel:+919982754110" class="phone-number">+91 9982754110</a>
						<br />
						<a href="mailto:mbvm.mungthala1@gmail.com" class="email">
							<span class="">mbvm.mungthala1@gmail.com</span>
						</a>
						

						<br>
						<a href="contact-us.html" class="address">Mungthala Bus Stand
							<br/>Abu-Road Rajasthan</a>
						</br>
					</address>

				</section>
				
				<section class="grid-col grid-col-6 footer-links">
					<h2 class="corner-radius">Help Links
						<i class="site"></i>
					</h2>
					<ul class="clear-fix">
						<li>
							<a href="faq.html">FAQ</a>
						</li>
						<li>
							<a href="admission.html">Admission</a>
						</li>
						<li>
							<a href="results.php">Results</a>
						</li>
						<li>
							<a href="timeline.html">Timeline</a>

						</li>
						<li>
							<a href="gallery.php">Gallery</a>
						</li>

						<li>
							<a href="contact-us.html">Contact Us</a>
						</li>
					</ul>
				</section>
			</div>
		</div>
		<div class="footer-bottom">
			<div class="grid-row clear-fix">
				<div class="copyright">Madhusudan Bal Vidya Mandir
					<span></span> 2026 All Rights Reserved
				</div>

				<div class="footer-social">
					<a href="https://www.youtube.com/@hindimid.mbvmschoolmoongth834/" class="fa fa-youtube"></a>
				</div>

				<div class="maintainedby">Maintained by
					<a href="https://www.linkedin.com/in/kuldeepchouhan1301/" class="site">kuldeep Chouhan</a>
				</div>
			</div>
		</div>
	</footer>

    <script src="/frontend/js/libs.js"></script>
    <script>
        (function () {
            'use strict';

            function showDashboardMessage(section, message, isError) {
                var existing = section.querySelector('.js-dashboard-action-message');
                if (!existing) {
                    existing = document.createElement('div');
                    existing.className = 'js-dashboard-action-message';
                    var head = section.querySelector('.dashboard-section-head');
                    if (head) {
                        head.parentNode.insertBefore(existing, head.nextSibling);
                    } else {
                        section.insertBefore(existing, section.firstChild);
                    }
                }

                existing.className = 'js-dashboard-action-message ' + (isError ? 'dashboard-error' : 'dashboard-message');
                existing.textContent = message;
            }

            function decrementCounter(id) {
                var count = document.getElementById(id);
                if (!count) {
                    return;
                }

                var current = parseInt(count.textContent, 10);
                count.textContent = String(Math.max(0, (isNaN(current) ? 1 : current) - 1));
            }

            document.addEventListener('submit', function (event) {
                var form = event.target.closest('.js-dashboard-delete-form');
                if (!form) {
                    return;
                }

                event.preventDefault();

                if (!confirm(form.getAttribute('data-confirm') || 'Delete this item?')) {
                    return;
                }

                var button = form.querySelector('button[type="submit"]');
                var originalText = button ? button.textContent : '';
                if (button) {
                    button.disabled = true;
                    button.textContent = 'Deleting...';
                }

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(form)
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            if (!response.ok || !data.success) {
                                throw new Error(data.message || 'Item could not be deleted.');
                            }
                            return data;
                        });
                    })
                    .then(function (data) {
                        var section = form.closest('.dashboard-section');
                        var row = form.closest('tr');
                        var tbody = row ? row.parentNode : null;
                        var tableWrap = form.closest('.dashboard-table-wrap');

                        if (row) {
                            row.parentNode.removeChild(row);
                        }

                        decrementCounter(form.getAttribute('data-counter'));
                        showDashboardMessage(section, data.message || 'Item deleted successfully.', false);

                        if (tbody && tbody.querySelectorAll('tr').length === 0 && tableWrap) {
                            tableWrap.outerHTML = '<div class="dashboard-empty">' + (form.getAttribute('data-empty') || 'No records yet.') + '</div>';
                        }
                    })
                    .catch(function (error) {
                        var section = form.closest('.dashboard-section');
                        showDashboardMessage(section, error.message || 'Item could not be deleted.', true);
                    })
                    .finally(function () {
                        if (button) {
                            button.disabled = false;
                            button.textContent = originalText;
                        }
                    });
            });

            var resultForm = document.getElementById('result-form');
            var resultSubmit = document.getElementById('result-submit');
            var resultCancel = document.getElementById('result-cancel-edit');

            function setResultField(name, value) {
                if (!resultForm) {
                    return;
                }

                var field = resultForm.querySelector('[name="' + name + '"]');
                if (field) {
                    field.value = value || '';
                }
            }

            function resetResultEdit() {
                if (!resultForm) {
                    return;
                }

                resultForm.reset();
                setResultField('result_id', '0');
                if (resultSubmit) {
                    resultSubmit.textContent = 'Save Result';
                }
                if (resultCancel) {
                    resultCancel.style.display = 'none';
                }
            }

            document.addEventListener('click', function (event) {
                var button = event.target.closest('.js-edit-result');
                if (!button || !resultForm) {
                    return;
                }

                var row = button.closest('tr');
                if (!row) {
                    return;
                }

                setResultField('result_id', row.getAttribute('data-result-id'));
                setResultField('student_name', row.getAttribute('data-student-name'));
                setResultField('registration_no', row.getAttribute('data-registration-no'));
                setResultField('roll_no', row.getAttribute('data-roll-no'));
                setResultField('class_name', row.getAttribute('data-class-name'));
                setResultField('session_year', row.getAttribute('data-session-year'));
                setResultField('total_marks', row.getAttribute('data-total-marks'));
                setResultField('obtained_marks', row.getAttribute('data-obtained-marks'));
                setResultField('grade', row.getAttribute('data-grade'));
                setResultField('status', row.getAttribute('data-status'));
                setResultField('remarks', row.getAttribute('data-remarks'));

                if (resultSubmit) {
                    resultSubmit.textContent = 'Update Result';
                }
                if (resultCancel) {
                    resultCancel.style.display = 'inline-block';
                }

                resultForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });

            if (resultCancel) {
                resultCancel.addEventListener('click', resetResultEdit);
            }
        }());
    </script>
</body>

</html>
