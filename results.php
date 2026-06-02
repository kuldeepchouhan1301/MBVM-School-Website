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
                $message = 'No result found for these details.';
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
    <title>MBVM | Result</title>
    <meta charset="utf-8">
    <meta name="description" content="School Website.">
    <meta name="keywords" content="school,college,management,result,exam,attendace,hostel,admission,events">
    <meta name="author" content="H.R.Shadhin">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">

    <link rel="shortcut icon" href="/frontend/img/favicon.png">
    <link rel="stylesheet" href="/frontend/css/libs.css">
    <link rel="stylesheet" href="/frontend/css/modern.css">
    <style>
        .result-card {
            margin-top: 30px;
            background: #fff;
            border: 1px solid #e8edf2;
            padding: 24px;
        }

        .result-card table {
            width: 100%;
            border-collapse: collapse;
        }

        .result-card th,
        .result-card td {
            padding: 12px;
            border-bottom: 1px solid #edf0f5;
            text-align: left;
        }

        .result-message {
            margin-top: 22px;
            padding: 13px 15px;
            background: #fff7e6;
            border: 1px solid #ffd591;
            color: #874d00;
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

    <div class="page-content" style="padding: 0px;">
        <main>
            <section class="fullwidth-background bg-2">
                <div class="grid-row">
                    <div class="login-block">
                        <h5 class="text-info">Fill up all information fields</h5>

                        <form class="login-form" method="POST" action="results.php">
                            <div class="form-group">
                                <select name="class" class="select2" required>
                                    <option value="">--Select Class--</option>
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
                                <span class="input-icon"><i class="fa fa-info"></i></span>
                            </div>
                            <div class="form-group">
                                <input type="text" name="session" class="login-input" required placeholder="Session">
                                <span class="input-icon"><i class="fa fa-calendar"></i></span>
                            </div>
                            <div class="form-group">
                                <input type="text" name="regNo" class="login-input" required placeholder="Registration No.">
                                <span class="input-icon"><i class="fa fa-info"></i></span>
                            </div>

                            <button type="submit" class="button-fullwidth cws-button bt-color-3 border-radius">SUBMIT</button>
                        </form>

                        <?php if ($message !== ''): ?>
                            <div class="result-message"><?= e($message) ?></div>
                        <?php endif; ?>

                        <?php if ($result !== null): ?>
                            <div class="result-card corner-radius">
                                <h2>Student Result</h2>
                                <table>
                                    <tr><th>Student Name</th><td><?= e((string) $result['student_name']) ?></td></tr>
                                    <tr><th>Registration No.</th><td><?= e((string) $result['registration_no']) ?></td></tr>
                                    <tr><th>Class</th><td><?= e((string) $result['class_name']) ?></td></tr>
                                    <tr><th>Session</th><td><?= e((string) $result['session_year']) ?></td></tr>
                                    <tr><th>Roll No.</th><td><?= e((string) $result['roll_no']) ?></td></tr>
                                    <tr><th>Marks</th><td><?= e((string) $result['obtained_marks']) ?> / <?= e((string) $result['total_marks']) ?></td></tr>
                                    <tr><th>Percentage</th><td><?= e((string) $result['percentage']) ?>%</td></tr>
                                    <tr><th>Grade</th><td><?= e((string) $result['grade']) ?></td></tr>
                                    <tr><th>Status</th><td><?= e((string) $result['status']) ?></td></tr>
                                    <tr><th>Remarks</th><td><?= nl2br(e((string) $result['remarks'])) ?></td></tr>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <footer>
        <div class="grid-row">
            <div class="grid-col-row clear-fix">
                <section class="grid-col grid-col-6 footer-about">
                    <h2 class="corner-radius">About Us</h2>
                    <div>
                        <p>MADHUSUDAN BAL VIDYA MANDIR MUNGTHALA was established in 2004 and it is managed by the Pvt. Unaided. It is located at Rural area of ABU-ROAD block of SIROHI district of Rajasthan.</p>
                    </div>
                    <address>
                        <a href="tel:+919982754110" class="phone-number">+91 9982754110</a><br>
                        <a href="mailto:mbvm.mungthala1@gmail.com" class="email">mbvm.mungthala1@gmail.com</a><br>
                        <a href="contact-us.html" class="address">Mungthala Bus Stand<br>Abu-Road Rajasthan</a>
                    </address>
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
        <div class="footer-bottom">
            <div class="grid-row clear-fix">
                <div class="copyright">Madhusudan Bal Vidya Mandir <span></span> 2026 All Rights Reserved</div>
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
