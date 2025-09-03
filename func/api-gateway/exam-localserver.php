<?php
$exam_service_provider_alter_code = array("waec" => "waec", "neco" => "neco", "nabteb" => "nabteb", "jamb" => "jamb");
if (in_array($product_name, array_keys($exam_service_provider_alter_code))) {
	if ($product_name == "waec") {
		$web_exam_size_array = array("result_checker" => "result_checker");
	} else {
		if ($product_name == "neco") {
			$web_exam_size_array = array("result_checker" => "result_checker");
		} else {
			if ($product_name == "nabteb") {
				$web_exam_size_array = array("result_checker" => "result_checker");
			} else {
				if ($product_name == "jamb") {
					$web_exam_size_array = array("direct_entry" => "direct_entry", "utme_with_mock" => "utme_with_mock", "utme_without_mock" => "utme_without_mock");
				}
			}
		}
	}

	if (in_array($quantity, array_keys($web_exam_size_array))) {
		$curl_url = "https://" . $api_detail["api_base_url"] . "/web/api/exam.php";
		$curl_request = curl_init($curl_url);
		curl_setopt($curl_request, CURLOPT_POST, true);
		curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
		$curl_http_headers = array(
			"Content-Type: application/json",
		);
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_http_headers);
		$curl_postfields_data = json_encode(array("api_key" => $api_detail["api_key"], "type" => $product_name, "quantity" => $web_exam_size_array[$quantity]), true);
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $curl_postfields_data);
		$curl_result = curl_exec($curl_request);
		$curl_json_result = json_decode($curl_result, true);
		

		if (curl_errno($curl_request)) {
			$api_response = "failed";
			$api_response_text = 1;
			$api_response_description = "";
			$api_response_status = 3;
		}

		if (in_array($curl_json_result["status"], array("success"))) {
			$api_response = "successful";
			$api_response_reference = $curl_json_result["ref"];
			$api_response_text = $curl_json_result["status"];
			$api_response_description = $curl_json_result["response_desc"];
			$api_response_status = 1;
		}

		if (in_array($curl_json_result["status"], array("pending"))) {
			$api_response = "pending";
			$api_response_reference = $curl_json_result["ref"];
			$api_response_text = $curl_json_result["status"];
			$api_response_description = $curl_json_result["response_desc"];
			$api_response_status = 2;
		}

		if (in_array($curl_json_result["status"], array("failed"))) {
			$api_response = "failed";
			$api_response_text = $curl_json_result["status"];
			$api_response_description = "Transaction Failed";
			$api_response_status = 3;
		}
	} else {
		//Exam size not available
		$api_response = "failed";
		$api_response_text = "";
		$api_response_description = "";
		$api_response_status = 3;
	}
} else {
	//Service not available
	$api_response = "failed";
	$api_response_text = "";
	$api_response_description = "Service not available";
	$api_response_status = 3;
}
curl_close($curl_request);
?>