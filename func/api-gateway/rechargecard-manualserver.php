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
			$select_rechargecard_products = mysqli_query($connection_server, "SELECT * FROM sas_cards WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && card_name='$card_name'");
			
			if(mysqli_num_rows($select_rechargecard_products) == 1){
				$get_rechargecards = mysqli_fetch_array($select_rechargecard_products);
				$explode_rechargecards = array_filter(explode(",",trim($get_rechargecards["cards"])));
				//Cards
				$users_card_purchased_array = array_slice($explode_rechargecards,0,$qty_number);
				$remaining_card_array = array_slice($explode_rechargecards,$qty_number);
				
				if(count($explode_rechargecards) >= $qty_number){
					
					foreach($users_card_purchased_array as $user_card){
						$all_users_cards .= $user_card."\n";
					}
					
					foreach($remaining_card_array as $remaining_card){
						$all_remaining_cards .= $remaining_card."\n";
					}
					
					$users_card_purchased = str_replace("\n",",",trim($all_users_cards));
					$remaining_card = str_replace("\n",",",trim($all_remaining_cards));
					mysqli_query($connection_server, "UPDATE sas_cards SET cards='$remaining_card' WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && card_name='$card_name'");
					mysqli_query($connection_server, "INSERT INTO sas_card_purchaseds (vendor_id, reference, business_name, card_type, username, card_name, cards) VALUES ('".$get_logged_user_details["vendor_id"]."', '$reference', '$business_name', '$type', '".$get_logged_user_details["username"]."', '$card_name', '$users_card_purchased')");
					
					$api_response = "successful";
					$api_response_reference = $reference;
					$api_response_text = "";
					$api_response_description = "Transaction Successful";
					$api_response_status = 1;
				}else{
					$api_response = "failed";
					$api_response_text = "";
					$api_response_description = "Error: Few Cards Left, Contact Admin";
					$api_response_status = 3;
				}
			}else{
				$api_response = "failed";
				$api_response_text = "";
				$api_response_description = "Cards Store Cannot Be Reached, Contact Admin";
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