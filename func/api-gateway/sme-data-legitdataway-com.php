<?php
    $data_service_provider_alter_code = array("mtn" => "mtn", "airtel" => "airtel", "9mobile" => "9mobile");
    if(in_array($product_name, array_keys($data_service_provider_alter_code))){
        if($product_name == "mtn"){
            $net_id = "1";
            $web_data_size_array = array("500mb"=>"36","1gb"=>"37","2gb"=>"38","3gb"=>"39","5gb"=>"40","10gb"=>"41");
        }else{
            if($product_name == "airtel"){
                $net_id = "2";
                $web_data_size_array = array();
            }else{
                if($product_name == "glo"){
                    $net_id = "3";
                    $web_data_size_array = array();
                }else{
                    if($product_name == "9mobile"){
                        $net_id = "4";
                        $web_data_size_array = array();
                    }
                }
            }
        }
        if(in_array($quantity, array_keys($web_data_size_array))){
            $curl_url = "https://".$api_detail["api_base_url"]."/api/user";
            $curl_legitdataway_user_request = curl_init($curl_url);
            curl_setopt($curl_legitdataway_user_request, CURLOPT_POST, true);
            curl_setopt($curl_legitdataway_user_request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_legitdataway_user_request, CURLOPT_SSL_VERIFYHOST, true);
            curl_setopt($curl_legitdataway_user_request, CURLOPT_SSL_VERIFYPEER, true);
            $curl_legitdataway_user_http_headers = array(
                "Authorization: Basic ".base64_encode($api_detail["api_key"]),
                "Content-Type: application/json",
            );
            curl_setopt($curl_legitdataway_user_request, CURLOPT_HTTPHEADER, $curl_legitdataway_user_http_headers);
            $curl_legitdataway_user_result = curl_exec($curl_legitdataway_user_request);
            $curl_legitdataway_user_json_result = json_decode($curl_legitdataway_user_result, true);
            curl_close($curl_legitdataway_user_request);

            if(curl_errno($curl_request)){
                $api_response = "failed";
                $api_response_text = 1;
                $api_response_description = "";
                $api_response_status = 3;
            }
            
            if($curl_legitdataway_user_json_result["status"] == "success"){
                $curl_url = "https://".$api_detail["api_base_url"]."/api/data/";
                $curl_request = curl_init($curl_url);
                curl_setopt($curl_request, CURLOPT_POST, true);
                curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
                $curl_http_headers = array(
                    "Authorization: Token  ".$curl_legitdataway_user_json_result["AccessToken"],
                    "Content-Type: application/json",
                );
                curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
                $legitdataway_reference = substr(str_shuffle("12345678901234567890"), 0, 15);
                $curl_postfields_data = json_encode(array("network"=>$net_id,"data_plan"=>$web_data_size_array[$quantity],"phone"=>$phone_no,"bypass"=>true,"request-id"=>$legitdataway_reference),true);
                curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
                $curl_result = curl_exec($curl_request);
                $curl_json_result = json_decode($curl_result, true);
                

                if(in_array($curl_json_result["status"],array("success"))){
                    $api_response = "successful";
                    $api_response_reference = $curl_json_result["request-id"];
                    $api_response_text = $curl_json_result["status"];
                    $api_response_description = "Transaction Successful | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
                    $api_response_status = 1;
                }
                
                if(!in_array($curl_json_result["status"],array("success"))){
                    $api_response = "failed";
                    $api_response_text = $curl_json_result["status"];
                    $api_response_description = "Transaction Failed | ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data shared to 234".substr($phone_no, "1", "11")." failed";
                    $api_response_status = 3;
                }
            }else{
                //Err: Could not connect
                $api_response = "failed";
                $api_response_text = "";
                $api_response_description = "Err: Could not connect";
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