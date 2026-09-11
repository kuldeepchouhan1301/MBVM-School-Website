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
    $dbError = 'Database connection error. Please run backend/schema.sql in phpMyAdmin or backend/setup_database.php.';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE HTML>
<html lang="en" class="admin-html">

<head>
    <title>MBVM | Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="description" content="Madhusudan Bal Vidya Mandir Admin Portal">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="frontend/img/favicon.png">
    <link rel="stylesheet" href="frontend/css/libs.css">
    <link rel="stylesheet" href="frontend/css/modern.css">
    <link rel="stylesheet" href="frontend/css/admin-modern.css">
</head>

<body class="admin-body">

    <!-- Admin Shell Container -->
    <div class="admin-shell">

        <!-- Mobile Drawer Overlay -->
        <div class="admin-sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sticky Left Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <a href="dashboard.php" class="admin-brand">
                    <div class="admin-logo-circle">
                        <img src="frontend/img/logo.png" alt="MBVM Logo">
                    </div>
                    <div>
                        <div class="admin-brand-title">MBVM School</div>
                        <div class="admin-brand-sub">Admin Portal</div>
                    </div>
                </a>
            </div>

            <nav class="admin-sidebar-nav">
                <a href="#overview" class="admin-nav-item is-active">
                    <i class="fa fa-dashboard"></i> <span>Overview</span>
                </a>
                <a href="#teachers" class="admin-nav-item">
                    <i class="fa fa-users"></i> <span>Teachers</span>
                </a>
                <a href="#events" class="admin-nav-item">
                    <i class="fa fa-calendar"></i> <span>Events</span>
                </a>
                <a href="#gallery" class="admin-nav-item">
                    <i class="fa fa-picture-o"></i> <span>Gallery</span>
                </a>
                <a href="#results" class="admin-nav-item">
                    <i class="fa fa-graduation-cap"></i> <span>Results</span>
                </a>
                <a href="#admissions" class="admin-nav-item">
                    <i class="fa fa-clipboard"></i> <span>Admissions</span>
                </a>
                <a href="#contacts" class="admin-nav-item">
                    <i class="fa fa-envelope-o"></i> <span>Messages</span>
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <div class="admin-user-info">
                    <div class="admin-avatar">A</div>
                    <div>
                        <div class="admin-user-name"><?= e((string) ($_SESSION['admin_user'] ?? 'Admin')) ?></div>
                        <div class="admin-user-role">System Admin</div>
                    </div>
                </div>
                <a href="backend/logout.php" class="admin-logout-btn" title="Logout">
                    <i class="fa fa-sign-out"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content Area Wrap -->
        <div class="admin-main-wrap">

            <!-- Top Header Bar -->
            <header class="admin-top-header">
                <div class="admin-header-title-wrap">
                    <button class="admin-mobile-toggle" id="sidebarToggle" aria-label="Toggle navigation drawer">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div>
                        <h1 class="admin-header-title">Dashboard</h1>
                        <p class="admin-header-sub">Manage your school website from one place.</p>
                    </div>
                </div>

                <div class="admin-header-actions">
                    <button type="button" id="adminHealthCheckBtn" class="admin-btn-secondary" title="Check server health">
                        <i class="fa fa-heartbeat"></i> <span>Health Check</span>
                    </button>
                    <a href="backend/logout.php" class="admin-btn-primary">
                        <i class="fa fa-sign-out"></i> <span>Logout</span>
                    </a>
                </div>
            </header>

            <!-- Main Content Grid -->
            <main class="admin-content">

                <?php if ($dbError !== ''): ?>
                    <div class="dashboard-error" style="margin-bottom: 1.5rem;"><?= e($dbError) ?></div>
                <?php endif; ?>

                <!-- Overview Section with Real MySQL Counts -->
                <section id="overview" style="scroll-margin-top: 90px;">

                    <!-- Stat Cards Grid -->
                    <div class="admin-stats-grid">
                        <a href="#admissions" class="admin-stat-card">
                            <div>
                                <div class="admin-stat-number" id="admission-count"><?= count($admissions) ?></div>
                                <div class="admin-stat-label">Admission Enquiries</div>
                            </div>
                            <div class="admin-stat-icon sky"><i class="fa fa-clipboard"></i></div>
                        </a>

                        <a href="#contacts" class="admin-stat-card">
                            <div>
                                <div class="admin-stat-number" id="contact-count"><?= count($contacts) ?></div>
                                <div class="admin-stat-label">Contact Messages</div>
                            </div>
                            <div class="admin-stat-icon coral"><i class="fa fa-envelope-o"></i></div>
                        </a>

                        <a href="#results" class="admin-stat-card">
                            <div>
                                <div class="admin-stat-number" id="result-count"><?= count($results) ?></div>
                                <div class="admin-stat-label">Student Results</div>
                            </div>
                            <div class="admin-stat-icon primary"><i class="fa fa-graduation-cap"></i></div>
                        </a>

                        <a href="#events" class="admin-stat-card">
                            <div>
                                <div class="admin-stat-number" id="event-count"><?= count($events) ?></div>
                                <div class="admin-stat-label">School Events</div>
                            </div>
                            <div class="admin-stat-icon gold"><i class="fa fa-calendar"></i></div>
                        </a>

                        <a href="#gallery" class="admin-stat-card">
                            <div>
                                <div class="admin-stat-number" id="gallery-count"><?= count($galleryItems) ?></div>
                                <div class="admin-stat-label">Gallery Photos</div>
                            </div>
                            <div class="admin-stat-icon green"><i class="fa fa-picture-o"></i></div>
                        </a>

                        <a href="#teachers" class="admin-stat-card">
                            <div>
                                <div class="admin-stat-number" id="teacher-count"><?= count($teachers) ?></div>
                                <div class="admin-stat-label">Teachers & Faculty</div>
                            </div>
                            <div class="admin-stat-icon sky"><i class="fa fa-users"></i></div>
                        </a>
                    </div>

                    <!-- Quick Actions Section -->
                    <div class="admin-quick-section">
                        <div class="admin-quick-header">
                            <h2 class="admin-quick-title">
                                <i class="fa fa-bolt" style="color: var(--admin-gold);"></i> Quick Actions
                            </h2>
                            <span class="admin-quick-sub">Shortcuts for common admin tasks</span>
                        </div>

                        <div class="admin-quick-grid">
                            <a href="#results" class="quick-action-card">
                                <div class="quick-card-icon primary">
                                    <i class="fa fa-graduation-cap"></i>
                                </div>
                                <div class="quick-card-body">
                                    <div class="quick-card-title">Add Student Result</div>
                                    <div class="quick-card-sub">Create or edit student marks & scorecard</div>
                                </div>
                                <div class="quick-card-link">
                                    <span>Open</span> <i class="fa fa-arrow-right"></i>
                                </div>
                            </a>

                            <a href="#gallery" class="quick-action-card">
                                <div class="quick-card-icon green">
                                    <i class="fa fa-picture-o"></i>
                                </div>
                                <div class="quick-card-body">
                                    <div class="quick-card-title">Add Gallery Photo</div>
                                    <div class="quick-card-sub">Upload school event & campus photos</div>
                                </div>
                                <div class="quick-card-link">
                                    <span>Open</span> <i class="fa fa-arrow-right"></i>
                                </div>
                            </a>

                            <a href="#teachers" class="quick-action-card">
                                <div class="quick-card-icon gold">
                                    <i class="fa fa-users"></i>
                                </div>
                                <div class="quick-card-body">
                                    <div class="quick-card-title">Add Teacher</div>
                                    <div class="quick-card-sub">Manage teacher profiles & designations</div>
                                </div>
                                <div class="quick-card-link">
                                    <span>Open</span> <i class="fa fa-arrow-right"></i>
                                </div>
                            </a>

                            <a href="#events" class="quick-action-card">
                                <div class="quick-card-icon coral">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <div class="quick-card-body">
                                    <div class="quick-card-title">Add Event</div>
                                    <div class="quick-card-sub">Publish upcoming school calendar event</div>
                                </div>
                                <div class="quick-card-link">
                                    <span>Open</span> <i class="fa fa-arrow-right"></i>
                                </div>
                            </a>

                            <a href="#admissions" class="quick-action-card">
                                <div class="quick-card-icon sky">
                                    <i class="fa fa-clipboard"></i>
                                </div>
                                <div class="quick-card-body">
                                    <div class="quick-card-title">Admissions</div>
                                    <div class="quick-card-sub">Review new student admission forms</div>
                                </div>
                                <div class="quick-card-link">
                                    <span>Open</span> <i class="fa fa-arrow-right"></i>
                                </div>
                            </a>

                            <a href="#contacts" class="quick-action-card">
                                <div class="quick-card-icon coral">
                                    <i class="fa fa-envelope-o"></i>
                                </div>
                                <div class="quick-card-body">
                                    <div class="quick-card-title">Messages</div>
                                    <div class="quick-card-sub">View & respond to parent inquiries</div>
                                </div>
                                <div class="quick-card-link">
                                    <span>Open</span> <i class="fa fa-arrow-right"></i>
                                </div>
                            </a>
                        </div>

                        <!-- Export Shortcuts Footer -->
                        <div class="quick-export-row">
                            <span class="quick-export-label"><i class="fa fa-download"></i> Quick CSV Exports:</span>
                            <div class="quick-export-links">
                                <a href="backend/export.php?type=results" class="quick-export-btn"><i class="fa fa-file-excel-o"></i> Results CSV</a>
                                <a href="backend/export.php?type=admissions" class="quick-export-btn"><i class="fa fa-file-excel-o"></i> Admissions CSV</a>
                                <a href="backend/export.php?type=contacts" class="quick-export-btn"><i class="fa fa-file-excel-o"></i> Messages CSV</a>
                            </div>
                        </div>
                    </div>

                </section>

                <!-- Teacher Management Section -->
                <section id="teachers" class="admin-card-section">
                    <div class="admin-section-header">
                        <h2 class="admin-section-title">
                            <i class="fa fa-users" style="color: var(--admin-primary);"></i>
                            <?= $editingTeacher ? 'Edit Faculty Profile' : 'Add Faculty Profile' ?>
                        </h2>
                        <?php if ($editingTeacher): ?>
                            <a href="dashboard.php#teachers" class="admin-btn-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($_GET['teacher_success'])): ?>
                        <div class="dashboard-message" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['teacher_success']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_GET['teacher_error'])): ?>
                        <div class="dashboard-error" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['teacher_error']) ?></div>
                    <?php endif; ?>

                    <form class="admin-form" method="post" action="backend/save_teacher.php" enctype="multipart/form-data">
                        <?= mbvm_csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) ($editingTeacher['id'] ?? 0) ?>">
                        <div class="admin-form-grid">
                            <div>
                                <label for="teacher_name" class="admin-label">Teacher Name *</label>
                                <input type="text" id="teacher_name" name="name" class="admin-input" value="<?= e((string) ($editingTeacher['name'] ?? '')) ?>" required placeholder="e.g. Ramesh Chouhan">
                            </div>
                            <div>
                                <label for="teacher_designation" class="admin-label">Designation *</label>
                                <input type="text" id="teacher_designation" name="designation" class="admin-input" value="<?= e((string) ($editingTeacher['designation'] ?? 'Teacher')) ?>" required placeholder="e.g. Senior Teacher">
                            </div>
                            <div>
                                <label for="teacher_subject" class="admin-label">Subject</label>
                                <input type="text" id="teacher_subject" name="subject" class="admin-input" value="<?= e((string) ($editingTeacher['subject'] ?? '')) ?>" placeholder="e.g. Mathematics">
                            </div>
                            <div>
                                <label for="teacher_qualification" class="admin-label">Qualification</label>
                                <input type="text" id="teacher_qualification" name="qualification" class="admin-input" value="<?= e((string) ($editingTeacher['qualification'] ?? '')) ?>" placeholder="e.g. B.Sc., B.Ed.">
                            </div>
                            <div>
                                <label for="teacher_sort_order" class="admin-label">Display Order</label>
                                <input type="number" id="teacher_sort_order" name="sort_order" class="admin-input" value="<?= e((string) ($editingTeacher['sort_order'] ?? '0')) ?>">
                            </div>
                            <div>
                                <label for="teacher_photo" class="admin-label">Photo Upload</label>
                                <input type="file" id="teacher_photo" name="teacher_photo" class="admin-input" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <div class="admin-field-full">
                                <label for="teacher_bio" class="admin-label">Profile Summary</label>
                                <textarea id="teacher_bio" name="bio" class="admin-textarea" placeholder="Brief teacher description..."><?= e((string) ($editingTeacher['bio'] ?? 'Dedicated faculty supporting students with discipline, care, and regular guidance.')) ?></textarea>
                            </div>
                            <div class="admin-field-full">
                                <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                    <input type="checkbox" name="is_active" value="1" <?= (int) ($editingTeacher['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
                                    <span>Show this teacher profile on public website</span>
                                </label>
                            </div>
                            <div class="admin-field-full">
                                <button type="submit" class="admin-btn-primary">
                                    <i class="fa fa-check"></i> <?= $editingTeacher ? 'Update Faculty Profile' : 'Save Faculty Profile' ?>
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if ($teachers === []): ?>
                        <div class="admin-empty-state">
                            <i class="fa fa-users admin-empty-icon"></i>
                            <div>No teacher profiles added yet.</div>
                        </div>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name & Details</th>
                                        <th>Subject</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($teachers as $row): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($row['photo_path'])): ?>
                                                    <img src="<?= e((string) $row['photo_path']) ?>" alt="" style="width: 50px; height: 54px; object-fit: cover; border-radius: 8px; border: 1px solid var(--admin-border);">
                                                <?php else: ?>
                                                    <div style="width: 50px; height: 54px; border-radius: 8px; background: var(--admin-bg); display: flex; align-items: center; justify-content: center; color: var(--admin-muted);"><i class="fa fa-user"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= e((string) ($row['name'] ?? '')) ?></strong><br>
                                                <span style="font-size: 0.8rem; color: var(--admin-muted);"><?= e((string) ($row['designation'] ?? '')) ?> | <?= e((string) ($row['qualification'] ?? '')) ?></span>
                                            </td>
                                            <td><?= e((string) ($row['subject'] ?? '-')) ?></td>
                                            <td><?= e((string) ($row['sort_order'] ?? '0')) ?></td>
                                            <td>
                                                <span class="admin-badge <?= (int) ($row['is_active'] ?? 1) === 1 ? 'success' : 'warning' ?>">
                                                    <?= (int) ($row['is_active'] ?? 1) === 1 ? 'Active' : 'Hidden' ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 0.5rem;">
                                                    <a href="dashboard.php?edit_teacher=<?= (int) $row['id'] ?>#teachers" class="admin-btn-secondary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">Edit</a>
                                                    <form action="backend/delete_teacher.php" method="post" class="js-dashboard-delete-form" data-confirm="Remove this teacher profile?" data-empty="No teacher profiles yet." data-counter="teacher-count" style="margin:0;">
                                                        <?= mbvm_csrf_field() ?>
                                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                        <button type="submit" class="admin-btn-danger">Delete</button>
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

                <!-- Events Management Section -->
                <section id="events" class="admin-card-section">
                    <div class="admin-section-header">
                        <h2 class="admin-section-title">
                            <i class="fa fa-calendar" style="color: var(--admin-gold);"></i> Add School Event
                        </h2>
                    </div>

                    <?php if (!empty($_GET['event_success'])): ?>
                        <div class="dashboard-message" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['event_success']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_GET['event_error'])): ?>
                        <div class="dashboard-error" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['event_error']) ?></div>
                    <?php endif; ?>

                    <form class="admin-form" method="post" action="backend/save_event.php" enctype="multipart/form-data">
                        <?= mbvm_csrf_field() ?>
                        <div class="admin-form-grid">
                            <div>
                                <label for="event_title" class="admin-label">Event Title *</label>
                                <input type="text" id="event_title" name="title" class="admin-input" required placeholder="e.g. Annual Sports Day 2026">
                            </div>
                            <div>
                                <label for="event_date" class="admin-label">Event Date *</label>
                                <input type="date" id="event_date" name="event_date" class="admin-input" required>
                            </div>
                            <div>
                                <label for="event_time" class="admin-label">Event Time</label>
                                <input type="text" id="event_time" name="event_time" class="admin-input" placeholder="e.g. 10:30 AM">
                            </div>
                            <div class="admin-field-full">
                                <label for="youtube_url" class="admin-label">YouTube Video URL</label>
                                <input type="url" id="youtube_url" name="youtube_url" class="admin-input" placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div class="admin-field-full">
                                <label for="event_image" class="admin-label">Event Image Upload</label>
                                <input type="file" id="event_image" name="event_image" class="admin-input" accept="image/jpeg,image/png,image/webp">
                            </div>
                            <div class="admin-field-full">
                                <label for="event_description" class="admin-label">Event Description *</label>
                                <textarea id="event_description" name="description" class="admin-textarea" required placeholder="Details about event schedule, activities..."></textarea>
                            </div>
                            <div class="admin-field-full">
                                <button type="submit" class="admin-btn-primary">
                                    <i class="fa fa-plus"></i> Add Event
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if ($events === []): ?>
                        <div class="admin-empty-state">
                            <i class="fa fa-calendar admin-empty-icon"></i>
                            <div>No school events added yet.</div>
                        </div>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Title</th>
                                        <th>Media</th>
                                        <th>Description</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($events as $row): ?>
                                        <tr>
                                            <td style="white-space: nowrap;">
                                                <strong><?= e((string) ($row['event_date'] ?? '')) ?></strong><br>
                                                <span style="font-size: 0.8rem; color: var(--admin-muted);"><?= e((string) ($row['event_time'] ?? '')) ?></span>
                                            </td>
                                            <td><strong><?= e((string) ($row['title'] ?? '')) ?></strong></td>
                                            <td>
                                                <?php if (!empty($row['youtube_url'])): ?>
                                                    <a href="<?= e((string) $row['youtube_url']) ?>" target="_blank" rel="noopener" class="admin-btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;"><i class="fa fa-youtube-play" style="color: #ff4d4d;"></i> Video</a>
                                                <?php endif; ?>
                                                <?php if (!empty($row['image_path'])): ?>
                                                    <a href="<?= e((string) $row['image_path']) ?>" target="_blank" rel="noopener" class="admin-btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;"><i class="fa fa-picture-o"></i> Image</a>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= nl2br(e((string) ($row['description'] ?? ''))) ?></td>
                                            <td style="text-align: right;">
                                                <form action="backend/delete_event.php" method="post" class="js-dashboard-delete-form" data-confirm="Delete this event?" data-empty="No events yet." data-counter="event-count" style="margin:0;">
                                                    <?= mbvm_csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                    <button type="submit" class="admin-btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Gallery Management Section -->
                <section id="gallery" class="admin-card-section">
                    <div class="admin-section-header">
                        <h2 class="admin-section-title">
                            <i class="fa fa-picture-o" style="color: var(--admin-green);"></i> Add Gallery Photo
                        </h2>
                    </div>

                    <?php if (!empty($_GET['gallery_success'])): ?>
                        <div class="dashboard-message" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['gallery_success']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_GET['gallery_error'])): ?>
                        <div class="dashboard-error" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['gallery_error']) ?></div>
                    <?php endif; ?>

                    <form class="admin-form" method="post" action="backend/save_gallery.php" enctype="multipart/form-data">
                        <?= mbvm_csrf_field() ?>
                        <div class="admin-form-grid">
                            <div>
                                <label for="gallery_title" class="admin-label">Photo Title</label>
                                <input type="text" id="gallery_title" name="title" class="admin-input" placeholder="e.g. Classroom Activity">
                            </div>
                            <div>
                                <label for="gallery_category" class="admin-label">Category *</label>
                                <select id="gallery_category" name="category" class="admin-select" required>
                                    <option value="campus">Campus</option>
                                    <option value="class_memories">Class Memories</option>
                                    <option value="sports_celebration">Sports Celebration</option>
                                    <option value="independence_parade">Independence Parade</option>
                                    <option value="picnic_and_tour">Picnic and Tour</option>
                                    <option value="farewell_cultural">Farewell & Cultural Program</option>
                                    <option value="celebrations">Celebrations</option>
                                    <option value="achievements">Achievements</option>
                                    <option value="activities">Activities</option>
                                </select>
                            </div>
                            <div>
                                <label for="gallery_image" class="admin-label">Photo Upload *</label>
                                <input type="file" id="gallery_image" name="gallery_image" class="admin-input" accept="image/jpeg,image/png,image/webp" required>
                            </div>
                            <div class="admin-field-full">
                                <button type="submit" class="admin-btn-primary">
                                    <i class="fa fa-plus"></i> Add Gallery Photo
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if ($galleryItems === []): ?>
                        <div class="admin-empty-state">
                            <i class="fa fa-picture-o admin-empty-icon"></i>
                            <div>No gallery photos added yet.</div>
                        </div>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Date Added</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Image View</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($galleryItems as $row): ?>
                                        <tr>
                                            <td style="white-space: nowrap;"><?= e((string) ($row['created_at'] ?? '')) ?></td>
                                            <td><?= e((string) ($row['title'] ?? '-')) ?></td>
                                            <td><span class="admin-badge info"><?= e(ucwords(str_replace('_', ' ', (string) ($row['category'] ?? '')))) ?></span></td>
                                            <td>
                                                <?php if (!empty($row['image_path'])): ?>
                                                    <a href="<?= e((string) $row['image_path']) ?>" target="_blank" rel="noopener" class="admin-btn-secondary" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;"><i class="fa fa-external-link"></i> View Photo</a>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <form action="backend/delete_gallery.php" method="post" class="js-dashboard-delete-form" data-confirm="Delete this gallery photo?" data-empty="No gallery photos yet." data-counter="gallery-count" style="margin:0;">
                                                    <?= mbvm_csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                    <button type="submit" class="admin-btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Student Results Management Section -->
                <section id="results" class="admin-card-section">
                    <div class="admin-section-header">
                        <h2 class="admin-section-title">
                            <i class="fa fa-graduation-cap" style="color: var(--admin-primary);"></i> Add / Update Student Result
                        </h2>
                        <a href="backend/export.php?type=results" class="admin-btn-secondary">
                            <i class="fa fa-download"></i> Export Results CSV
                        </a>
                    </div>

                    <?php if (!empty($_GET['result_success'])): ?>
                        <div class="dashboard-message" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['result_success']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_GET['result_error'])): ?>
                        <div class="dashboard-error" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['result_error']) ?></div>
                    <?php endif; ?>

                    <form class="admin-form" method="post" action="backend/save_result.php" id="result-form">
                        <?= mbvm_csrf_field() ?>
                        <input type="hidden" name="result_id" id="result_id" value="0">
                        <div class="admin-form-grid">
                            <div>
                                <label for="student_name" class="admin-label">Student Name *</label>
                                <input type="text" id="student_name" name="student_name" class="admin-input" required placeholder="e.g. Aarav Sharma">
                            </div>
                            <div>
                                <label for="registration_no" class="admin-label">Registration No. *</label>
                                <input type="text" id="registration_no" name="registration_no" class="admin-input" required placeholder="e.g. MBVM-2026-001">
                            </div>
                            <div>
                                <label for="roll_no" class="admin-label">Roll No.</label>
                                <input type="text" id="roll_no" name="roll_no" class="admin-input" placeholder="e.g. 101">
                            </div>
                            <div>
                                <label for="class_name" class="admin-label">Class *</label>
                                <select id="class_name" name="class_name" class="admin-select" required>
                                    <option value="">Select Class</option>
                                    <option value="1">Class 1</option>
                                    <option value="2">Class 2</option>
                                    <option value="3">Class 3</option>
                                    <option value="4">Class 4</option>
                                    <option value="5">Class 5</option>
                                    <option value="6">Class 6</option>
                                    <option value="7">Class 7</option>
                                    <option value="8">Class 8</option>
                                    <option value="9">Class 9</option>
                                    <option value="10">Class 10</option>
                                </select>
                            </div>
                            <div>
                                <label for="session_year" class="admin-label">Session *</label>
                                <input type="text" id="session_year" name="session_year" class="admin-input" placeholder="2025-2026" required>
                            </div>
                            <div>
                                <label for="status" class="admin-label">Status *</label>
                                <select id="status" name="status" class="admin-select" required>
                                    <option value="Pass">Pass</option>
                                    <option value="Fail">Fail</option>
                                    <option value="Promoted">Promoted</option>
                                    <option value="Withheld">Withheld</option>
                                </select>
                            </div>
                            <div>
                                <label for="total_marks" class="admin-label">Total Marks *</label>
                                <input type="number" id="total_marks" name="total_marks" class="admin-input" min="1" step="0.01" required placeholder="e.g. 500">
                            </div>
                            <div>
                                <label for="obtained_marks" class="admin-label">Obtained Marks *</label>
                                <input type="number" id="obtained_marks" name="obtained_marks" class="admin-input" min="0" step="0.01" required placeholder="e.g. 465">
                            </div>
                            <div>
                                <label for="grade" class="admin-label">Grade *</label>
                                <input type="text" id="grade" name="grade" class="admin-input" placeholder="e.g. A+" required>
                            </div>
                            <div class="admin-field-full">
                                <label for="remarks" class="admin-label">Teacher Remarks</label>
                                <textarea id="remarks" name="remarks" class="admin-textarea" placeholder="Remarks or remarks regarding performance..."></textarea>
                            </div>
                            <div class="admin-field-full" style="display: flex; gap: 0.75rem;">
                                <button type="submit" class="admin-btn-primary" id="result-submit">
                                    <i class="fa fa-check"></i> Save Result
                                </button>
                                <button type="button" class="admin-btn-secondary" id="result-cancel-edit" style="display:none;">
                                    Cancel Edit
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if ($results === []): ?>
                        <div class="admin-empty-state">
                            <i class="fa fa-graduation-cap admin-empty-icon"></i>
                            <div>No student results uploaded yet.</div>
                        </div>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Updated</th>
                                        <th>Student Details</th>
                                        <th>Class & Session</th>
                                        <th>Marks & %</th>
                                        <th>Grade</th>
                                        <th>Status</th>
                                        <th style="text-align: right;">Action</th>
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
                                            <td style="white-space: nowrap;"><?= e((string) ($row['updated_at'] ?? '')) ?></td>
                                            <td>
                                                <strong><?= e((string) ($row['student_name'] ?? '')) ?></strong><br>
                                                <span style="font-size: 0.8rem; color: var(--admin-muted);">Reg: <?= e((string) ($row['registration_no'] ?? '')) ?> | Roll: <?= e((string) ($row['roll_no'] ?? '-')) ?></span>
                                            </td>
                                            <td>Class <?= e((string) ($row['class_name'] ?? '')) ?><br><span style="font-size: 0.8rem; color: var(--admin-muted);"><?= e((string) ($row['session_year'] ?? '')) ?></span></td>
                                            <td>
                                                <strong><?= e((string) ($row['obtained_marks'] ?? '')) ?> / <?= e((string) ($row['total_marks'] ?? '')) ?></strong><br>
                                                <span style="font-size: 0.8rem; color: var(--admin-green); font-weight: 700;"><?= e((string) ($row['percentage'] ?? '')) ?>%</span>
                                            </td>
                                            <td><span class="admin-badge info"><?= e((string) ($row['grade'] ?? '')) ?></span></td>
                                            <td>
                                                <span class="admin-badge <?= strtolower((string) ($row['status'] ?? '')) === 'pass' ? 'success' : 'danger' ?>">
                                                    <?= e((string) ($row['status'] ?? '')) ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right;">
                                                <div style="display: inline-flex; gap: 0.5rem;">
                                                    <button type="button" class="admin-btn-secondary js-edit-result" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">Edit</button>
                                                    <form action="backend/delete_result.php" method="post" class="js-dashboard-delete-form" data-confirm="Delete this student result?" data-empty="No student results yet." data-counter="result-count" style="margin:0;">
                                                        <?= mbvm_csrf_field() ?>
                                                        <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                        <button type="submit" class="admin-btn-danger">Delete</button>
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

                <!-- Admission Enquiries Section -->
                <section id="admissions" class="admin-card-section">
                    <div class="admin-section-header">
                        <h2 class="admin-section-title">
                            <i class="fa fa-id-card-o" style="color: var(--admin-sky);"></i> Admission Enquiries
                        </h2>
                        <a href="backend/export.php?type=admissions" class="admin-btn-secondary">
                            <i class="fa fa-download"></i> Export Admissions CSV
                        </a>
                    </div>

                    <?php if (!empty($_GET['admission_success'])): ?>
                        <div class="dashboard-message" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['admission_success']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_GET['admission_error'])): ?>
                        <div class="dashboard-error" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['admission_error']) ?></div>
                    <?php endif; ?>

                    <?php if ($admissions === []): ?>
                        <div class="admin-empty-state">
                            <i class="fa fa-id-card-o admin-empty-icon"></i>
                            <div>No online admission enquiries submitted yet.</div>
                        </div>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Student Details</th>
                                        <th>Class & Session</th>
                                        <th>Parent Contacts</th>
                                        <th>ID Card</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($admissions as $row): ?>
                                        <tr>
                                            <td style="white-space: nowrap;"><?= e((string) ($row['created_at'] ?? '')) ?></td>
                                            <td>
                                                <strong><?= e((string) ($row['fullname'] ?? '')) ?></strong><br>
                                                <span style="font-size: 0.8rem; color: var(--admin-muted);">DOB: <?= e((string) ($row['dob'] ?? '-')) ?> | Nationality: <?= e((string) ($row['nationality'] ?? '-')) ?></span>
                                            </td>
                                            <td>Class <?= e((string) ($row['class_name'] ?? '')) ?><br><span style="font-size: 0.8rem; color: var(--admin-muted);"><?= e((string) ($row['session_year'] ?? '')) ?></span></td>
                                            <td>
                                                <strong>Father:</strong> <?= e((string) ($row['father_name'] ?? '-')) ?> (<?= e((string) ($row['father_mobile'] ?? '-')) ?>)<br>
                                                <strong>Mother:</strong> <?= e((string) ($row['mother_name'] ?? '-')) ?> (<?= e((string) ($row['mother_mobile'] ?? '-')) ?>)<br>
                                                <span style="font-size: 0.8rem; color: var(--admin-muted);"><?= e((string) ($row['email'] ?? '')) ?></span>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['id_card'])): ?>
                                                    <a href="backend/download_admission_file.php?file=<?= e(rawurlencode(basename((string) $row['id_card']))) ?>" target="_blank" rel="noopener" class="admin-btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">
                                                        <i class="fa fa-download"></i> View Doc
                                                    </a>
                                                <?php else: ?>
                                                    <span style="color: var(--admin-muted); font-size: 0.8rem;">None</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="text-align: right;">
                                                <form action="backend/delete_admission.php" method="post" class="js-dashboard-delete-form" data-confirm="Delete this admission enquiry?" data-empty="No admission enquiries yet." data-counter="admission-count" style="margin:0;">
                                                    <?= mbvm_csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                    <button type="submit" class="admin-btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Contact Messages Section -->
                <section id="contacts" class="admin-card-section">
                    <div class="admin-section-header">
                        <h2 class="admin-section-title">
                            <i class="fa fa-envelope-o" style="color: var(--admin-coral);"></i> Contact Messages
                        </h2>
                        <a href="backend/export.php?type=contacts" class="admin-btn-secondary">
                            <i class="fa fa-download"></i> Export Contacts CSV
                        </a>
                    </div>

                    <?php if (!empty($_GET['contact_success'])): ?>
                        <div class="dashboard-message" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['contact_success']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_GET['contact_error'])): ?>
                        <div class="dashboard-error" style="margin-bottom: 1.25rem;"><?= e((string) $_GET['contact_error']) ?></div>
                    <?php endif; ?>

                    <?php if ($contacts === []): ?>
                        <div class="admin-empty-state">
                            <i class="fa fa-envelope-o admin-empty-icon"></i>
                            <div>No contact messages received yet.</div>
                        </div>
                    <?php else: ?>
                        <div class="admin-table-wrap">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Sender Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message Text</th>
                                        <th style="text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contacts as $row): ?>
                                        <tr>
                                            <td style="white-space: nowrap;"><?= e((string) ($row['created_at'] ?? '')) ?></td>
                                            <td><strong><?= e((string) ($row['name'] ?? '')) ?></strong></td>
                                            <td><a href="mailto:<?= e((string) ($row['email'] ?? '')) ?>" style="color: var(--admin-primary);"><?= e((string) ($row['email'] ?? '')) ?></a></td>
                                            <td><strong><?= e((string) ($row['subject'] ?? '')) ?></strong></td>
                                            <td><?= nl2br(e((string) ($row['message'] ?? ''))) ?></td>
                                            <td style="text-align: right;">
                                                <form action="backend/delete_contact.php" method="post" class="js-dashboard-delete-form" data-confirm="Delete this contact message?" data-empty="No contact messages yet." data-counter="contact-count" style="margin:0;">
                                                    <?= mbvm_csrf_field() ?>
                                                    <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                                                    <button type="submit" class="admin-btn-danger">Delete</button>
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

            <!-- Minimal Admin Footer -->
            <footer class="admin-footer">
                <div>© 2026 Madhusudan Bal Vidya Mandir — Admin Management Portal</div>
                <div style="margin-top: 4px;">Maintained by <a href="https://www.linkedin.com/in/kuldeepchouhan1301/" target="_blank" rel="noopener">Kuldeep Chouhan</a></div>
            </footer>

        </div>

    </div>

    <!-- Custom Styled Delete Confirmation Modal -->
    <div id="adminDeleteModal" class="admin-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="adminDeleteModalTitle">
        <div class="admin-modal-card">
            <div class="admin-modal-icon">
                <i class="fa fa-trash-o"></i>
            </div>
            <h3 id="adminDeleteModalTitle" class="admin-modal-title">Confirm Deletion</h3>
            <p id="adminDeleteModalMsg" class="admin-modal-msg">Are you sure you want to delete this record? This action cannot be undone.</p>
            <div id="adminDeleteModalError" class="admin-modal-error"></div>
            <div class="admin-modal-actions">
                <button type="button" class="admin-modal-btn-cancel" id="adminDeleteCancelBtn">Cancel</button>
                <button type="button" class="admin-modal-btn-delete" id="adminDeleteConfirmBtn">
                    <i class="fa fa-trash-o"></i> <span>Delete Record</span>
                </button>
            </div>
        </div>
    </div>

    <script src="frontend/js/libs.js"></script>
    <script>
        (function () {
            'use strict';

            // Mobile Sidebar Drawer Toggle
            var sidebar = document.getElementById('adminSidebar');
            var overlay = document.getElementById('sidebarOverlay');
            var toggleBtn = document.getElementById('sidebarToggle');
            var navItems = document.querySelectorAll('.admin-nav-item');

            function openSidebar() {
                if (sidebar) sidebar.classList.add('is-open');
                if (overlay) overlay.classList.add('is-active');
                document.body.classList.add('admin-menu-open');
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('is-open');
                if (overlay) overlay.classList.remove('is-active');
                document.body.classList.remove('admin-menu-open');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    if (sidebar && sidebar.classList.contains('is-open')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }
            if (overlay) overlay.addEventListener('click', closeSidebar);

            navItems.forEach(function (item) {
                item.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    closeSidebar();
                }
            });

            // Active Sidebar Navigation Highlight on Scroll
            var navItems = document.querySelectorAll('.admin-nav-item');
            var sections = document.querySelectorAll('section[id]');
            var mainWrap = document.querySelector('.admin-main-wrap');

            function highlightNav() {
                var scrollPos = (mainWrap && mainWrap.scrollTop > 0) ? mainWrap.scrollTop : (window.scrollY || window.pageYOffset);
                sections.forEach(function (sec) {
                    var top = sec.offsetTop - 120;
                    var height = sec.offsetHeight;
                    var id = sec.getAttribute('id');

                    if (scrollPos >= top && scrollPos < top + height) {
                        navItems.forEach(function (item) {
                            if (item.getAttribute('href') === '#' + id) {
                                item.classList.add('is-active');
                            } else {
                                item.classList.remove('is-active');
                            }
                        });
                    }
                });
            }

            window.addEventListener('scroll', highlightNav);
            if (mainWrap) mainWrap.addEventListener('scroll', highlightNav);

            navItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    closeSidebar();
                });
            });

            // Decrement Stat Counter after delete
            function decrementCounter(id) {
                var count = document.getElementById(id);
                if (!count) return;
                var current = parseInt(count.textContent, 10);
                count.textContent = String(Math.max(0, (isNaN(current) ? 1 : current) - 1));
            }

            function showDashboardMessage(section, message, isError) {
                var existing = section.querySelector('.js-dashboard-action-message');
                if (!existing) {
                    existing = document.createElement('div');
                    existing.className = 'js-dashboard-action-message';
                    var head = section.querySelector('.admin-section-header');
                    if (head) {
                        head.parentNode.insertBefore(existing, head.nextSibling);
                    } else {
                        section.insertBefore(existing, section.firstChild);
                    }
                }
                existing.className = 'js-dashboard-action-message ' + (isError ? 'dashboard-error' : 'dashboard-message');
                existing.style.marginBottom = '1.25rem';
                existing.textContent = message;
            }

            // Custom Styled Delete Confirmation Modal Handling
            var deleteModal = document.getElementById('adminDeleteModal');
            var deleteConfirmBtn = document.getElementById('adminDeleteConfirmBtn');
            var deleteCancelBtn = document.getElementById('adminDeleteCancelBtn');
            var deleteModalError = document.getElementById('adminDeleteModalError');
            var pendingForm = null;
            var triggerElement = null;

            function getModalFocusableElements() {
                if (!deleteModal) return [];
                var sel = 'button:not([disabled]), [href]:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';
                var elements = Array.prototype.slice.call(deleteModal.querySelectorAll(sel));
                return elements.filter(function(el) {
                    return el.offsetWidth > 0 && el.offsetHeight > 0 && window.getComputedStyle(el).visibility !== 'hidden';
                });
            }

            function openDeleteModal(form, message) {
                pendingForm = form;
                triggerElement = document.activeElement || (form ? form.querySelector('button[type="submit"]') : null);

                if (message) {
                    var msgEl = document.getElementById('adminDeleteModalMsg');
                    if (msgEl) msgEl.textContent = message;
                }
                if (deleteModalError) {
                    deleteModalError.classList.remove('is-active');
                    deleteModalError.textContent = '';
                }
                if (deleteConfirmBtn) {
                    deleteConfirmBtn.disabled = false;
                    deleteConfirmBtn.innerHTML = '<i class="fa fa-trash-o"></i> <span>Delete Record</span>';
                }
                if (deleteCancelBtn) {
                    deleteCancelBtn.disabled = false;
                }
                if (deleteModal) deleteModal.classList.add('is-active');

                // Initial Focus on Cancel button for safety
                if (deleteCancelBtn) {
                    try { deleteCancelBtn.focus(); } catch (err) {}
                    setTimeout(function() {
                        try { deleteCancelBtn.focus(); } catch (err) {}
                    }, 50);
                }
            }

            function closeDeleteModal() {
                pendingForm = null;
                if (deleteModal) deleteModal.classList.remove('is-active');
                if (deleteModalError) {
                    deleteModalError.classList.remove('is-active');
                    deleteModalError.textContent = '';
                }
                if (deleteConfirmBtn) {
                    deleteConfirmBtn.disabled = false;
                    deleteConfirmBtn.innerHTML = '<i class="fa fa-trash-o"></i> <span>Delete Record</span>';
                }
                if (deleteCancelBtn) {
                    deleteCancelBtn.disabled = false;
                }

                // Focus Restoration to original trigger element
                if (triggerElement && typeof triggerElement.focus === 'function' && document.body.contains(triggerElement)) {
                    try { triggerElement.focus(); } catch (err) {}
                }
                triggerElement = null;
            }

            if (deleteCancelBtn) deleteCancelBtn.addEventListener('click', closeDeleteModal);
            if (deleteModal) {
                deleteModal.addEventListener('click', function(e) {
                    if (e.target === deleteModal) closeDeleteModal();
                });
            }

            // Accessible Modal Keyboard & Focus Trap Handler (TAB / SHIFT+TAB Focus Trap, ESC = Cancel, ENTER = Button Action)
            document.addEventListener('keydown', function(e) {
                // Scope keyboard trap & handling strictly to when the delete confirmation modal is open
                if (!deleteModal || !deleteModal.classList.contains('is-active')) {
                    return;
                }

                if (e.key === 'Escape') {
                    e.preventDefault();
                    e.stopPropagation();
                    closeDeleteModal();
                    return;
                }

                if (e.key === 'Tab') {
                    var focusables = getModalFocusableElements();
                    if (focusables.length === 0) {
                        e.preventDefault();
                        return;
                    }
                    var firstEl = focusables[0];
                    var lastEl = focusables[focusables.length - 1];

                    if (e.shiftKey) { // SHIFT + TAB (Backwards)
                        if (document.activeElement === firstEl || !deleteModal.contains(document.activeElement)) {
                            e.preventDefault();
                            e.stopPropagation();
                            lastEl.focus();
                        }
                    } else { // TAB (Forwards)
                        if (document.activeElement === lastEl || !deleteModal.contains(document.activeElement)) {
                            e.preventDefault();
                            e.stopPropagation();
                            firstEl.focus();
                        }
                    }
                    return;
                }

                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();

                    // Prevent double delete if request is already in progress
                    if (!pendingForm || (deleteConfirmBtn && deleteConfirmBtn.disabled)) {
                        return;
                    }

                    // Standard focused button semantics: if Cancel is focused, activate Cancel; otherwise execute Delete Record
                    if (document.activeElement === deleteCancelBtn) {
                        closeDeleteModal();
                    } else if (deleteConfirmBtn) {
                        deleteConfirmBtn.click();
                    }
                }
            }, true);

            // Intercept form submissions for delete forms
            document.addEventListener('submit', function (event) {
                var form = event.target.closest('.js-dashboard-delete-form');
                if (!form) return;

                event.preventDefault();
                var confirmMsg = form.getAttribute('data-confirm') || 'Delete this record?';
                openDeleteModal(form, confirmMsg);
            });

            // Delete Confirmation Execution Handler
            if (deleteConfirmBtn) {
                deleteConfirmBtn.addEventListener('click', function() {
                    if (!pendingForm) return;

                    var targetForm = pendingForm;

                    // Set Loading State inside Modal
                    deleteConfirmBtn.disabled = true;
                    deleteCancelBtn.disabled = true;
                    deleteConfirmBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <span>Deleting...</span>';
                    if (deleteModalError) deleteModalError.classList.remove('is-active');

                    fetch(targetForm.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new FormData(targetForm)
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
                            var section = targetForm.closest('.admin-card-section') || targetForm.closest('.dashboard-section');
                            var row = targetForm.closest('tr');
                            var tbody = row ? row.parentNode : null;
                            var tableWrap = targetForm.closest('.admin-table-wrap') || targetForm.closest('.dashboard-table-wrap');

                            if (row) {
                                row.parentNode.removeChild(row);
                            }

                            decrementCounter(targetForm.getAttribute('data-counter'));
                            if (section) showDashboardMessage(section, data.message || 'Item deleted successfully.', false);

                            if (tbody && tbody.querySelectorAll('tr').length === 0 && tableWrap) {
                                tableWrap.outerHTML = '<div class="admin-empty-state"><i class="fa fa-info-circle admin-empty-icon"></i><div>' + (targetForm.getAttribute('data-empty') || 'No records yet.') + '</div></div>';
                            }

                            closeDeleteModal();
                        })
                        .catch(function (error) {
                            if (deleteModalError) {
                                deleteModalError.textContent = error.message || 'Unable to delete this record. Please try again.';
                                deleteModalError.classList.add('is-active');
                            }
                            var section = targetForm.closest('.admin-card-section') || targetForm.closest('.dashboard-section');
                            if (section) showDashboardMessage(section, error.message || 'Item could not be deleted.', true);

                            deleteConfirmBtn.disabled = false;
                            deleteCancelBtn.disabled = false;
                            deleteConfirmBtn.innerHTML = '<i class="fa fa-trash-o"></i> <span>Delete Record</span>';
                        });
                });
            }

            // Student Result Edit Populator
            var resultForm = document.getElementById('result-form');
            var resultSubmit = document.getElementById('result-submit');
            var resultCancel = document.getElementById('result-cancel-edit');

            function setResultField(name, value) {
                if (!resultForm) return;
                var field = resultForm.querySelector('[name="' + name + '"]');
                if (field) field.value = value || '';
            }

            function resetResultEdit() {
                if (!resultForm) return;
                resultForm.reset();
                setResultField('result_id', '0');
                if (resultSubmit) resultSubmit.innerHTML = '<i class="fa fa-check"></i> Save Result';
                if (resultCancel) resultCancel.style.display = 'none';
            }

            document.addEventListener('click', function (event) {
                var button = event.target.closest('.js-edit-result');
                if (!button || !resultForm) return;

                var row = button.closest('tr');
                if (!row) return;

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

                if (resultSubmit) resultSubmit.innerHTML = '<i class="fa fa-check"></i> Update Result';
                if (resultCancel) resultCancel.style.display = 'inline-flex';

                resultForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });

            if (resultCancel) resultCancel.addEventListener('click', resetResultEdit);

            // Admin Toast Notification System
            function showAdminToast(message, type, duration) {
                type = type || 'info';
                duration = duration || 5000;
                var container = document.getElementById('adminToastContainer');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'adminToastContainer';
                    container.className = 'admin-toast-container';
                    document.body.appendChild(container);
                }
                var toast = document.createElement('div');
                toast.className = 'admin-toast ' + type;
                var iconClass = 'fa-info-circle';
                if (type === 'success') iconClass = 'fa-check-circle';
                else if (type === 'error') iconClass = 'fa-exclamation-triangle';
                else if (type === 'warning') iconClass = 'fa-exclamation-circle';

                toast.innerHTML = '<i class="fa ' + iconClass + ' admin-toast-icon"></i>' +
                    '<div class="admin-toast-message">' + message + '</div>' +
                    '<button type="button" class="admin-toast-close" aria-label="Close">&times;</button>';

                var closeBtn = toast.querySelector('.admin-toast-close');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function () {
                        toast.classList.remove('is-active');
                        setTimeout(function () {
                            if (toast.parentNode) toast.parentNode.removeChild(toast);
                        }, 300);
                    });
                }

                container.appendChild(toast);
                void toast.offsetHeight;
                toast.classList.add('is-active');

                setTimeout(function () {
                    if (toast.parentNode) {
                        toast.classList.remove('is-active');
                        setTimeout(function () {
                            if (toast.parentNode) toast.parentNode.removeChild(toast);
                        }, 300);
                    }
                }, duration);
            }

            // Health Check AJAX Handler
            var healthBtn = document.getElementById('adminHealthCheckBtn');
            if (healthBtn) {
                healthBtn.addEventListener('click', function () {
                    if (healthBtn.disabled) return;
                    healthBtn.disabled = true;
                    var originalHtml = healthBtn.innerHTML;
                    healthBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <span>Checking...</span>';

                    fetch('backend/health.php?format=json', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function (res) {
                        return res.json().catch(function () {
                            throw new Error('Server returned non-JSON response (HTTP ' + res.status + ')');
                        });
                    })
                    .then(function (data) {
                        if (data && data.healthy) {
                            var phpVer = (data.checks && data.checks.php_version && data.checks.php_version.detail) ? data.checks.php_version.detail : '';
                            showAdminToast('System Healthy! PHP ' + phpVer + ' | Database: Connected | Storage: OK', 'success', 5000);
                        } else {
                            var errMsg = (data && data.error) ? data.error : 'Health check detected issues';
                            showAdminToast('Health Check Warning: ' + errMsg, 'warning', 6000);
                        }
                    })
                    .catch(function (err) {
                        showAdminToast('Health Check Failed: ' + (err.message || 'Network error'), 'error', 6000);
                    })
                    .finally(function () {
                        healthBtn.disabled = false;
                        healthBtn.innerHTML = originalHtml;
                    });
                });
            }

        }());
    </script>
</body>

</html>
