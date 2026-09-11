<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function event_embed_url(string $url): string
{
    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~', $url, $match)) {
        return 'https://www.youtube.com/embed/' . $match[1];
    }

    return $url;
}

$defaultEvents = [
    ['title' => 'Farewell Party Dance Performance 2025-26 | Class 10th', 
    'event_date' => '2025-02-25', 'event_time' => '10:30 AM', 
    'description' => 'A memorable and energetic dance performance by Class 10th students, filled with enthusiasm, teamwork, and joyful expressions.', 
    'youtube_url' => 'https://www.youtube.com/embed/yUYft0PmWXQ?start=116', 'image_path' => ''],
    
    ['title' => 'Farewell Party 2025-26 | Class 10th', 
    'event_date' => '2026-02-09', 'event_time' => '11:15 AM', 
    'description' => 'A special farewell celebration honoring achievements, friendships, and beautiful memories from the school journey.', 
    'youtube_url' => '', 'image_path' => ''],

    ['title' => 'Rakshabandhan Celebration 2025', 'event_date' => '2025-08-09',
    'event_time' => '03:30 PM', 
    'description' => 'A joyful celebration of love, care, smiles, and festive togetherness through a special Raksha Bandhan program.',
    'youtube_url' => 'https://www.youtube.com/embed/GFrKtrTFpY4', 'image_path' => ''],
    
    ['title' => "Children's Day Kabaddi Competition", 
    'event_date' => '2024-11-14', 'event_time' => '10:00 AM',
    'description' => 'An energetic Kabaddi competition between Class 5th and Class 6th students, full of teamwork and sportsmanship.', 
    'youtube_url' => 'https://www.youtube.com/embed/dX9iLQryxlY', 'image_path' => ''],
    
    ['title' => 'Independence Day 2024 | Patriotic Dance', 
    'event_date' => '2025-08-15', 'event_time' => '04:00 PM', 
    'description' => 'A powerful patriotic dance performance by Payal and Group, expressing love, respect, and dedication for the nation.', 
    'youtube_url' => 'https://www.youtube.com/embed/Tfm10oCS8Bg', 'image_path' => ''],
    
    ['title' => 'Republic Day 2023 | Barsa and Group', 
    'event_date' => '2023-01-26', 'event_time' => '10:30 AM', 
    'description' => 'A cultural performance celebrating the pride, unity, and diversity of our nation with patriotic spirit.', 
    'youtube_url' => 'https://www.youtube.com/embed/TtT0pwCnfnQ', 'image_path' => ''],
];

try {
    $events = array_merge(mbvm_load_events(), $defaultEvents);
} catch (Throwable $error) {
    $events = $defaultEvents;
}
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <title>MBVM | School Events & Celebrations</title>
    <meta charset="utf-8">
    <meta name="description" content="Explore events, celebrations, sports competitions, and cultural activities at Madhusudan Bal Vidya Mandir.">
    <meta name="keywords" content="events, celebrations, school, MBVM, Mungthala, Abu Road, sports">
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
				<a href="teachers.php">Teachers</a>
				<a href="events.php" class="active">Events</a>
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

    <main class="section-pad">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3.5rem;">
                <span class="gold-line"></span>
                <h1 class="section-title">School Events & Celebrations</h1>
                <p class="section-subtitle" style="max-width: 36rem; margin-left: auto; margin-right: auto;">Celebrations, competitions, cultural programs, and memorable student performances from Madhusudan Bal Vidya Mandir.</p>
            </div>

            <div class="grid-3" style="gap: 2rem;">
                <?php foreach ($events as $event): ?>
                    <div class="card" style="display: flex; flex-direction: column;">
                        <div style="aspect-ratio: 16/9; position: relative; background: var(--mbvm-warm); overflow: hidden;">
                            <?php if (!empty($event['youtube_url'])): ?>
                                <iframe src="<?= e(event_embed_url((string) $event['youtube_url'])) ?>" title="<?= e((string) $event['title']) ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position: absolute; inset: 0; width: 100%; height: 100%; border: 0;"></iframe>
                            <?php elseif (!empty($event['image_path'])): ?>
                                <img src="<?= e((string) $event['image_path']) ?>" alt="<?= e((string) $event['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--mbvm-blue);">
                                    <i class="fa fa-calendar-check-o"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.8125rem; color: var(--mbvm-muted);">
                                <span><i class="fa fa-calendar" style="color: var(--mbvm-blue);"></i> <?= e(date('j M, Y', strtotime((string) ($event['event_date'] ?? 'now')))) ?></span>
                                <?php if (!empty($event['event_time'])): ?>
                                    <span><i class="fa fa-clock-o"></i> <?= e((string) $event['event_time']) ?></span>
                                <?php endif; ?>
                            </div>

                            <h3 style="font-size: 1.125rem; line-height: 1.4; margin-bottom: 0.75rem; color: var(--mbvm-ink);"><?= e((string) $event['title']) ?></h3>
                            <p style="font-size: 0.875rem; color: var(--mbvm-body); line-height: 1.6; margin-top: auto; border-top: 1px solid var(--mbvm-border); padding-top: 0.75rem;">
                                <?= e((string) $event['description']) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

		<?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="frontend/js/libs.js"></script>
</body>

</html>
