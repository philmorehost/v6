<?php
	$recharge_service_provider_alter_code = array("mtn" => "mtn", "airtel" => "airtel", "glo" => "glo", "9mobile" => "9mobile");
    if(in_array($product_name, array_keys($recharge_service_provider_alter_code))){
        if($product_name == "mtn"){
            $web_recharge_size_array = array("100","200","500");
        }else{
            if($product_name == "airtel"){
                $web_recharge_size_array = array("100","200","500");
            }else{
                if($product_name == "glo"){
                    $web_recharge_size_array = array("100","200","500");
                }else{
                    if($product_name == "9mobile"){
                        $web_recharge_size_array = array("100","200","500");
                    }
                }
            }
        }
	
		if(in_array($quantity, $web_recharge_size_array)){
			$card_name = $isp."_".$quantity;
			$curl_url = "https://".$api_detail["api_base_url"]."/web/api/card.php";
			$curl_request = curl_init($curl_url);
			curl_setopt($curl_request, CURLOPT_POST, true);
			curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
			$curl_http_headers = array(
				"Content-Type: application/json",
			);
			curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
			$curl_postfields_data = json_encode(array("api_key"=> $api_detail["api_key"],"network"=> $product_name,"qty_number"=> $qty_number,"type"=> "rechargecard", "quantity" => $web_recharge_size_array[$quantity], "card_name" => $business_name), true);
			curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
			$curl_result = curl_exec($curl_request);
			$curl_json_result = json_decode($curl_result, true);
			
			
			if(curl_errno($curl_request)){
				$api_response = "failed";
				$api_response_text = 1;
				$api_response_description = "";
				$api_response_status = 3;
			}
			
			if(in_array($curl_json_result["status"],array("success"))){
				mysqli_query($connection_server, "INSERT INTO sas_card_purchaseds (vendor_id, reference, business_name, card_type, username, card_name, cards) VALUES ('".$get_logged_user_details["vendor_id"]."', '$reference', '$business_name', '$type', '".$get_logged_user_details["username"]."', '$card_name', '".str_replace(",",",",$curl_json_result["cards"])."')");
				$users_card_purchased = str_replace(",",",",$curl_json_result["cards"]);
				$api_response = "successful";
				$api_response_reference = $curl_json_result["ref"];
				$api_response_text = $curl_json_result["status"];
				$api_response_description = "Transaction Successful";
				$api_response_status = 1;
			}
			
			if(in_array($curl_json_result["status"],array("failed"))){
				$api_response = "failed";
				$api_response_text = $curl_json_result["status"];
				$api_response_description = "Transaction Failed";
				$api_response_status = 3;
			}
		}else{
			//recharge size not available
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