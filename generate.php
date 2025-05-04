<?php
header('Content-Type: application/json');

define('SITE_URL', 'https://chatdatlng.biz.id/t');
define('TOKEN_LENGTH', 12);

class LinkGenerator {
    private $smartlink;
    private $campaign;
    private $features;
    private $ogData;

    private $defaultTitles = [
        "Don't Miss This Offer!",
        "Limited Time Deal!",
        "Exclusive Promotion!",
        "Best Offer Today!",
        "Special Discount Inside!",
        "New Exciting Deal Just For You!",
        "Hurry! Offer Ends Soon!"
    ];

    private $defaultDescriptions = [
        "Grab this amazing deal before it's gone.",
        "Limited availability, act fast!",
        "Exclusive offer just for you.",
        "Save big with this special promotion.",
        "Don't wait, get it now!",
        "Unlock your special discount today.",
        "Join thousands who are saving big!"
    ];

    public function __construct($data) {
        $this->smartlink = 'https://fbhhhg.naughtymets.com/s/5f54849de4bb0'; // Fixed Trafee Smartlink URL
        $this->campaign = 'Permanent Campaign Name'; // Set permanent campaign name here
        $this->features = $data['features'] ?? [];

        $title = trim($data['og_title']);
        $description = trim($data['og_description']);

        if (empty($title)) {
            $title = $this->defaultTitles[array_rand($this->defaultTitles)] . ' ' . $this->generateRandomString(6);
        } else {
            $title .= ' ' . $this->generateRandomString(6);
        }

        if (empty($description)) {
            $description = $this->defaultDescriptions[array_rand($this->defaultDescriptions)] . ' ' . $this->generateRandomString(6);
        } else {
            $description .= ' ' . $this->generateRandomString(6);
        }

        $this->ogData = [
            'title' => $title,
            'description' => $description,
            'image' => filter_var($data['og_image'], FILTER_VALIDATE_URL)
        ];
    }

    public function generate() {
        if (!$this->validateInputs()) {
            return $this->error('Please fill all required fields with valid data');
        }

        try {
            $token = $this->generateToken();

            $proxyImageUrl = '';
            if (!empty($this->ogData['image'])) {
                $imageFileName = basename(parse_url($this->ogData['image'], PHP_URL_PATH));
                $randomQuery = '?v=' . $this->generateRandomString(6);
                $proxyImageUrl = SITE_URL . '/image_proxy.php?img=' . urlencode($imageFileName) . $randomQuery;

                $imageShortCode = $this->generateImageShortCode();
                $this->storeImageShortlink($imageShortCode, $proxyImageUrl);

                $proxyImageUrl = SITE_URL . '/i/' . $imageShortCode . $randomQuery;
            }
            $this->ogData['image'] = $proxyImageUrl;

            $this->storeLink($token);

            $protectedUrl = $this->createProtectedUrl($token);

            $shortCode = $this->generateShortCode();
            $this->storeShortlink($shortCode, $protectedUrl);

            $fbclid = 'IwAR' . bin2hex(random_bytes(12));
            $shortlinkUrl = SITE_URL . '/x/' . $shortCode . '?fbclid=' . $fbclid;

            return [
                'success' => true,
                'url' => $shortlinkUrl,
                'preview' => [
                    'title' => $this->ogData['title'],
                    'description' => $this->ogData['description'],
                    'image' => $this->ogData['image']
                ],
                'features' => [
                    'mobile' => in_array('mobile', $this->features),
                    'facebook' => in_array('fb', $this->features),
                    'fingerprint' => in_array('fingerprint', $this->features)
                ]
            ];
        } catch (Exception $e) {
            return $this->error('Error generating link: ' . $e->getMessage());
        }
    }

    private function generateShortCode() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < 7; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }

    private function generateImageShortCode() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < 7; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }

    private function generateRandomString($length) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $str;
    }

    private function storeShortlink($code, $url) {
        $shortlinks = [];
        $file = __DIR__ . '/shortlinks.json';
        if (file_exists($file)) {
            $shortlinks = json_decode(file_get_contents($file), true) ?? [];
        }
        $shortlinks[$code] = $url;
        file_put_contents($file, json_encode($shortlinks, JSON_PRETTY_PRINT));
    }

    private function storeImageShortlink($code, $url) {
        $file = __DIR__ . '/image_shortlinks.json';
        $shortlinks = [];
        if (file_exists($file)) {
            $shortlinks = json_decode(file_get_contents($file), true) ?? [];
        }
        $shortlinks[$code] = $url;
        file_put_contents($file, json_encode($shortlinks, JSON_PRETTY_PRINT));
    }

    private function generateToken() {
        return bin2hex(random_bytes(TOKEN_LENGTH));
    }

    private function storeLink($token) {
        $data = [
            'token' => $token,
            'smartlink' => $this->smartlink,
            'campaign' => $this->campaign,
            'features' => $this->features,
            'og' => $this->ogData,
            'created' => time()
        ];

        $links = [];
        if (file_exists('links.json')) {
            $links = json_decode(file_get_contents('links.json'), true) ?? [];
        }

        $links[] = $data;

        if (!file_put_contents('links.json', json_encode($links, JSON_PRETTY_PRINT))) {
            throw new Exception('Could not store link data');
        }
    }

    private $redirectScripts = ['redirect.php', 'urlredire.php', 'redir.php'];

    private function createProtectedUrl($token) {
        $script = $this->redirectScripts[array_rand($this->redirectScripts)];
        return SITE_URL . '/' . $script . '?token=' . urlencode($token);
    }

    private function error($message) {
        return [
            'success' => false,
            'error' => $message
        ];
    }
}

try {
    $generator = new LinkGenerator($_POST);
    echo json_encode($generator->generate());
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'System error: ' . $e->getMessage()
    ]);
}
?>
