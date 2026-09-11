<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';

$result = null;
$message = '';
$searched = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($searched) {
    $className = mbvm_clean((string) ($_POST['class'] ?? ''));
    $sessionYear = mbvm_clean((string) ($_POST['session'] ?? ''));
    $registrationNo = mbvm_clean((string) ($_POST['regNo'] ?? ''));

    if ($className === '' || $sessionYear === '' || $registrationNo === '') {
        $message = 'Please fill all result search fields.';
    } else {
        try {
            $result = mbvm_find_result($className, $sessionYear, $registrationNo);
            if ($result === null) {
                $message = 'No result found for these details. Please verify your Registration Number, Class, and Session Year.';
            }
        } catch (Throwable $error) {
            $message = 'Result database is not ready. Please contact school office.';
        }
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE HTML>
<html lang="en">

<head>
    <title>MBVM | Student Examination Results</title>
    <meta charset="utf-8">
    <meta name="description" content="Check student examination results and scorecards for Madhusudan Bal Vidya Mandir Mungthala.">
    <meta name="keywords" content="results, exam, student, scorecard, MBVM, Mungthala, Abu Road">
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
				<a href="gallery.php">Gallery</a>
				<a href="results.php" class="active">Results</a>
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

	<main class="results-page-wrap">
		<div class="results-container">
			
			<!-- Top Back Navigation Bar -->
			<div class="results-top-nav">
				<a href="index.html" class="btn-back-home">
					<i class="fa fa-arrow-left"></i> Back to Home
				</a>
				<?php if ($searched): ?>
					<a href="results.php" class="btn-back-search">
						<i class="fa fa-refresh"></i> New Search
					</a>
				<?php endif; ?>
			</div>

			<!-- Results Hero Header Section -->
			<div class="results-hero-header">
				<div class="results-hero-badge">
					<i class="fa fa-graduation-cap"></i> STUDENT RESULTS
				</div>
				<h1 class="results-hero-title">
					Check Student <span>Examination Result</span>
				</h1>
				<p class="results-hero-subtitle">
					View your academic report card instantly using your class, session year and registration number.
				</p>

				<div class="results-feature-pills">
					<div class="results-feature-pill">
						<i class="fa fa-check-circle"></i> Fast Result Lookup
					</div>
					<div class="results-feature-pill">
						<i class="fa fa-shield"></i> Secure Academic Records
					</div>
					<div class="results-feature-pill">
						<i class="fa fa-file-text-o"></i> Session-wise Report Cards
					</div>
				</div>
			</div>

			<!-- Glass Search Card Section -->
			<div class="results-search-card-wrap">
				<div class="results-search-card">
					<div class="search-card-header">
						<h2 class="search-card-title">
							<i class="fa fa-search"></i> Search Your Result
						</h2>
						<p class="search-card-subtitle">
							Select your class and session year, then enter your registration number to generate your official scorecard.
						</p>
					</div>

					<form method="POST" action="results.php" class="results-form" id="resultsForm">
						<div class="form-group-wrap">
							<label class="results-field-label" for="selectClass">
								Class <span class="req">*</span>
							</label>
							<div class="results-input-icon-wrap">
								<i class="fa fa-graduation-cap field-icon"></i>
								<select name="class" id="selectClass" class="results-input-control" required>
									<option value="">-- Select Class --</option>
									<?php for ($c = 1; $c <= 10; $c++): ?>
										<option value="<?= $c ?>" <?= (isset($_POST['class']) && $_POST['class'] == $c) ? 'selected' : '' ?>>Class <?= $c ?></option>
									<?php endfor; ?>
								</select>
							</div>
						</div>

						<div class="results-form-grid-2">
							<div class="form-group-wrap">
								<label class="results-field-label" for="sessionDisplayInput">
									Session Year <span class="req">*</span>
								</label>
								<div class="session-input-wrapper" id="sessionPickerContainer" aria-expanded="false" role="combobox" aria-haspopup="true">
									<i class="fa fa-calendar calendar-icon" id="sessionCalendarIcon"></i>
									
									<?php 
										$rawSession = (string) ($_POST['session'] ?? '2025-2026');
										$displaySession = str_replace('-', '–', $rawSession);
									?>

									<!-- Visible Display Input -->
									<input type="text" 
										   id="sessionDisplayInput" 
										   class="session-display-input" 
										   value="<?= e($displaySession) ?>" 
										   placeholder="e.g. 2025–2026" 
										   readonly 
										   required 
										   aria-label="Session Year">

									<!-- Hidden Field Sent to PHP Backend -->
									<input type="hidden" name="session" id="sessionHiddenInput" value="<?= e($rawSession) ?>">

									<!-- Interactive Session Year Popover -->
									<div class="session-year-popover" id="sessionYearPopover" style="display: none;" onclick="event.stopPropagation();">
										<div class="popover-header">
											<span><i class="fa fa-calendar-check-o" style="color: var(--mbvm-blue);"></i> Select Academic Session</span>
											<button type="button" class="popover-close-btn" id="closePopoverBtn" aria-label="Close">&times;</button>
										</div>
										
										<div class="popover-years-grid">
											<?php 
												$currentY = (int) date('Y');
												for ($y = $currentY + 1; $y >= $currentY - 5; $y--):
													$sessBackend = $y . '-' . ($y + 1);
													$sessDisplay = $y . '–' . ($y + 1);
													$isCurrentSelected = ($rawSession === $sessBackend);
											?>
												<button type="button" 
														class="year-chip-btn <?= $isCurrentSelected ? 'active' : '' ?>" 
														data-year="<?= $y ?>" 
														data-backend="<?= $sessBackend ?>" 
														data-display="<?= $sessDisplay ?>">
													<?= $sessDisplay ?>
												</button>
											<?php endfor; ?>
										</div>

										<div class="popover-custom-row">
											<label for="customStartYear">Or enter start year:</label>
											<div style="display: flex; gap: 0.5rem;">
												<input type="number" id="customStartYear" min="2000" max="2099" placeholder="e.g. 2026" class="custom-year-input">
												<button type="button" id="applyCustomYearBtn" class="btn-apply-year">Apply</button>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group-wrap">
								<label class="results-field-label" for="inputRegNo">
									Registration No. <span class="req">*</span>
								</label>
								<div class="results-input-icon-wrap">
									<i class="fa fa-id-card-o field-icon"></i>
									<input type="text" name="regNo" id="inputRegNo" class="results-input-control" placeholder="e.g. REG-101" value="<?= e((string) ($_POST['regNo'] ?? '')) ?>" required>
								</div>
							</div>
						</div>


						<button type="submit" class="btn-results-search" id="submitSearchBtn">
							<span id="btnIcon"><i class="fa fa-search"></i></span>
							<span id="btnText">Search Result</span>
						</button>
					</form>
				</div>
			</div>

			<!-- Quick Stats Section (Visible when no specific result is loaded) -->
			<?php if ($result === null && $message === ''): ?>
				<div class="results-stats-section">
					<div class="results-stats-grid">
						<div class="results-stat-card">
							<div class="stat-icon-wrap icon-blue-bg">
								<i class="fa fa-users"></i>
							</div>
							<div class="stat-card-number">500+</div>
							<div class="stat-card-label">Enrolled Students</div>
						</div>
						<div class="results-stat-card">
							<div class="stat-icon-wrap icon-gold-bg">
								<i class="fa fa-trophy"></i>
							</div>
							<div class="stat-card-number">50+</div>
							<div class="stat-card-label">JNV Selections</div>
						</div>
						<div class="results-stat-card">
							<div class="stat-icon-wrap icon-green-bg">
								<i class="fa fa-shield"></i>
							</div>
							<div class="stat-card-number">100%</div>
							<div class="stat-card-label">Secure Records</div>
						</div>
						<div class="results-stat-card">
							<div class="stat-icon-wrap icon-purple-bg">
								<i class="fa fa-calendar-check-o"></i>
							</div>
							<div class="stat-card-number">2025–2026</div>
							<div class="stat-card-label">Updated Session</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<!-- Error / No Result Found State Card -->
			<?php if ($message !== '' && $result === null): ?>
				<div class="results-empty-card" id="emptyResultCard">
					<div class="empty-state-icon-circle">
						<i class="fa fa-exclamation-triangle"></i>
					</div>
					<h3 class="empty-state-title">No Result Found</h3>
					<p class="empty-state-desc"><?= e($message) ?></p>

					<div class="empty-state-suggestions">
						<div class="suggestions-title"><i class="fa fa-lightbulb-o" style="color: var(--mbvm-blue);"></i> Suggestions to try:</div>
						<ul class="suggestions-list">
							<li>Verify that your Registration Number matches your admission slip (e.g. <strong>REG-101</strong>).</li>
							<li>Ensure you have selected the exact <strong>Class</strong> you studied in during that session.</li>
							<li>Check the <strong>Session Year</strong> format (e.g. <strong>2025-2026</strong>).</li>
							<li>If you continue to experience issues, please contact the school office.</li>
						</ul>
					</div>

					<a href="results.php" class="btn-results-retry">
						<i class="fa fa-refresh"></i> Search Again
					</a>
				</div>
			<?php endif; ?>

			<!-- Student Result Scorecard Display -->
			<?php if ($result !== null): ?>
				<?php 
					$status = (string) ($result['status'] ?? 'Pass');
					$grade = (string) ($result['grade'] ?? 'A');
					$isPass = strcasecmp($status, 'Fail') !== 0;
					$percentage = floatval($result['percentage'] ?? 0);

					$progressClass = 'good';
					if ($percentage >= 75) $progressClass = 'excellent';
					elseif ($percentage < 50) $progressClass = 'needs-work';
				?>
				<div class="result-scorecard-wrap" id="scorecardDisplay">
					<div style="margin-bottom: 1.5rem;">
						<a href="results.php" class="btn-back-search">
							<i class="fa fa-arrow-left"></i> Back to Results Search
						</a>
					</div>

					<div class="scorecard-card">
						<!-- Top Ribbon Header -->
						<div class="scorecard-top-bar">
							<div class="student-profile-summary">
								<div class="student-avatar">
									<i class="fa fa-user"></i>
								</div>
								<div class="student-info-main">
									<h2 class="student-name-title"><?= e((string) $result['student_name']) ?></h2>
									<div class="student-reg-meta">
										<span>REG NO: <strong><?= e((string) $result['registration_no']) ?></strong></span>
										<span>•</span>
										<span>ROLL NO: <strong><?= e((string) ($result['roll_no'] ?: 'N/A')) ?></strong></span>
									</div>
								</div>
							</div>

							<div class="scorecard-status-badge-wrap">
								<div class="<?= $isPass ? 'status-badge-pass' : 'status-badge-fail' ?>">
									<i class="fa <?= $isPass ? 'fa-check-circle' : 'fa-times-circle' ?>"></i> <?= e(strtoupper($status)) ?>
								</div>
								<div class="grade-pill-tag">
									OVERALL GRADE: <?= e($grade) ?>
								</div>
							</div>
						</div>

						<!-- Scorecard Main Body -->
						<div class="scorecard-body">
							<!-- 5 Mini Summary Cards -->
							<div class="summary-cards-grid">
								<div class="summary-card-item">
									<div class="summary-card-lbl">Class</div>
									<div class="summary-card-val">Class <?= e((string) $result['class_name']) ?></div>
								</div>
								<div class="summary-card-item">
									<div class="summary-card-lbl">Session</div>
									<div class="summary-card-val"><?= e((string) $result['session_year']) ?></div>
								</div>
								<div class="summary-card-item">
									<div class="summary-card-lbl">Obtained Marks</div>
									<div class="summary-card-val val-blue"><?= e((string) $result['obtained_marks']) ?> / <?= e((string) $result['total_marks']) ?></div>
								</div>
								<div class="summary-card-item">
									<div class="summary-card-lbl">Percentage</div>
									<div class="summary-card-val val-green"><?= e((string) $result['percentage']) ?>%</div>
								</div>
								<div class="summary-card-item">
									<div class="summary-card-lbl">Grade</div>
									<div class="summary-card-val"><?= e((string) $result['grade']) ?></div>
								</div>
							</div>

							<!-- Performance Circle & Achievement Banner -->
							<div class="performance-row">
								<div class="perf-ring-card">
									<svg viewBox="0 0 36 36" class="circular-chart">
										<path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
										<path class="circle-progress <?= $progressClass ?>" stroke-dasharray="<?= round($percentage) ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
										<text x="18" y="20.35" class="percentage-text"><?= round($percentage) ?>%</text>
									</svg>
									<div class="percentage-subtext">Overall Score</div>
								</div>

								<div class="achievement-banner">
									<div class="achievement-icon">
										<?php if ($percentage >= 75): ?>
											🏆
										<?php elseif ($percentage >= 50): ?>
											🌟
										<?php else: ?>
											📚
										<?php endif; ?>
									</div>
									<div class="achievement-content">
										<h4>
											<?php if ($percentage >= 75): ?>
												Excellent Performance!
											<?php elseif ($percentage >= 50): ?>
												Good Performance!
											<?php else: ?>
												Dedicated Effort Needed
											<?php endif; ?>
										</h4>
										<p>
											<?php if ($percentage >= 75): ?>
												Congratulations! The student has performed exceptionally well with distinction marks across subjects.
											<?php elseif ($percentage >= 50): ?>
												Commendable effort! Continuous practice and regular attendance will yield even higher academic success.
											<?php else: ?>
												Encouragement and focused guidance are recommended to improve academic performance in upcoming tests.
											<?php endif; ?>
										</p>
									</div>
								</div>
							</div>

							<!-- Subject Score Breakdown Table -->
							<div class="scorecard-table-card">
								<div class="scorecard-table-title">
									<i class="fa fa-list-alt" style="color: var(--mbvm-blue);"></i> Scorecard Details Breakdown
								</div>
								<div style="overflow-x: auto;">
									<table class="scorecard-table">
										<thead>
											<tr>
												<th>Student Name</th>
												<th>Reg No.</th>
												<th>Class</th>
												<th>Session</th>
												<th>Max Marks</th>
												<th>Obtained Marks</th>
												<th>Percentage</th>
												<th>Result Status</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><strong><?= e((string) $result['student_name']) ?></strong></td>
												<td><?= e((string) $result['registration_no']) ?></td>
												<td>Class <?= e((string) $result['class_name']) ?></td>
												<td><?= e((string) $result['session_year']) ?></td>
												<td><?= e((string) $result['total_marks']) ?></td>
												<td><strong style="color: var(--mbvm-blue);"><?= e((string) $result['obtained_marks']) ?></strong></td>
												<td><strong style="color: var(--mbvm-green);"><?= e((string) $result['percentage']) ?>%</strong></td>
												<td>
													<span class="<?= $isPass ? 'badge-a' : 'badge-f' ?>">
														<?= e($status) ?>
													</span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>

							<!-- Teacher Remarks Box -->
							<?php if (!empty($result['remarks'])): ?>
								<div class="remarks-box">
									<i class="fa fa-commenting-o remarks-icon"></i>
									<div class="remarks-text">
										<strong>Teacher Remarks:</strong><br>
										<?= nl2br(e((string) $result['remarks'])) ?>
									</div>
								</div>
							<?php endif; ?>

						</div>
					</div>
				</div>
			<?php endif; ?>

		</div>
	</main>

		<?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="frontend/js/libs.js"></script>
	<script>
		document.getElementById('resultsForm')?.addEventListener('submit', function() {
			const btn = document.getElementById('submitSearchBtn');
			if (btn) {
				btn.disabled = true;
				btn.style.opacity = '0.85';
				document.getElementById('btnIcon').innerHTML = '<i class="fa fa-circle-o-notch fa-spin"></i>';
				document.getElementById('btnText').textContent = ' Searching...';
			}
		});

		// Academic Session Picker Logic
		const sessionWrapper = document.getElementById('sessionPickerContainer');
		const displayInput = document.getElementById('sessionDisplayInput');
		const hiddenInput = document.getElementById('sessionHiddenInput');
		const popover = document.getElementById('sessionYearPopover');
		const closeBtn = document.getElementById('closePopoverBtn');

		function togglePicker(e) {
			if (e) e.stopPropagation();
			if (!popover) return;
			const isVisible = popover.style.display === 'block';
			if (isVisible) {
				closePicker();
			} else {
				openPicker();
			}
		}

		function positionPopover() {
			if (!popover || !sessionWrapper) return;
			const rect = sessionWrapper.getBoundingClientRect();
			const popoverHeight = popover.offsetHeight || 300;
			const spaceBelow = window.innerHeight - rect.bottom;
			const spaceAbove = rect.top;

			if (spaceBelow < popoverHeight && spaceAbove > spaceBelow) {
				popover.classList.add('pop-upward');
				popover.classList.remove('pop-downward');
			} else {
				popover.classList.add('pop-downward');
				popover.classList.remove('pop-upward');
			}
		}

		function openPicker() {
			if (popover) {
				popover.style.display = 'block';
				sessionWrapper?.classList.add('is-open');
				sessionWrapper?.setAttribute('aria-expanded', 'true');
				positionPopover();
			}
		}

		function closePicker() {
			if (popover) {
				popover.style.display = 'none';
				sessionWrapper?.classList.remove('is-open');
				sessionWrapper?.setAttribute('aria-expanded', 'false');
			}
		}

		if (sessionWrapper) {
			sessionWrapper.addEventListener('click', togglePicker);
		}

		if (closeBtn) {
			closeBtn.addEventListener('click', function(e) {
				e.stopPropagation();
				closePicker();
			});
		}

		document.addEventListener('click', function(e) {
			if (popover && popover.style.display === 'block' && sessionWrapper && !sessionWrapper.contains(e.target)) {
				closePicker();
			}
		});

		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && popover && popover.style.display === 'block') {
				closePicker();
			}
		});

		window.addEventListener('resize', function() {
			if (popover && popover.style.display === 'block') {
				positionPopover();
			}
		});


		// Convert Start Year to Academic Session Formats
		function setAcademicSession(startYear) {
			const y = parseInt(startYear, 10);
			if (isNaN(y)) return;
			const nextY = y + 1;
			const backendVal = y + '-' + nextY;
			const displayVal = y + '–' + nextY;

			if (displayInput) displayInput.value = displayVal;
			if (hiddenInput) hiddenInput.value = backendVal;

			// Highlight active button
			document.querySelectorAll('.year-chip-btn').forEach(btn => {
				if (btn.getAttribute('data-year') == y) {
					btn.classList.add('active');
				} else {
					btn.classList.remove('active');
				}
			});
		}

		// Year Chip Click Handlers
		document.querySelectorAll('.year-chip-btn').forEach(btn => {
			btn.addEventListener('click', function(e) {
				e.stopPropagation();
				const year = this.getAttribute('data-year');
				setAcademicSession(year);
				closePicker();
			});
		});

		// Custom Year Input Handler
		const applyBtn = document.getElementById('applyCustomYearBtn');
		const customYearInput = document.getElementById('customStartYear');
		if (applyBtn && customYearInput) {
			applyBtn.addEventListener('click', function(e) {
				e.stopPropagation();
				const val = customYearInput.value.trim();
				if (val && val.length === 4 && !isNaN(val)) {
					setAcademicSession(val);
					closePicker();
				} else {
					alert('Please enter a valid 4-digit start year (e.g. 2025)');
				}
			});
		}
	</script>

</body>

</html>
