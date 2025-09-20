<?php
// Dropbox file URL from query
$url = $_GET['file'] ?? '';
$url = filter_var($url, FILTER_SANITIZE_URL);

if (!$url || !preg_match('#^https?://www.dropbox.com/#', $url)) {
    http_response_code(400);
    echo "Invalid file URL";
    exit;
}

// Map extension to MIME
$ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
$mimeTypes = [
    'apk' => 'application/vnd.android.package-archive',
    'mobileconfig' => 'application/x-apple-aspen-config'
];
$contentType = $mimeTypes[$ext] ?? 'application/octet-stream';

// Suggested filename
$filename = 'MyPlisbet.' . $ext;

// Headers to force download
header('Content-Description: File Transfer');
header('Content-Type: ' . $contentType);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Stream file from Dropbox
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
$data = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo 'Download error: ' . curl_error($ch);
    exit;
}

curl_close($ch);
echo $data;
exit;
