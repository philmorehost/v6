<?php
    $data_service_provider_alter_code = array("mtn" => "gifting", "airtel" => "airtel_cg");
    if(in_array($product_name, array_keys($data_service_provider_alter_code))){
        if($product_name == "mtn"){
            $base_url_suffix = "gifting";
            $web_data_size_array = array("500mb" => "500","1gb" => "1000","2gb" => "2000","3gb" => "3000","5gb" => "5000","10gb" => "10000");
        }else{
            if($product_name == "airtel"){
                $base_url_suffix = "airtel-cg";
                $web_data_size_array = array("2gb" => "503","3gb" => "504");
            }else{
                if($product_name == "glo"){
                    $base_url_suffix = "";
                    $web_data_size_array = array();
                }else{
                    if($product_name == "9mobile"){
                        $base_url_suffix = "";
                        $web_data_size_array = array();
					}
                }
            }
        }
        if(in_array($quantity, array_keys($web_data_size_array))){
            $abumpay_reference = substr(str_shuffle("12345678901234567890"), 0, 15);
            $curl_url = "https://".$api_detail["api_base_url"]."/api/".str_replace("-","_",$base_url_suffix)."?apiToken=".$api_detail["api_key"]."&network=".$base_url_suffix."&network_code=".$web_data_size_array[$quantity]."&mobile=".$phone_no."&ref=".$abumpay_reference;
            $curl_request = curl_init($curl_url);
            curl_setopt($curl_request, CURLOPT_HTTPGET, true);
            curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
            $curl_result = curl_exec($curl_request);
            $curl_json_result = json_decode($curl_result, true);
            

            if(curl_errno($curl_request)){
                $api_response = "failed";
                $api_response_text = 1;
                $api_response_description = "";
                $api_response_status = 3;
            }
            
            if(in_array($curl_json_result["code"],array(200))){
                $api_response = "successful";
                $api_response_reference = $abumpay_reference;
                $api_response_text = $curl_json_result["status"];
                $api_response_description = "Transaction Successful | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
                $api_response_status = 1;
            }
            
            if(!in_array($curl_json_result["code"],array(200))){
                $api_response = "failed";
                $api_response_text = $curl_json_result["status"];
                $api_response_description = "Transaction Failed | ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data shared to 234".substr($phone_no, "1", "11")." failed";
                $api_response_status = 3;
            }
        }else{
            //Data size not available
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