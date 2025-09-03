<?php
    $data_service_provider_alter_code = array("mtn" => "mtn", "airtel" => "airtel", "glo" => "glo", "9mobile" => "9mobile");
    if(in_array($product_name, array_keys($data_service_provider_alter_code))){
        if($product_name == "mtn"){
            $web_data_size_array = array("250mb" => "mtn_corporate_data_250mb", "500mb" => "mtn_corporate_data_500mb", "1gb" => "mtn_corporate_data_1gb", "2gb" => "mtn_corporate_data_2gb", "3gb" => "mtn_corporate_data_3gb", "5gb" => "mtn_corporate_data_5gb", "10gb" => "mtn_corporate_data_10gb");
        }else{
            if($product_name == "airtel"){
                $web_data_size_array = array();
            }else{
                if($product_name == "glo"){
                    $web_data_size_array = array("200mb" => "glo_cg_200mb", "500mb" => "glo_cg_500mb", "1gb" => "glo_cg_1gb", "2gb" => "glo_cg_2gb", "3gb" => "glo_cg_3gb", "5gb" => "glo_cg_5gb", "10gb" => "glo_cg_10gb");
                }else{
                    if($product_name == "9mobile"){
                        $web_data_size_array = array();
                    }
                }
            }
        }
        if(in_array($quantity, array_keys($web_data_size_array))){
            $curl_url = "https://".$api_detail["api_base_url"]."/api/v2/datashare/?api_key=".$api_detail["api_key"]."&product_code=".$web_data_size_array[$quantity]."&phone=".$phone_no;
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
            
            if(in_array($curl_json_result["error_code"],array(1986))){
                $api_response = "successful";
                $api_response_reference = $curl_json_result["data"]["recharge_id"];
                $api_response_text = $curl_json_result["data"]["text_status"];
                $api_response_description = "Transaction Successful | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
                $api_response_status = 1;
            }
            
            if(in_array($curl_json_result["error_code"],array(1981))){
                $api_response = "pending";
                $api_response_reference = $curl_json_result["data"]["recharge_id"];
                $api_response_text = $curl_json_result["data"]["text_status"];
                $api_response_description = "Transaction Pending | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
                $api_response_status = 2;
            }
            
            if(!in_array($curl_json_result["error_code"],array(1986, 1981))){
                $api_response = "failed";
                $api_response_text = $curl_json_result["data"]["text_status"];
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