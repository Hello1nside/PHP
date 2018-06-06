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

$rs = DB::RS("SELECT * FROM data");

while ($row = $rs->fetch_assoc()) {

    $description = strip_tags($row["description"]);
    $description = str_replace('"', '\"', $description);
    //$description = str_replace("\\", "\\\\", $description);

    $title = strip_tags($row["title"]);
    $title = str_replace('"', '\"', $title);

    //$description = str_replace(PHP_EOL, '', $description);
    //$description = trim(preg_replace('/\s+/g', '', $description));
    //$description = str_replace("\t","     ",$description);

    echo "{\"index\": {\"_index\": \"jnstore_it\", \"_type\": \"products\", \"_id\": \"" . $row["id"] . "\"}}<br> {\"url\":\""
        . $row["url"] . "\", \"title\": \"" . $title . "\", \"description\": \"" .
        $description . "\", \"image\": \"" . $row["image"] . "\"}<br>";
}