<?php session_start();
    //header("Content-Type", "application/json");
    include_once("func/bc-connect.php");
    include_once("func/bc-func.php");

    //Select Vendor Table
	$select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='".$_SERVER["HTTP_HOST"]."' LIMIT 1"));
	if(($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)){
        $get_api_post_info = json_decode(file_get_contents('php://input'),true);
        $post_username = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["user"])));
        if(isset($get_api_post_info["request_sender"])){
        	$post_request_sender = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["request_sender"])));	
        }else{
        	$post_request_sender = "";
        }
        $get_vendor_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='".$_SERVER["HTTP_HOST"]."' LIMIT 1"));
        $get_user_detail_via_username = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_vendor_details["id"]."' && username='".$post_username."' LIMIT 1");
        $get_logged_user_details = mysqli_fetch_array($get_user_detail_via_username);
        if(mysqli_num_rows($get_user_detail_via_username) == 1){
            if(($_SESSION["user_session"] !== strtolower($get_logged_user_details["username"])) || ($post_request_sender == "admin")){
                //User exists
                $username = strtoupper($get_logged_user_details["firstname"]." ".$get_logged_user_details["lastname"]).checkIfEmpty(ucwords($get_logged_user_details["othername"]),", ", "");
                $json_response_array = array("status" => "200", "text" => $username);
                echo json_encode($json_response_array,true);
            }else{
                //Cannot share fund to same account
                $json_response_array = array("status" => "201", "text" => "Cannot Share Fund To Same Account");
                echo json_encode($json_response_array,true);
            }
        }else{
        	//User not exists
        	$json_response_array = array("status" => "201", "text" => "User Not Exists");
        	echo json_encode($json_response_array,true);
        }
	}else{
		//Website not registered
		$json_response_array = array("status" => "201", "desc" => "Website Not Registered");
		echo json_encode($json_response_array,true);
	}
?>