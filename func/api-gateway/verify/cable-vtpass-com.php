<?php
    $data_service_provider_alter_code = array("startimes" => "startimes", "dstv" => "dstv", "gotv" => "gotv");
    if(in_array($product_name, array_keys($data_service_provider_alter_code))){
    	if($product_name == "startimes"){
    		$web_cable_size_array = array("nova_weekly" => "nova_weekly", "basic_weekly" => "basic-weekly", "smart_weekly" => "smart-weekly", "classic_weekly" => "classic-weekly", "super_weekly" => "super-weekly", "nova" => "nova", "basic" => "basic", "smart" => "smart", "classic" => "classic", "super" => "super", "chinese_dish" => "uni-1", "nova_antenna" => "uni-2", "special_weekly" => "special-weekly","special_monthly" => "special-monthly","nova_dish_weekly" => "nova-dish-weekly","super_antenna_weekly" => "super-antenna-weekly","super_antenna_monthly" => "super-antenna-monthly","combo_smart_basic_weekly" => "combo-smart-basic-weekly","combo_special_basic_weekly" => "combo-special-basic-weekly","combo_super_classic_weekly" => "combo-super-classic--weekly","combo_smart_basic_monthly" => "combo-smart-basic-monthly","combo_special_basic_monthly" => "combo-special-basic-monthly","combo_super_classic_monthly" => "combo-super-classic--monthly");
    		$vtpass_isp_code = "startimes";
    	}else{
    		if($product_name == "dstv"){
    			$web_cable_size_array = array("padi" => "padi", "yanga" => "yanga", "confam" => "confam", "compact" => "compact", "compact_plus" => "compact_plus");
    			$vtpass_isp_code = "dstv";
    		}else{
    			if($product_name == "gotv"){
    				$web_cable_size_array = array("smallie" => "smallie", "jinja" => "jinja", "jolli" => "jolli", "max" => "max", "super" => "super");
    				$vtpass_isp_code = "gotv";
    			}
    		}
    	}
    	
    	if(in_array($quantity, array_keys($web_cable_size_array))){
    		$curl_url = "https://vtpass.com/api/merchant-verify";
        	$curl_request = curl_init($curl_url);
        	curl_setopt($curl_request, CURLOPT_POST, true);
        	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
        	curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
        	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
        	$curl_http_headers = array(
        		"Authorization: Basic ".base64_encode($api_detail["api_key"]),
        		"Content-Type: application/json",
        	);
        	curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
        	$curl_postfields_data = json_encode(array("billersCode"=> $iuc_no,"serviceID"=> $vtpass_isp_code), true);
        	curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
        	$curl_result = curl_exec($curl_request);
        	$curl_json_result = json_decode($curl_result, true);
        	

        	if(in_array($curl_json_result["code"],array("000"))){
            	$api_response = "successful";
            	$api_response_text = "";
            	$api_response_description = $curl_json_result["content"]["Customer_Name"];
            	$api_response_status = 1;
        	}

        	if(!in_array($curl_json_result["code"],array("000"))){
            	$api_response = "failed";
            	$api_response_text = "";
            	$api_response_description = "Err: Cannot Verify Customer";
            	$api_response_status = 3;
        	}
    	}else{
        	//Cable size not available
        	$api_response = "failed";
        	$api_response_text = "";
        	$api_response_description = "";
        	$api_response_status = 3;
    	}
    }else{
    //Service not available
    $api_response = "failed";
    $api_response_text = "";
    $api_response_description = "Service not available";
    $api_response_status = 3;
    }
curl_close($curl_request);
?>