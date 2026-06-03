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
    <title>MBVM | Teachers</title>
    <meta charset="utf-8">
    <meta name="description" content="School Website.">
    <meta name="keywords" content="school,college,management,result,exam,attendace,hostel,admission,events">
    <meta name="author" content="H.R.Shadhin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

    <link rel="shortcut icon" href="/frontend/img/favicon.png">
    <link rel="stylesheet" href="/frontend/css/libs.css">
    <link rel="stylesheet" href="/frontend/css/modern.css">
    <style>
        .teacher-alert {
            background: #fff1f0;
            border: 1px solid #ffccc7;
            color: #b42318;
            padding: 16px;
            margin-bottom: 22px;
        }

        .teacher-empty {
            background: #fff;
            border: 1px solid #e8edf2;
            padding: 18px;
        }

        .teacher-meta {
            display: block;
            margin-top: 4px;
            color: rgba(255, 255, 255, .86);
            font-size: 13px;
        }

        .item-instructor .info-box p {
            min-height: 66px;
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
                    <a href="login.html" class="fa fa-user login-icon"></a>
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
                            <li><a href="teachers.php" class="active">Teachers</a></li>
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
        <main>
            <div class="container">
                <section class="clear-fix">
                    <h2>Meet our Teachers</h2>

                    <?php if ($dbError !== ''): ?>
                        <div class="teacher-alert"><?= e($dbError) ?></div>
                    <?php elseif ($teachers === []): ?>
                        <div class="teacher-empty">No teacher profiles have been added yet.</div>
                    <?php else: ?>
                        <?php foreach (array_chunk($teachers, 2) as $teacherRow): ?>
                            <div class="grid-col-row">
                                <?php foreach ($teacherRow as $index => $teacher): ?>
                                    <?php $colors = ['bg-color-1', 'bg-color-3', 'bg-color-4', 'bg-color-6']; ?>
                                    <div class="grid-col grid-col-6 margin-top-20">
                                        <div class="item-instructor <?= e($colors[((int) ($teacher['id'] ?? $index)) % count($colors)]) ?>">
                                            <div class="instructor-avatar">
                                                <img src="<?= e((string) ($teacher['photo_path'] ?: '/frontend/uploads/teacher/210x220-img-1.jpg')) 
                                                ?>" alt="<?= e((string) ($teacher['name'] ?? 'Teacher')) ?>">
                                            </div>
                                            <div class="info-box">
                                                <h3><?= e((string) ($teacher['name'] ?? 'Teacher')) ?></h3>
                                                <span class="instructor-profession"><?= e((string) ($teacher['designation'] ?? 'Teacher')) ?></span>
                                                <?php if (!empty($teacher['subject']) || !empty($teacher['qualification'])): ?>
                                                    <span class="teacher-meta">
                                                        <?= e(trim((string) ($teacher['subject'] ?? '') . ' ' . (string) ($teacher['qualification'] ?? ''))) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <div class="divider"></div>
                                                <p><?= e((string) ($teacher['bio'] ?: 'Dedicated faculty supporting students with discipline, care, and regular guidance.')) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>
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
</body>

</html>
