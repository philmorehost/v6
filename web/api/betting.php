<?php session_start();
//header("Content-Type", "application/json");
include_once("../../func/bc-connect.php");

//Select Vendor Table
$select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
    if (isset($api_post_info_from_app) && !is_array($api_post_info_from_app)) {
        $purchase_method = "app";
        $get_api_post_info = $api_post_info_from_app;
    } else {
        $purchase_method = "api";
        $get_api_post_info = json_decode(file_get_contents('php://input'), true);
    }
    $get_vendor_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
    $api_key_sanitized = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["api_key"])));
    $get_user_detail_via_apikey = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_details["id"] . "' && api_key='" . $api_key_sanitized . "' LIMIT 1");
    $get_logged_user_details = mysqli_fetch_array($get_user_detail_via_apikey);
    if (mysqli_num_rows($get_user_detail_via_apikey) == 1) {
        if ($get_logged_user_details["api_status"] == 1) {
            $action_function = 1;
            $_SESSION["user_session"] = $get_logged_user_details["username"];
            include_once("../../func/bc-func.php");
            include_once("../func/betting.php");
            $api_json_response_encode = $json_response_encode;
            alterUser($get_logged_user_details["username"], "last_login", date('Y-m-d H:i:s.u'));
            unset($_SESSION["user_session"]);
        }

        if ($get_logged_user_details["api_status"] == 2) {
            //API approval needed, Contact Admin
            $json_response_array = array("status" => "failed", "desc" => "API approval needed, Contact Admin");
            $api_json_response_encode = json_encode($json_response_array, true);
        }
    } else {
        //User not exists
        $json_response_array = array("status" => "failed", "desc" => "User not exists");
        $api_json_response_encode = json_encode($json_response_array, true);
    }
} else {
    //Website not registered
    $json_response_array = array("status" => "failed", "desc" => "Website not registered");
    $api_json_response_encode = json_encode($json_response_array, true);
}

if (!isset($api_post_info_from_app) || !is_array($api_post_info_from_app)) {
    echo $api_json_response_encode;
}

mysqli_close($connection_server);
?>