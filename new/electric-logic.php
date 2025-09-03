<?php
session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
include("../func/bc-config.php");

if(isset($_POST["verify-meter"]) || isset($_POST["buy-electric"])){
    $purchase_method = "web";
    $action_function = 0;
    if(isset($_POST["verify-meter"])){
        $action_function = 3; // Corresponds to verification
    }
    if(isset($_POST["buy-electric"])){
        $action_function = 1; // Corresponds to purchase
    }

    // This is the logic from web/func/electric.php
    $purchase_method = strtoupper($purchase_method);
    $purchase_method_array = array("API", "WEB");
    if (in_array($purchase_method, $purchase_method_array)) {
        if ($purchase_method === "WEB") {
            if (isset($_SESSION["meter_name"])) {
                $epp = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_SESSION["meter_provider"]))));
                $type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_SESSION["meter_type"]))));
                $amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_SESSION["meter_amount"]))));
                $meter_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_SESSION["meter_number"]))));
            } else {
                $epp = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["provider"]))));
                $type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["meter-type"]))));
                $amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["amount"]))));
                $meter_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["meter-number"]))));
            }

        }

        if (in_array($purchase_method, array("API", "APP"))) {
            $epp = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["provider"]))));
            $type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["type"]))));
            $amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($get_api_post_info["amount"]))));
            $meter_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($get_api_post_info["meter_number"]))));
        }
        $type_alternative = ucwords($epp . " electric");
        $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
        $description = "Electric Charges";
        $status = 3;

        $electric_type_array = array("ekedc", "eedc", "ikedc", "jedc", "kedco", "ibedc", "phed", "aedc", "yedc");
        if (in_array($epp, $electric_type_array)) {
            //Purchase Service
            if ($action_function == 1) {
                if (!empty(userBalance(1)) && is_numeric(userBalance(1)) && (userBalance(1) > 0)) {
                    if (userBalance(1) >= $amount && !empty($amount) && is_numeric($amount) && !empty($epp) && !empty($type)) {

                        $electric_type_table_name_arrays = array("ekedc" => "sas_electric_status", "eedc" => "sas_electric_status", "ikedc" => "sas_electric_status", "jedc" => "sas_electric_status", "kedco" => "sas_electric_status", "ibedc" => "sas_electric_status", "phed" => "sas_electric_status", "aedc" => "sas_electric_status", "yedc" => "sas_electric_status");
                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $electric_type_table_name_arrays[$epp] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$epp'"));
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='electric'");
                        $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='electric' && status='1'");

                        if (mysqli_num_rows($get_api_lists) > 0) {
                            if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                                while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                    if (!empty($api_detail["api_key"])) {
                                        if ($api_detail["status"] == 1) {
                                            $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                                            if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                                                $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                                                $electric_type_table_name = $electric_type_table_name_arrays[$epp];
                                                $product_name = strtolower($epp);
                                                $product_status_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $electric_type_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                $product_discount_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_id='" . $api_detail["id"] . "' && product_id='" . $product_table["id"] . "' LIMIT 1"));
                                                $discounted_amount = ($amount - ($amount * ($product_discount_table["val_1"] / 100)));
                                            }
                                            if (is_numeric($product_discount_table["val_1"])) {
                                                if ((userBalance(1) >= $amount) && !empty($amount) && is_numeric($amount)) {
                                                    if (($product_table["status"] == 1) && ($product_status_table["status"] == 1)) {
                                                        if (productIDBlockChecker($meter_number) == "success") {
                                                            if (productIDPurchaseChecker($meter_number, "electric") == "success") {
                                                                $debit_user = chargeUser("debit", $meter_number, $type_alternative, $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                                                if ($debit_user === "success") {
                                                                    $api_gateway_name_file_exists = "electric-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                                        $api_gateway_name = "electric-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                                    } else {
                                                                        $api_gateway_name = "electric-localserver.php";
                                                                    }

                                                                    $api_response = null;
                                                                    $api_response_description = null;
                                                                    $api_response_reference = null;
                                                                    $api_response_text = null;
                                                                    $api_response_status = null;

                                                                    include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                                                    $api_response_text = strtolower($api_response_text);
                                                                    if (in_array($api_response, array("successful"))) {
                                                                        updateProductPurchaseList($reference, $meter_number, "electric");
                                                                        alterTransaction($reference, "status", $api_response_status);
                                                                        alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                        alterTransaction($reference, "product_id", $product_table["id"]);
                                                                        alterTransaction($reference, "api_reference", $api_response_reference);
                                                                        alterTransaction($reference, "description", $api_response_description);
                                                                        alterTransaction($reference, "api_website", $api_detail["api_base_url"]);
                                                                        $json_response_array = array("ref" => $reference, "status" => "success", "meter_number" => $api_response_meter_number, "token" => $api_response_token, "token_unit" => $api_response_token_unit, "desc" => "Transaction Successful", "response_desc" => $api_response_description);
                                                                    }

                                                                    if (in_array($api_response, array("pending"))) {
                                                                        updateProductPurchaseList($reference, $meter_number, "electric");
                                                                        alterTransaction($reference, "status", $api_response_status);
                                                                        alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                        alterTransaction($reference, "product_id", $product_table["id"]);
                                                                        alterTransaction($reference, "api_reference", $api_response_reference);
                                                                        alterTransaction($reference, "description", $api_response_description);
                                                                        alterTransaction($reference, "api_website", $api_detail["api_base_url"]);
                                                                        $json_response_array = array("ref" => $reference, "status" => "pending", "meter_number" => $api_response_meter_number, "token" => $api_response_token, "desc" => "Transaction Pending", "response_desc" => $api_response_description);
                                                                    }

                                                                    if ($api_response == "failed") {
                                                                        $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                                                        alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                        alterTransaction($reference, "product_id", $product_table["id"]);
                                                                        alterTransaction($reference, "api_reference", $api_response_reference);
                                                                        alterTransaction($reference, "description", $api_response_description);
                                                                        chargeUser("credit", $meter_number, "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
                                                                        $json_response_array = array("status" => "failed", "desc" => "Transaction Failed");
                                                                    }
                                                                } else {
                                                                    $json_response_array = array("status" => "failed", "desc" => "Unable to proceed with charges");
                                                                }
                                                            } else {
                                                                $json_response_array = array("status" => "failed", "desc" => "Error: Daily Limit Exceeded For This Meter Number: " . $meter_number . ", Contact Admin for Support");
                                                            }
                                                        } else {
                                                            $json_response_array = array("status" => "failed", "desc" => "Error: Meter number has been blocked");
                                                        }
                                                    } else {
                                                        $json_response_array = array("status" => "failed", "desc" => "Product Locked");
                                                    }
                                                } else {
                                                    $json_response_array = array("status" => "failed", "desc" => "Insufficient Wallet Balance");
                                                }
                                            } else {
                                                $json_response_array = array("status" => "failed", "desc" => "Electric size not available");
                                            }
                                        } else {
                                            $json_response_array = array("status" => "failed", "desc" => "System Is Busy");
                                        }
                                    } else {
                                        $json_response_array = array("status" => "failed", "desc" => "Empty Gateway Key");
                                    }
                                }
                            } else {
                                if (mysqli_num_rows($get_api_enabled_lists) > 1) {
                                    $json_response_array = array("status" => "failed", "desc" => "System is unavailable, try again later");
                                } else {
                                    if (mysqli_num_rows($get_api_enabled_lists) < 1) {
                                        $json_response_array = array("status" => "failed", "desc" => "Product Not Available");
                                    }
                                }
                            }
                        } else {
                            $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                        }

                    } else {
                        $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
                    }
                } else {
                    $json_response_array = array("status" => "failed", "desc" => "Balance is LOW");
                }
            }

            //Verify Service
            if ($action_function == 3) {
                if (!empty($meter_number) && is_numeric($meter_number) && !empty($epp) && !empty($type)) {
                    $electric_type_table_name_arrays = array("ekedc" => "sas_electric_status", "eedc" => "sas_electric_status", "ikedc" => "sas_electric_status", "jedc" => "sas_electric_status", "kedco" => "sas_electric_status", "ibedc" => "sas_electric_status", "phed" => "sas_electric_status", "aedc" => "sas_electric_status", "yedc" => "sas_electric_status");
                    $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $electric_type_table_name_arrays[$epp] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$epp'"));
                    $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='electric'");
                    $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='electric' && status='1'");

                    if (mysqli_num_rows($get_api_lists) > 0) {
                        if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                            while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                if (!empty($api_detail["api_key"])) {
                                    if ($api_detail["status"] == 1) {
                                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                                        if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                                            $electric_type_table_name = $electric_type_table_name_arrays[$epp];
                                            $product_name = strtolower($epp);
                                            $product_status_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $electric_type_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                            $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                        }
                                        if (($product_table["status"] == 1) && ($product_status_table["status"] == 1)) {
                                            $api_gateway_name_file_exists = "electric-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/verify/" . $api_gateway_name_file_exists)) {
                                                $api_gateway_name = "electric-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            } else {
                                                $api_gateway_name = "electric-localserver.php";
                                            }

                                            $api_response = null;
                                            $api_response_description = null;
                                            $api_response_customer_name = null;
                                            $api_response_customer_address = null;

                                            include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/verify/" . $api_gateway_name);
                                            if (in_array($api_response, array("successful", "pending"))) {
                                                $_SESSION["meter_amount"] = $amount;
                                                $_SESSION["meter_number"] = $meter_number;
                                                $_SESSION["meter_provider"] = $epp;
                                                $_SESSION["meter_type"] = $type;
                                                $_SESSION["meter_name"] = $api_response_description; // Assuming customer name is in description
                                                $json_response_array = array("status" => "success", "desc" => $api_response_description, "customer_name" => $api_response_customer_name, "customer_address" => $api_response_customer_address);
                                            }
                                            if ($api_response == "failed") {
                                                $json_response_array = array("status" => "failed", "desc" => "Error: Unable to verify customer");
                                            }
                                        } else {
                                            $json_response_array = array("status" => "failed", "desc" => "Product Locked");
                                        }
                                    } else {
                                        $json_response_array = array("status" => "failed", "desc" => "System Is Busy");
                                    }
                                } else {
                                    $json_response_array = array("status" => "failed", "desc" => "Empty Gateway Key");
                                }
                            }
                        } else {
                            if (mysqli_num_rows($get_api_enabled_lists) > 1) {
                                $json_response_array = array("status" => "failed", "desc" => "System is unavailable, try again later");
                            } else {
                                if (mysqli_num_rows($get_api_enabled_lists) < 1) {
                                    $json_response_array = array("status" => "failed", "desc" => "Product Not Available");
                                }
                            }
                        }
                    } else {
                        $json_response_array = array("status" => "failed", "desc" => "Gateway Error");
                    }
                } else {
                    $json_response_array = array("status" => "failed", "desc" => "Incomplete Parameters");
                }
            }

        } else {
            $json_response_array = array("status" => "failed", "desc" => "Invalid electric type");
        }
    } else {
        $json_response_array = array("status" => "failed", "desc" => "Purchase Method Not specified");
    }

    if(isset($json_response_array)){
         $_SESSION["product_purchase_response"] = $json_response_array["desc"];
    }
    header("Location: electric.php");
} else {
    header("Location: electric.php");
}
?>
