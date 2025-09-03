<?php
    if(!empty($requery_reference)){
        $curl_url = "https://".$api_detail["api_base_url"]."/web/api/requery.php";
        $curl_request = curl_init($curl_url);
        curl_setopt($curl_request, CURLOPT_POST, true);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
        $curl_http_headers = array(
        	"Content-Type: application/json",
        );
        curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
        $curl_postfields_data = json_encode(array("api_key"=> $api_detail["api_key"],"reference"=> getTransaction($requery_reference, "api_reference")), true);
        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
        $curl_result = curl_exec($curl_request);
        $curl_json_result = json_decode($curl_result, true);
        
        
        if(in_array($curl_json_result["status"],array("success"))){
        	$api_response = "successful";
        	$api_response_reference = $curl_json_result["ref"];
        	$api_response_text = $curl_json_result["status"];
        	$api_response_description = $curl_json_result["response_desc"];
        	$api_response_status = 1;
        }
        
        if(in_array($curl_json_result["status"],array("pending"))){
        	$api_response = "pending";
        	$api_response_reference = $curl_json_result["ref"];
        	$api_response_text = $curl_json_result["status"];
        	$api_response_description = $curl_json_result["response_desc"];
        	$api_response_status = 2;
        }
        
        if(in_array($curl_json_result["status"],array("failed"))){
        	$api_response = "failed";
        	$api_response_text = $curl_json_result["status"];
        	$api_response_description = "Transaction Failed";
        	$api_response_status = 3;
        }
    }else{
        //Electric ref empty
        $api_response = "failed";
        $api_response_text = "";
        $api_response_description = "";
        $api_response_status = 3;
    }
curl_close($curl_request);
?>