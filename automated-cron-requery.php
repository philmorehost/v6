<?php session_start();
//header("Content-Type", "application/json");
include_once("func/bc-connect.php");
include_once("func/bc-func.php");

//Select Vendor Table
$select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));
if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
    $get_api_post_info = json_decode(file_get_contents('php://input'), true);

    $get_vendor_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1"));

    $select_user_requeried_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='" . $get_vendor_details["id"] . "' && status='2' LIMIT 10");

    if (mysqli_num_rows($select_user_requeried_transaction_details) > 0) {
        while ($requeried_transaction = mysqli_fetch_assoc($select_user_requeried_transaction_details)) {
            $_SESSION["user_session"] = $requeried_transaction["username"];
            $get_user_detail_via_username = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_details["id"] . "' && username='" . $_SESSION["user_session"] . "' LIMIT 1");
            $get_logged_user_details = mysqli_fetch_array($get_user_detail_via_username);

            if ((mysqli_num_rows($get_user_detail_via_username) == 1) && ($get_logged_user_details["status"] == "1")) {
                $purchase_method = "cron_job";
                $action_function = 2;
                $cron_job_requery_reference = $requeried_transaction["reference"];
                include($_SERVER['DOCUMENT_ROOT'] . "/web/func/requery-transaction.php");
                $json_response_decode = json_decode($json_response_encode, true);
                //echo json_encode($json_response_decode, true)."<br/>";
            }
        }
    }

} else {
    //Website not registered
    $json_response_array = array("status" => "201", "text" => "Website Not Registered");
    echo json_encode($json_response_array, true);
}

mysqli_close($connection_server);
?>