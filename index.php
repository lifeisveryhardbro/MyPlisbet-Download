<?php
// Detect Android device from User-Agent
function isAndroidDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return stripos($userAgent, 'android') !== false;
}
$isAndroid = isAndroidDevice();

// Fetch app links from external API
$apiUrl = 'https://panel.mytechdev.co/api/apps/17';
$context = stream_context_create([
    "http" => [
        "method" => "GET",
        "timeout" => 5,
        "header" => "Accept: application/json\r\n"
    ]
]);

$response = @file_get_contents($apiUrl, false, $context);
$androidLink = null;
$iosLink = null;

if ($response !== false) {
    $data = json_decode($response, true);
    if ($data && !empty($data['success']) && isset($data['data'])) {
        $androidLink = $data['data']['android_app'] ?? null; // Dropbox APK link
        $iosLink     = $data['data']['iphone_app'] ?? null;  // Dropbox mobileconfig link
    }
}

// Helper function to generate a download proxy URL
function downloadUrl($file) {
    return "dropbox-download.php?file=" . urlencode($file);
}

// Dynamic current URL for og:url
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$currentUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Aplikasi all in one, lengkap, praktis, bebas virus, gratis, dan ringan!">
    <title>Download Aplikasi Resmi MyPlisbet by Plisbet</title>
    <link rel="shortcut icon" href="./assets/download.svg" type="image/svg">
    <link rel="stylesheet" href="style.css?v=1.13">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="Download Aplikasi Resmi MyPlisbet by Plisbet">
    <meta property="og:description" content="Aplikasi all in one, lengkap, praktis, bebas virus, gratis, dan ringan!">
    <meta property="og:image" content="<?= $protocol ?>://<?= $_SERVER['HTTP_HOST'] ?>/assets/myplisbet.webp">
    <meta property="og:url" content="<?= htmlspecialchars($currentUrl, ENT_QUOTES) ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Download Aplikasi Resmi MyPlisbet by Plisbet">
    <meta name="twitter:description" content="Aplikasi all in one, lengkap, praktis, bebas virus, gratis, dan ringan!">
    <meta name="twitter:image" content="<?= $protocol ?>://<?= $_SERVER['HTTP_HOST'] ?>/assets/myplisbet.webp">
</head>
<body>
    <main class="container app-download-section">
        <img src="./assets/logo.webp.webp" alt="Company Logo" class="app-logo" width="350" height="100">
        <h1 class="section-title">Download Our Mobile App</h1>
        <p class="section-description">
            Enjoy seamless access to our services right from your phone. Download now and stay connected!
        </p>

        <div class="download-buttons">
            <?php if ($androidLink): ?>
            <a href="<?= downloadUrl($androidLink) ?>" class="download-button android-button" rel="nofollow noopener">
                <img src="./assets/android.webp" alt="Android Logo" class="platform-icon" width="60" height="60">
                <p>Download for <span class="platform-name">Android</span></p>
                <img src="./assets/download.svg" alt="Download Icon" class="download-icon" width="54" height="54">
            </a>
            <?php endif; ?>

            <?php if ($iosLink): ?>
            <a href="<?= downloadUrl($iosLink) ?>" class="download-button ios-button" rel="nofollow noopener">
                <img src="./assets/iphone.webp" alt="iPhone Logo" class="platform-icon" width="60" height="60">
                <p>Download for <span class="platform-name">iPhone</span></p>
                <img src="./assets/download.svg" alt="Download Icon" class="download-icon" width="54" height="54">
            </a>
            <?php endif; ?>
        </div>

        <?php if ($isAndroid): ?>
        <p class="footer-text">
            Already Have Our App? 
            <a href="#" class="open-app-link" id="openAppLink">
                Open Now 
                <img src="./assets/arrow.svg" alt="Arrow Icon" class="arrow-icon" width="28" height="28">
            </a> 
        </p>
        <?php endif; ?>
    </main>

    <?php if ($isAndroid): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openLink = document.getElementById('openAppLink');
            const footerText = document.querySelector('.footer-text');

            openLink.addEventListener('click', function (e) {
                e.preventDefault();
                const start = Date.now();
                window.location = "myPlisbet://"; 

                setTimeout(() => {
                    const elapsed = Date.now() - start;
                    if (elapsed < 1500) {
                        if (footerText) footerText.style.display = 'none';

                        const toast = document.createElement('div');
                        toast.innerText = "App not installed.";
                        toast.style.cssText = `
                            position: fixed;
                            bottom: 20px;
                            left: 50%;
                            transform: translateX(-50%);
                            background-color: #333;
                            color: #fff;
                            padding: 12px 20px;
                            border-radius: 6px;
                            font-family: 'Poppins', sans-serif;
                            font-size: 14px;
                            box-shadow: 0 0 10px rgba(0,0,0,0.2);
                            z-index: 9999;
                        `;
                        document.body.appendChild(toast);

                        setTimeout(() => {
                            toast.remove();
                        }, 3000);
                    }
                }, 1200);
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
