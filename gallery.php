<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$defaultItems = [];
for ($i = 1; $i <= 6; $i++) {
    $defaultItems[] = [
        'title' => 'School Gallery ' . $i,
        'category' => 'students',
        'image_path' => '/frontend/uploads/gallery/' . $i . '.jpg',
    ];
}

try {
    $galleryItems = array_merge(mbvm_load_gallery_items(), $defaultItems);
} catch (Throwable $error) {
    $galleryItems = $defaultItems;
}
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <title>MBVM | Gallery</title>
    <meta charset="utf-8">
    <meta name="description" content="School Website.">
    <meta name="keywords" content="school,college,management,result,exam,attendace,hostel,admission,events">
    <meta name="author" content="H.R.Shadhin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <link rel="shortcut icon" href="/frontend/img/favicon.png">
    <link rel="stylesheet" href="/frontend/css/libs.css">
    <link rel="stylesheet" href="/frontend/css/modern.css">
    <style>
        .gallery-page .isotope .item {
            width: calc(33.333% - 30px);
            margin-left: 30px;
            margin-bottom: 30px;
        }

        .gallery-page .isotope .item .picture {
            aspect-ratio: 4 / 3;
            background: #f2f2f2;
        }

        .gallery-page .isotope .item .picture img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media screen and (max-width: 980px) {
            .gallery-page .isotope .item {
                width: calc(50% - 20px);
                margin-left: 20px;
                margin-bottom: 20px;
            }
        }

        @media screen and (max-width: 479px) {
            .gallery-page .isotope .item {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="gallery-page">
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
                            <li><a href="events.php">Events</a></li>
                            <li><a href="gallery.php" class="active">Gallery</a></li>
                            <li><a href="contact-us.html">Contact Us</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="page-content">
        <div class="grid-row">
            <main>
                <div class="grid-col-row">
                    <div class="isotope">
                        <?php foreach ($galleryItems as $item): ?>
                            <?php $category = preg_replace('/[^a-z0-9_-]+/i', ' ', (string) ($item['category'] ?? '')); ?>
                            <div class="item <?= e((string) $category) ?>">
                                <div class="picture">
                                    <div class="hover-effect"></div>
                                    <div class="link-cont">
                                        <a href="<?= e((string) $item['image_path']) ?>" class="fancy fa fa-search"></a>
                                    </div>
                                    <img src="<?= e((string) $item['image_path']) ?>" data-at2x="<?= e((string) $item['image_path']) 
                                    ?>" alt="<?= e((string) ($item['title'] ?? 'School gallery photo')) ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>
        </div>
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
