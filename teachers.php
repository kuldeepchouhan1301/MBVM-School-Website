<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';

$teachers = [];
$dbError = '';

try {
    $teachers = mbvm_load_teachers(true);
} catch (Throwable $error) {
    $dbError = 'Teacher profiles are not available yet. Please run backend/schema.sql in phpMyAdmin.';
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
    <title>MBVM | Dedicated Teachers & Faculty</title>
    <meta charset="utf-8">
    <meta name="description" content="Meet the experienced and dedicated faculty at Madhusudan Bal Vidya Mandir Mungthala.">
    <meta name="keywords" content="teachers, faculty, school, MBVM, Mungthala, Sirohi, Rajasthan">
    <meta name="author" content="Kuldeep Chouhan">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="frontend/img/favicon.png">
    <link rel="stylesheet" href="frontend/css/libs.css">
    <link rel="stylesheet" href="frontend/css/modern.css">
</head>

<body>

	<!-- Top Information Bar -->
	<div class="top-bar">
		<div class="header-container top-bar-inner">
			<div class="top-bar-contact">
				<a href="tel:+919982754110"><i class="fa fa-phone"></i> +91 9982754110</a>
				<a href="mailto:mbvm.mungthala1@gmail.com"><i class="fa fa-envelope-o"></i> mbvm.mungthala1@gmail.com</a>
			</div>
			<div class="top-bar-links">
				<a href="https://www.youtube.com/@hindimid.mbvmschoolmoongth834/" target="_blank" rel="noopener" title="YouTube"><i class="fa fa-youtube-play"></i></a>
				<div class="lang-dropdown">
					<i class="fa fa-globe"></i> EN <i class="fa fa-angle-down" style="font-size: 0.75rem;"></i>
				</div>
				<a href="login.html" title="Admin Login"><i class="fa fa-user"></i> Login</a>
			</div>
		</div>
	</div>

	<!-- Navigation Header -->
	<header class="main-header">
		<div class="header-container main-header-inner">
			<a href="index.html" class="brand-logo-wrap">
				<img src="frontend/img/logo.png" alt="MBVM Logo">
				<div>
					<div class="brand-title">Madhusudan Bal Vidya Mandir</div>
					<div class="brand-subtitle">Mungthala, Abu Road</div>
				</div>
			</a>

			<nav class="main-nav" id="mainNav">
				<a href="index.html">Home</a>
				<a href="class.html">Classes</a>
				<a href="teachers.php" class="active">Teachers</a>
				<a href="events.php">Events</a>
				<a href="gallery.php">Gallery</a>
				<a href="results.php">Results</a>
				<a href="faq.html">FAQ</a>
				<a href="timeline.html">Timeline</a>
				<a href="contact-us.html">Contact Us</a>
				<a href="admission.html" class="btn-primary" style="margin-left: 0.5rem;">Apply Now</a>
			</nav>

			<button class="mobile-nav-toggle" id="navToggle" aria-label="Toggle navigation">
				<i class="fa fa-bars" style="font-size: 1.25rem;"></i>
			</button>
		</div>
	</header>

	<!-- Teachers Main Section -->
	<section class="teachers-section-wrap">
		<div class="teachers-container">
			<div class="teachers-header">
				<span class="teachers-eyebrow">OUR FACULTY</span>
				<h1 class="teachers-main-title">Meet Our Dedicated <span>Teachers</span></h1>
				<p class="teachers-header-desc">Our faculty members are committed to guiding students with care, discipline, and academic excellence.</p>
			</div>

			<?php if ($dbError !== ''): ?>
				<div style="background: #fff1f0; border: 1px solid #ffccc7; color: #b42318; padding: 1.25rem; border-radius: 12px; margin-bottom: 2rem; text-align: center;">
					<?= e($dbError) ?>
				</div>
			<?php elseif ($teachers === []): ?>
				<div class="teacher-card" style="padding: 3rem; text-align: center; color: var(--mbvm-muted); grid-column: 1 / -1;">
					No teacher profiles have been added yet.
				</div>
			<?php else: ?>
				<div class="teachers-cards-grid">
					<?php foreach ($teachers as $teacher): 
						$rawPath = (string) ($teacher['photo_path'] ?? '');
						$cleanPath = ltrim($rawPath, '/');
						$imageExists = $cleanPath !== '' && file_exists(__DIR__ . '/' . $cleanPath);
					?>
						<div class="teacher-card">
							<div class="teacher-card-img-wrap">
								<?php if ($imageExists): ?>
									<img src="<?= e($cleanPath) ?>" alt="<?= e((string) ($teacher['name'] ?? 'Teacher')) ?>" class="teacher-card-img">
								<?php else: ?>
									<div class="teacher-card-img-placeholder">
										<div class="teacher-avatar-circle">
											<i class="fa fa-graduation-cap"></i>
										</div>
									</div>
								<?php endif; ?>
								<div class="teacher-card-badge">
									<i class="fa fa-graduation-cap"></i>
								</div>
							</div>
							<div class="teacher-card-body">
								<h3 class="teacher-card-name"><?= e((string) ($teacher['name'] ?? 'Teacher')) ?></h3>
								<div class="teacher-card-role"><?= e((string) ($teacher['designation'] ?? 'Faculty Member')) ?></div>
								
								<?php if (!empty($teacher['subject']) || !empty($teacher['qualification'])): ?>
									<div class="teacher-card-subject">
										<i class="fa fa-book"></i> <?= e(trim((string) ($teacher['subject'] ?? '') . ' ' . (string) ($teacher['qualification'] ?? ''))) ?>
									</div>
								<?php endif; ?>

								<p class="teacher-card-bio">
									<?= e((string) ($teacher['bio'] ?: 'Dedicated faculty supporting students with discipline, care, and regular guidance.')) ?>
								</p>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

		<?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="frontend/js/libs.js"></script>
</body>

</html>
