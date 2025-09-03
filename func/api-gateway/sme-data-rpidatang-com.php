<?php
    $data_service_provider_alter_code = array("mtn" => "mtn", "airtel" => "airtel", "9mobile" => "9mobile");
    if(in_array($product_name, array_keys($data_service_provider_alter_code))){
        if($product_name == "mtn"){
            $net_id = "1";
            $web_data_size_array = array("500mb"=>"213","1gb"=>"215","2gb"=>"216","3gb"=>"238","5gb"=>"218","10gb"=>"224");
        }else{
            if($product_name == "airtel"){
                $net_id = "4";
                $web_data_size_array = array("500mb"=>"232","1gb"=>"233","2gb"=>"234","5gb"=>"235","10gb"=>"150");
            }else{
                if($product_name == "glo"){
                    $net_id = "2";
                    $web_data_size_array = array();
                }else{
                    if($product_name == "9mobile"){
                        $net_id = "3";
                        $web_data_size_array = array();
                    }
                }
            }
        }
        if(in_array($quantity, array_keys($web_data_size_array))){
            $curl_url = "https://".$api_detail["api_base_url"]."/api/data/";
            $curl_request = curl_init($curl_url);
            curl_setopt($curl_request, CURLOPT_POST, true);
            curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
            $curl_http_headers = array(
                "Authorization: Token  ".$api_detail["api_key"],
                "Content-Type: application/json",
            );
            curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
            $curl_postfields_data = json_encode(array("network"=>$net_id,"plan"=>$web_data_size_array[$quantity],"mobile_number"=>$phone_no,"Ported_number"=>true),true);
            curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
            $curl_result = curl_exec($curl_request);
            $curl_json_result = json_decode($curl_result, true);
            

            if(curl_errno($curl_request)){
                $api_response = "failed";
                $api_response_text = 1;
                $api_response_description = "";
                $api_response_status = 3;
            }
            
            if(in_array($curl_json_result["Status"],array("successful"))){
                $api_response = "successful";
                $api_response_reference = $curl_json_result["id"];
                $api_response_text = $curl_json_result["Status"];
                $api_response_description = "Transaction Successful | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
                $api_response_status = 1;
            }
            
            if(in_array($curl_json_result["Status"],array("pending"))){
                $api_response = "pending";
                $api_response_reference = $curl_json_result["id"];
                $api_response_text = $curl_json_result["Status"];
                $api_response_description = "Transaction Pending | You have successfully shared ".strtoupper(str_replace(["_","-"]," ",$quantity))." Data to 234".substr($phone_no, "1", "11");
                $api_response_status = 2;
            }
            
            if(!in_array($curl_json_result["Status"],array("successful","pending"))){
                $api_response = "failed";
                $api_response_text = $curl_json_result["Status"];
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