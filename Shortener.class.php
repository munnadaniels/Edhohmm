<?php
class Shortener
{
    private $apiUrls = [
        "https://gyanilinks.com/api",
        "https://atglinks.com/api",
    ];

    private $apiTokens = [
        "73d210766797b994503513ea3b308e5bcc0c140f",
        "d87e4f63f5c7b094c12463b228b6c0f4dbfaf7bd",
    ];

    private $pdo;
    private $timestamp;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->timestamp = date("Y-m-d H:i:s");
    }

    // ... (rest of the Shortener class remains the same)

    public function urlToShortCode($url)
    {
        if (empty($url)) {
            throw new Exception("No URL was supplied.");
        }
        if ($this->validateUrlFormat($url) == false) {
            throw new Exception("URL does not have a valid format.");
        }
        if (self::$checkUrlExists) {
            if (!$this->verifyUrlExists($url)) {
                throw new Exception("URL does not appear to exist.");
            }
        }

        // Randomly select an API
        $randomApiIndex = array_rand($this->apiUrls);
        $shortenedUrl = $this->shortenUrlWithApi($url, $this->apiUrls[$randomApiIndex], $this->apiTokens[$randomApiIndex]);

        return $shortenedUrl;
    }

    // ... (rest of the Shortener class remains the same)

    private function shortenUrlWithApi($longUrl, $apiUrl, $apiToken)
    {
        $longUrl = urlencode($longUrl);
        $api_url = "{$apiUrl}?api={$apiToken}&url={$longUrl}&alias=CustomAlias";
        $result = @json_decode(file_get_contents($api_url), TRUE);

        if ($result["status"] === 'error') {
            throw new Exception($result["message"]);
        } else {
            return $result["shortenedUrl"];
        }
    }
}

// Usage Example
require_once 'dbConfig.php';
require_once 'Shortener.class.php';

// Initialize Shortener class and pass PDO object
$shortener = new Shortener($db);

// Long URL
$longURL = 'https://www.codexworld.com/tutorials/php/';

try {
    // Get short code of the URL
    $shortURL = $shortener->urlToShortCode($longURL);

    // Display short URL
    echo 'Short URL: ' . $shortURL;
} catch (Exception $e) {
    // Display error
    echo $e->getMessage();
}

?>
