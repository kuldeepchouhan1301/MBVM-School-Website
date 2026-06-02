<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function event_embed_url(string $url): string
{
    if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)
    ([A-Za-z0-9_-]{6,})~', $url, $match)) {
        return 'https://www.youtube.com/embed/' . $match[1];
    }

    return $url;
}

$defaultEvents = [
    ['title' => 'Farewell Party Dance Performance 2025-26 | Class 10th', 
    'event_date' => '2025-02-25', 'event_time' => '10:30 AM', 
    'description' => 'A memorable and energetic dance performance by Class 10th students, 
    filled with enthusiasm, teamwork, and joyful expressions.', 
    'youtube_url' => 'https://www.youtube.com/embed/yUYft0PmWXQ?start=116', 'image_path' => ''],
    
    ['title' => 'Farewell Party 2025-26 | Class 10th', 
    'event_date' => '2026-02-09', 'event_time' => '11:15 AM', 
    'description' => 'A special farewell celebration honoring achievements, 
    friendships, and beautiful memories from the school journey.', 
    'youtube_url' => '', 'image_path' => ''],

    ['title' => 'Rakshabandhan Celebration 2025', 'event_date' => '2025-08-09',
    'event_time' => '03:30 PM', 
    'description' => 'A joyful celebration of love, care, smiles, 
    and festive togetherness through a special Raksha Bandhan program.',
    'youtube_url' => 'https://www.youtube.com/embed/GFrKtrTFpY4', 'image_path' => ''],
    
    ['title' => "Children's Day Kabaddi Competition", 
    'event_date' => '2024-11-14', 'event_time' => '10:00 AM',
    'description' => 'An energetic Kabaddi competition between Class 5th and Class 6th students, 
    full of teamwork and sportsmanship.', 
    'youtube_url' => 'https://www.youtube.com/embed/dX9iLQryxlY', 'image_path' => ''],
    
    ['title' => 'Independence Day 2024 | Patriotic Dance', 
    'event_date' => '2025-08-15', 'event_time' => '04:00 PM', 
    'description' => 'A powerful patriotic dance performance by Payal and Group, 
    expressing love, respect, and dedication for the nation.', 
    'youtube_url' => 'https://www.youtube.com/embed/Tfm10oCS8Bg', 'image_path' => ''],
    
    ['title' => 'Republic Day 2023 | Barsa and Group', 
    'event_date' => '2023-01-26', 'event_time' => '10:30 AM', 
    'description' => 'A cultural performance celebrating the pride, unity, 
    and diversity of our nation with patriotic spirit.', 
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
    <title>MBVM | Events</title>
    <meta charset="utf-8">
    <meta name="description" content="School Website.">
    <meta name="keywords" content="school,college,management,result,exam,attendance,hostel,admission,events">
    <meta name="author" content="H.R.Shadhin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <link rel="shortcut icon" href="/frontend/img/favicon.png">
    <link rel="stylesheet" href="/frontend/css/libs.css">
    <link rel="stylesheet" href="/frontend/css/modern.css">
    <style>
        .event-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <header>
        <div class="page-header-top">
            <div class="grid-row clear-fix">
                <address>
                    <a href="tel:+919982754110" class="phone-number"><i class="fa fa-phone"></i>+91 9982754110</a>
                    <a href="mailto:mbvm.mungthala1@gmail.com" class="email"><i class="fa fa-envelope-o"></i><span>mbvm.mungthala1@gmail.com</span></a>
                </address>
                <div class="header-top-panel">
                    <a href="login.html" class="fa fa-user login-icon"></a>
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
                            <li><a href="events.php" class="active">Events</a></li>
                            <li><a href="gallery.php">Gallery</a></li>
                            <li><a href="contact-us.html">Contact Us</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="page-content events-page">
        <div class="grid-row">
            <main>
                <section class="page-title events-title">
                    <h1>School Events</h1>
                    <p>Celebrations, competitions, cultural programs, and memorable student performances from Madhusudan Bal Vidya Mandir.</p>
                </section>

                <section class="events-section">
                    <div class="grid-col-row clear-fix events-grid">
                        <?php foreach ($events as $event): ?>
                            <div class="grid-col grid-col-4">
                                <article class="blog-post event-card">
                                    <div class="post-info event">
                                        <div class="date-post">
                                            <div class="time"><?= e((string) ($event['event_time'] ?? '')) ?></div>
                                        </div>
                                        <div class="post-info-main">
                                            <div class="event-info"><?= e(date('j M, Y', strtotime((string) ($event['event_date'] ?? 'now')))) ?></div>
                                        </div>
                                    </div>
                                    <?php if (!empty($event['youtube_url'])): ?>
                                        <div class="event-media">
                                            <iframe src="<?= e(event_embed_url((string) $event['youtube_url'])) ?>" title="<?= e((string) $event['title']) ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                        </div>
                                    <?php elseif (!empty($event['image_path'])): ?>
                                        <div class="event-media">
                                            <img src="<?= e((string) $event['image_path']) ?>" alt="<?= e((string) $event['title']) ?>">
                                        </div>
                                    <?php else: ?>
                                        <div class="event-media event-placeholder">
                                            <i class="fa fa-calendar-check-o"></i>
                                        </div>
                                    <?php endif; ?>
                                    <h3><?= e((string) $event['title']) ?></h3>
                                    <p><?= e((string) $event['description']) ?></p>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <footer>
        <div class="grid-row">
            <div class="grid-col-row clear-fix">
                <section class="grid-col grid-col-6 footer-about">
                    <h2 class="corner-radius">About Us</h2>
                    <p>MADHUSUDAN BAL VIDYA MANDIR MUNGTHALA was established in 2004 and it is managed by the Pvt. Unaided.</p>
                </section>
                <section class="grid-col grid-col-6 footer-links">
                    <h2 class="corner-radius">Help Links <i class="site"></i></h2>
                    <ul class="clear-fix">
                        <li><a href="faq.html">FAQ</a></li>
                        <li><a href="admission.html">Admission</a></li>
                        <li><a href="results.php">Results</a></li>
                        <li><a href="timeline.html">Timeline</a></li>
                        <li><a href="gallery.php">Gallery</a></li>
                        <li><a href="contact-us.html">Contact Us</a></li>
                    </ul>
                </section>
            </div>
        </div>
    </footer>
    <script src="/frontend/js/libs.js"></script>
</body>

</html>
