<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Approved category definitions & label mapping
$categoryConfig = [
    'campus'               => ['slug' => 'campus',               'label' => 'CAMPUS',                      'tag' => 'Campus'],
    'class_memories'       => ['slug' => 'class_memories',       'label' => 'CLASS MEMORIES',              'tag' => 'Class Memories'],
    'sports_celebration'   => ['slug' => 'sports_celebration',   'label' => 'SPORTS CELEBRATION',          'tag' => 'Sports Celebration'],
    'independence_parade'  => ['slug' => 'independence_parade',  'label' => 'INDEPENDENCE PARADE',         'tag' => 'Independence Parade'],
    'picnic_and_tour'      => ['slug' => 'picnic_and_tour',      'label' => 'PICNIC & TOUR',               'tag' => 'Picnic & Tour'],
    'farewell_cultural'    => ['slug' => 'farewell_cultural',    'label' => 'FAREWELL & CULTURAL',         'tag' => 'Farewell & Cultural'],
    'celebrations'         => ['slug' => 'celebrations',         'label' => 'CELEBRATIONS',                'tag' => 'Celebrations'],
    'achievements'          => ['slug' => 'achievements',         'label' => 'ACHIEVEMENTS',                'tag' => 'Achievements'],
    'activities'           => ['slug' => 'activities',           'label' => 'ACTIVITIES',                  'tag' => 'Activities'],
];

function normalizeCategoryKey(string $cat): string
{
    $c = strtolower(trim($cat));
    if (strpos($c, 'sport') !== false) return 'sports_celebration';
    if (strpos($c, 'independ') !== false || strpos($c, 'parade') !== false) return 'independence_parade';
    if (strpos($c, 'class') !== false || strpos($c, 'memor') !== false) return 'class_memories';
    if (strpos($c, 'picnic') !== false || strpos($c, 'tour') !== false || strpos($c, 'trip') !== false) return 'picnic_and_tour';
    if (strpos($c, 'farewell') !== false || strpos($c, 'cultural') !== false) return 'farewell_cultural';
    if (strpos($c, 'celebrat') !== false) return 'celebrations';
    if (strpos($c, 'achiev') !== false || strpos($c, 'award') !== false) return 'achievements';
    if (strpos($c, 'activit') !== false) return 'activities';
    if (strpos($c, 'campus') !== false || strpos($c, 'build') !== false) return 'campus';
    return preg_replace('/[^a-z0-9_]+/', '_', $c) ?: 'campus';
}

$defaultItems = [
    [
        'title' => 'Sports Celebration',
        'category' => 'sports_celebration',
        'image_path' => 'frontend/uploads/gallery/1.jpg',
    ],
    [
        'title' => 'Independence Parade',
        'category' => 'independence_parade',
        'image_path' => 'frontend/uploads/gallery/2.jpg',
    ],
    [
        'title' => 'Class Memories',
        'category' => 'class_memories',
        'image_path' => 'frontend/uploads/gallery/3.jpg',
    ],
    [
        'title' => 'Picnic and Tour',
        'category' => 'picnic_and_tour',
        'image_path' => 'frontend/uploads/gallery/4.jpg',
    ],
    [
        'title' => 'Sports Celebration',
        'category' => 'sports_celebration',
        'image_path' => 'frontend/uploads/gallery/5.jpg',
    ],
    [
        'title' => 'Farewell and Cultural Program',
        'category' => 'farewell_cultural',
        'image_path' => 'frontend/uploads/gallery/6.jpg',
    ],
];

try {
    $dbItems = mbvm_load_gallery_items();
    $galleryItems = $dbItems !== [] ? $dbItems : $defaultItems;
} catch (Throwable $error) {
    $galleryItems = $defaultItems;
}

// Calculate unique categories for filter bar
$categoriesMap = [];
foreach ($galleryItems as $item) {
    $normSlug = normalizeCategoryKey((string) ($item['category'] ?? 'campus'));
    $categoriesMap[$normSlug] = ($categoriesMap[$normSlug] ?? 0) + 1;
}
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <title>MBVM | Photo Gallery & Campus Life</title>
    <meta charset="utf-8">
    <meta name="description" content="Explore campus life, activities, celebrations, and achievements at Madhusudan Bal Vidya Mandir Mungthala.">
    <meta name="keywords" content="gallery, photos, campus, events, activities, MBVM, Mungthala, Abu Road, school">
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
				<a href="events.php">Events</a>
				<a href="gallery.php" class="active">Gallery</a>
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

    <!-- Main Gallery Section -->
    <main class="gallery-page-wrap">
        <div class="gallery-container">

            <!-- Section Introduction -->
            <div class="gallery-header">
                <span class="gallery-eyebrow">OUR GALLERY</span>
                <h1 class="gallery-main-title">Moments <span>That Matter</span></h1>
                <p class="gallery-header-desc">
                    Explore memorable moments, celebrations, activities, and achievements from Madhusudan Bal Vidya Mandir.
                </p>
            </div>

            <!-- Dynamic Category Filter Bar -->
            <?php if (count($categoriesMap) > 0): ?>
                <div class="gallery-filter-bar" id="galleryFilterBar">
                    <button class="gallery-filter-btn active" data-filter="all">
                        ALL <span class="gallery-filter-count"><?= count($galleryItems) ?></span>
                    </button>
                    <?php foreach ($categoriesMap as $catSlug => $catCount): 
                        $catInfo = $categoryConfig[$catSlug] ?? [
                            'slug' => $catSlug,
                            'label' => strtoupper(str_replace('_', ' ', $catSlug)),
                            'tag' => ucfirst(str_replace('_', ' ', $catSlug))
                        ];
                    ?>
                        <button class="gallery-filter-btn" data-filter="<?= e($catSlug) ?>">
                            <?= e($catInfo['label']) ?> <span class="gallery-filter-count"><?= (int) $catCount ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Premium Gallery Grid -->
            <div class="gallery-grid" id="galleryGrid">
                <?php foreach ($galleryItems as $index => $item): 
                    $rawPath = (string) ($item['image_path'] ?? '');
                    $cleanPath = e(ltrim($rawPath, '/'));
                    $title = e((string) ($item['title'] ?? 'School Gallery Photo'));
                    $normSlug = normalizeCategoryKey((string) ($item['category'] ?? 'campus'));
                    $catInfo = $categoryConfig[$normSlug] ?? [
                        'slug' => $normSlug,
                        'label' => strtoupper(str_replace('_', ' ', $normSlug)),
                        'tag' => ucfirst(str_replace('_', ' ', $normSlug))
                    ];
                    $categoryTag = e($catInfo['tag']);
                    $isFeatured = ($index === 0) ? 'featured-card' : '';
                ?>
                    <div class="gallery-card <?= $isFeatured ?>" data-category="<?= e($normSlug) ?>" data-index="<?= (int) $index ?>">
                        <a href="<?= $cleanPath ?>" class="gallery-link js-gallery-trigger" data-title="<?= $title ?>" data-category="<?= $categoryTag ?>" title="<?= $title ?>">
                            <img src="<?= $cleanPath ?>" alt="<?= $title ?>" class="gallery-img" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            
                            <!-- Fallback Container if Image Fails -->
                            <div class="gallery-img-fallback" style="display: none;">
                                <i class="fa fa-picture-o"></i>
                                <span><?= $title ?></span>
                            </div>

                            <!-- Hover Overlay -->
                            <div class="gallery-overlay">
                                <div class="gallery-zoom-icon" aria-label="Expand image">
                                    <i class="fa fa-search-plus"></i>
                                </div>
                                <div class="gallery-info-overlay">
                                    <span class="gallery-category-tag"><?= $categoryTag ?></span>
                                    <h3 class="gallery-item-title"><?= $title ?></h3>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Dynamic Stats Row -->
            <div class="gallery-stats-banner">
                <div class="gallery-stats-grid">
                    <div class="gallery-stat-item">
                        <span class="gallery-stat-val"><?= count($galleryItems) ?>+</span>
                        <span class="gallery-stat-lbl">Captured Moments</span>
                    </div>
                    <div class="gallery-stat-item">
                        <span class="gallery-stat-val"><?= count($categoriesMap) ?></span>
                        <span class="gallery-stat-lbl">Activity Categories</span>
                    </div>
                    <div class="gallery-stat-item">
                        <span class="gallery-stat-val">2004</span>
                        <span class="gallery-stat-lbl">Year Established</span>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Accessible Fullscreen Lightbox Modal -->
    <div class="mbvm-lightbox-modal" id="mbvmLightbox" role="dialog" aria-modal="true" aria-label="Photo Lightbox">
        <button class="mbvm-lightbox-close" id="lightboxClose" aria-label="Close Lightbox">&times;</button>
        <button class="mbvm-lightbox-prev" id="lightboxPrev" aria-label="Previous Image">&lsaquo;</button>
        <button class="mbvm-lightbox-next" id="lightboxNext" aria-label="Next Image">&rsaquo;</button>
        
        <div class="mbvm-lightbox-content">
            <img src="" alt="" class="mbvm-lightbox-img" id="lightboxImg">
            <div class="mbvm-lightbox-caption">
                <div class="mbvm-lightbox-category" id="lightboxCat"></div>
                <h3 class="mbvm-lightbox-title" id="lightboxTitle"></h3>
            </div>
        </div>
    </div>

		<?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="frontend/js/libs.js"></script>
	<script>
        // Gallery Filter & Lightbox Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterBtns = document.querySelectorAll('.gallery-filter-btn');
            const galleryCards = document.querySelectorAll('.gallery-card');
            const triggers = Array.from(document.querySelectorAll('.js-gallery-trigger'));

            // Filter Handler
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const selectedFilter = this.getAttribute('data-filter');

                    galleryCards.forEach(card => {
                        const cardCat = card.getAttribute('data-category');
                        if (selectedFilter === 'all' || cardCat === selectedFilter) {
                            card.classList.remove('is-hidden');
                        } else {
                            card.classList.add('is-hidden');
                        }
                    });
                });
            });

            // Lightbox Modal Logic
            const lightbox = document.getElementById('mbvmLightbox');
            const lightboxImg = document.getElementById('lightboxImg');
            const lightboxTitle = document.getElementById('lightboxTitle');
            const lightboxCat = document.getElementById('lightboxCat');
            const closeBtn = document.getElementById('lightboxClose');
            const prevBtn = document.getElementById('lightboxPrev');
            const nextBtn = document.getElementById('lightboxNext');

            let currentIndex = 0;
            let visibleTriggers = [];

            function updateVisibleTriggers() {
                visibleTriggers = triggers.filter(el => {
                    const card = el.closest('.gallery-card');
                    return card && !card.classList.contains('is-hidden');
                });
            }

            function openLightbox(index) {
                updateVisibleTriggers();
                if (visibleTriggers.length === 0) return;
                
                currentIndex = (index + visibleTriggers.length) % visibleTriggers.length;
                const activeTrigger = visibleTriggers[currentIndex];

                const imgSrc = activeTrigger.getAttribute('href');
                const title = activeTrigger.getAttribute('data-title') || '';
                const category = activeTrigger.getAttribute('data-category') || '';

                lightboxImg.src = imgSrc;
                lightboxImg.alt = title;
                lightboxTitle.textContent = title;
                lightboxCat.textContent = category;

                lightbox.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                lightbox.classList.remove('is-open');
                document.body.style.overflow = '';
            }

            triggers.forEach((trigger) => {
                trigger.addEventListener('click', function(e) {
                    e.preventDefault();
                    updateVisibleTriggers();
                    const visibleIdx = visibleTriggers.indexOf(trigger);
                    openLightbox(visibleIdx >= 0 ? visibleIdx : 0);
                });
            });

            closeBtn?.addEventListener('click', closeLightbox);
            
            lightbox?.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    closeLightbox();
                }
            });

            prevBtn?.addEventListener('click', function(e) {
                e.stopPropagation();
                openLightbox(currentIndex - 1);
            });

            nextBtn?.addEventListener('click', function(e) {
                e.stopPropagation();
                openLightbox(currentIndex + 1);
            });

            // Keyboard Shortcuts
            document.addEventListener('keydown', function(e) {
                if (!lightbox.classList.contains('is-open')) return;

                if (e.key === 'Escape') {
                    closeLightbox();
                } else if (e.key === 'ArrowLeft') {
                    openLightbox(currentIndex - 1);
                } else if (e.key === 'ArrowRight') {
                    openLightbox(currentIndex + 1);
                }
            });
        });
	</script>
</body>

</html>

