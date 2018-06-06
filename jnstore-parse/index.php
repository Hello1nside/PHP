<?php
/**
 * @author     Mudrahel.com
 * @category   Application\Parser
 * @copyright  2018
 */

set_time_limit(900);
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once('db.php');

class Parser
{
    public function curl_get_contents($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36');
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public function readfile($file) {
        $file = file_get_contents('jnstore.it.txt');
        $urls = explode(PHP_EOL, $file);
        return $urls;
    }

    public function parseTitle($page) {
        $patternTitle = "/<h1 class=\"product_title h1\" itemprop=\"name\">(.+?)<\/h1>/";
        preg_match_all($patternTitle, $page,$title);
        return $title[1][0];
    }

    public function parseDescription($page) {
        $patternDescription = '/<div class=\"tab-pane fade in active\" id=\"description\" role=\"tabpanel\">(.*?)<\/div>/s';
        preg_match_all($patternDescription, $page, $description);
        return $description[1][0].'</div>';
    }

    public function parseImage($page) {
        $patternImage = "/<meta property=\"og:image\" content=\"(.*)\">/";
        preg_match_all($patternImage, $page,$image);
        return $image[1][0];
    }
}

$Parser = new Parser;
$urls = $Parser->readfile('jnstore.it.txt');

$i = 1;
foreach ($urls as $url) {
    $page = $Parser->curl_get_contents($url);
    //echo $i.' = '.$url.'<br>';

    $title = $Parser->parseTitle($page);
    //echo $title;

    $description = $Parser->parseDescription($page);
    $description = strip_tags(addslashes(trim($description)));
    //echo $description;

    $image = $Parser->parseImage($page);
    //echo $image;

    if(!empty($title) && !empty($description) && !empty($image)) {
        // INSERT DATA
        $sql = "INSERT INTO data1 (`url`, `title`, `description`, `image`) VALUES ('{$url}','{$title}','{$description}','{$image}')";
        DB::Execute($sql);
    }

    echo $i.' - success';
    echo '<hr>';
    $i++;

    // for testing only 10 page (not all document)
    if($i == 10) {
        exit();
    }

}