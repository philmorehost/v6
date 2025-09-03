<?php error_reporting(0);
include_once("app-config.php");

header("Content-Type: application/json");
$incoming_post_request = '{"username":"habeeb","encoded-passkey":"e3483779e5efd0e4005f320d96b99c94","network":"airtel","phone":"08124232128","amount":"100"}';
fwrite(fopen("airtime.txt", "a"), $incoming_post_request . "\n\n");
$decode_post_request = json_decode($incoming_post_request, true);
if (json_last_error() === JSON_ERROR_NONE) {

    //Select Vendor Table
    $select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
    if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
        $username = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["username"])));
        $password = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["encoded-passkey"])));
        $network = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["network"])));
        $phone = str_replace(" ", "", mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["phone"]))));
        $amount = mysqli_real_escape_string($connection_server, trim(strip_tags($decode_post_request["amount"])));
        $amount = str_replace(["-", "+", "*", "/"], "", $amount);

        $status_update = "failed";
        $status_msg = "Unknown Error";

        if (
            !empty($username) &&
            !empty($password) &&
            !empty($network) &&
            !empty($phone) &&
            is_numeric($phone) &&
            (strlen($phone) == 11) &&
            !empty($amount) &&
            is_numeric($amount) &&
            ($amount >= 100)
        ) {
            if (in_array(substr($phone, 1, 3), $mtn_carrier_id_array)) {
                $network = "mtn";
            } elseif (in_array(substr($phone, 1, 3), $airtel_carrier_id_array)) {
                $network = "airtel";
            } elseif (in_array(substr($phone, 1, 3), $glo_carrier_id_array)) {
                $network = "glo";
            } elseif (in_array(substr($phone, 1, 3), $etisalat_carrier_id_array)) {
                $network = "9mobile";
            }

            $checkuser = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND  (username = '" . $username . "' OR email='" . $username . "')");
            if (mysqli_num_rows($checkuser) == 1) {
                $checkuser_pass = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id = '" . $select_vendor_table["id"] . "' AND (username = '" . $username . "' OR email='" . $username . "') AND password = '" . $password . "'");
                if (mysqli_num_rows($checkuser_pass) == 1) {
                    $get_user_info = mysqli_fetch_array($checkuser_pass);
                    $api_file_url = $_SERVER["DOCUMENT_ROOT"] . "/web/api/airtime.php";
                    if (file_exists($api_file_url)) {
                        $api_post_info_from_app = array("api_key" => $get_user_info["api_key"], "network" => $network, "phone_number" => $phone, "amount" => $amount);
                        include_once($api_file_url);
                        if (in_array($json_response_array["status"], array("success", "pending"))) {
                            $status_update = "success";
                            $status_msg = $json_response_array["desc"];
                        } else {
                            $status_msg = $json_response_array["desc"];
                        }
                    } else {
                        $status_msg = "Server Unavailable";
                    }
                } else {
                    $status_msg = "Invalid Password";
                }
            } else {
                $status_msg = "Invalid Username or email";
            }
        } else {
            if (empty($username)) {
                $status_msg = "Empty Username";
            } elseif (empty($password)) {
                $status_msg = "Empty Password";
            } elseif (empty($network)) {
                $status_msg = "Fullname is required";
            } elseif (empty($phone)) {
                $status_msg = "Phone number is required";
            } elseif (!is_numeric($phone)) {
                $status_msg = "Phone number must be a number";
            } elseif ((strlen($phone) < 11) || (strlen($phone) > 11)) {
                $status_msg = "Phone number must be 11 digits long";
            } elseif (empty($amount)) {
                $status_msg = "Amount is required";
            } elseif (!is_numeric($amount)) {
                $status_msg = "Amount must be a number";
            } elseif ($amount < 100) {
                $status_msg = "Amount must be 100 NGN and above";
            }
        }
        $app_json = json_encode(array("json-status" => "success", "status" => $status_update, "status-msg" => $status_msg), true);
    } else {
        //Website not registered
        $app_json = json_encode(array("json-status" => "failed", "status" => "failed", "status-msg" => "Website not registered"), true);
    }
} else {
    $app_json = json_encode(array("json-status" => "success", "status" => "failed", "status-msg" => "Bad Request"), true);
}


echo $app_json;
?>