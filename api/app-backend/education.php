<?php error_reporting(0);
// include_once($_SERVER["DOCUMENT_ROOT"] . "/config.php");
header("Content-Type: application/json");
$incoming_post_request = file_get_contents("php://input");
fwrite(fopen("education.txt", "a"), $incoming_post_request . "\n\n");

    $app_json = json_encode(array("json-status" => "success", "status" => "failed", "status-msg" => "Bad Request"), true);


echo $app_json;
?>