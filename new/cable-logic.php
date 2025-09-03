<?php
session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
include("../func/bc-config.php");

if(isset($_POST["verify-cable"]) || isset($_POST["buy-cable"])){
    $purchase_method = "web";
    $action_function = 0;
    if(isset($_POST["verify-cable"])){
        $action_function = 3; // Corresponds to verification
    }
    if(isset($_POST["buy-cable"])){
        $action_function = 1; // Corresponds to purchase
    }

    // This is the logic from web/func/cable.php
    $purchase_method = strtoupper($purchase_method);
    $purchase_method_array = array("API", "WEB");
    if (in_array($purchase_method, $purchase_method_array)) {
        if ($purchase_method === "WEB") {
            if (isset($_SESSION["cable_name"])) {
                $isp = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_SESSION["cable_provider"]))));
                $iuc_no = mysqli_real_escape_string($connection_server, trim(strip_tags($_SESSION["iuc_number"])));
                $quantity = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_SESSION["cable_package"]))));
            } else {
                $isp = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["provider"]))));
                $iuc_no = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["iuc"])));
                $quantity = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["package"]))));
            }
        }

        if (in_array($purchase_method, array("API", "APP"))) {
            $isp = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["type"]))));
            $iuc_no = mysqli_real_escape_string($connection_server, trim(strip_tags($get_api_post_info["iuc_number"])));
            $quantity = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($get_api_post_info["package"]))));
        }
        //$discounted_amount = $amount;
        $type_alternative = ucwords($isp . " cable");
        $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
        $description = "Cable charges";
        $status = 3;

        $cable_type_array = array("startimes", "dstv", "gotv");
        if (in_array($isp, $cable_type_array)) {
            //Purchase Service
            if ($action_function == 1) {
                if (!empty(userBalance(1)) && is_numeric(userBalance(1)) && (userBalance(1) > 0)) {
                    if (!empty($isp) && !empty($iuc_no) && is_numeric($iuc_no) && !empty($quantity)) {

                        $cable_type_table_name_arrays = array("startimes" => "sas_cable_status", "dstv" => "sas_cable_status", "gotv" => "sas_cable_status");
                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $cable_type_table_name_arrays[$isp] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$isp'"));
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='cable'");
                        $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='cable' && status='1'");

                        if (mysqli_num_rows($get_api_lists) > 0) {
                            if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                                while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                    if (!empty($api_detail["api_key"])) {
                                        if ($api_detail["status"] == 1) {
                                            $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                                            if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                                                $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                                                $cable_type_table_name = $cable_type_table_name_arrays[$isp];
                                                $product_name = strtolower($isp);
                                                $product_status_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $cable_type_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                                $product_discount_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && api_id='" . $api_detail["id"] . "' && product_id='" . $product_table["id"] . "' && val_1='" . $quantity . "' LIMIT 1"));
                                                $amount = $product_discount_table["val_2"];
                                                $discounted_amount = $amount;
                                            }
                                            if (!empty(trim($product_discount_table["val_1"])) && !empty(trim($product_discount_table["val_2"])) && is_numeric($product_discount_table["val_2"])) {
                                                if ((userBalance(1) >= $amount) && !empty($amount) && is_numeric($amount)) {
                                                    if (($product_table["status"] == 1) && ($product_status_table["status"] == 1)) {
                                                        if (productIDBlockChecker($iuc_no) == "success") {
                                                            if (productIDPurchaseChecker($iuc_no, "cable") == "success") {
                                                                $debit_user = chargeUser("debit", $iuc_no, $type_alternative, $reference, "", $amount, $discounted_amount, $description, $purchase_method, $_SERVER["HTTP_HOST"], $status);
                                                                if ($debit_user === "success") {
                                                                    $api_gateway_name_file_exists = "cable-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name_file_exists)) {
                                                                        $api_gateway_name = "cable-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                                                    } else {
                                                                        $api_gateway_name = "cable-localserver.php";
                                                                    }

                                                                    // Reset variables at the start of each transaction
                                                                    $api_response = null;
                                                                    $api_response_description = null;
                                                                    $api_response_reference = null;
                                                                    $api_response_text = null;
                                                                    $api_response_status = null;

                                                                    include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/" . $api_gateway_name);
                                                                    $api_response_text = strtolower($api_response_text);
                                                                    if (in_array($api_response, array("successful"))) {
                                                                        updateProductPurchaseList($reference, $iuc_no, "cable");
                                                                        alterTransaction($reference, "status", $api_response_status);
                                                                        alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                        alterTransaction($reference, "product_id", $product_table["id"]);
                                                                        alterTransaction($reference, "api_reference", $api_response_reference);
                                                                        alterTransaction($reference, "description", $api_response_description);
                                                                        alterTransaction($reference, "api_website", $api_detail["api_base_url"]);
                                                                        $json_response_array = array("ref" => $reference, "status" => "success", "desc" => "Transaction Successful", "response_desc" => $api_response_description);
                                                                    }

                                                                    if (in_array($api_response, array("pending"))) {
                                                                        updateProductPurchaseList($reference, $iuc_no, "cable");
                                                                        alterTransaction($reference, "status", $api_response_status);
                                                                        alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                        alterTransaction($reference, "product_id", $product_table["id"]);
                                                                        alterTransaction($reference, "api_reference", $api_response_reference);
                                                                        alterTransaction($reference, "description", $api_response_description);
                                                                        alterTransaction($reference, "api_website", $api_detail["api_base_url"]);
                                                                        $json_response_array = array("ref" => $reference, "status" => "pending", "desc" => "Transaction Pending", "response_desc" => $api_response_description);
                                                                    }

                                                                    if ($api_response == "failed") {
                                                                        $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                                                        alterTransaction($reference, "api_id", $api_detail["id"]);
                                                                        alterTransaction($reference, "product_id", $product_table["id"]);
                                                                        alterTransaction($reference, "api_reference", $api_response_reference);
                                                                        alterTransaction($reference, "description", $api_response_description);
                                                                        chargeUser("credit", $iuc_no, "Refund", $reference_2, "", $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $purchase_method, $_SERVER["HTTP_HOST"], "1");
                                                                        $json_response_array = array("status" => "failed", "desc" => "Transaction Failed");
                                                                    }
                                                                } else {
                                                                    $json_response_array = array("status" => "failed", "desc" => "Unable to proceed with charges");
                                                                }
                                                            } else {
                                                                $json_response_array = array("status" => "failed", "desc" => "Error: Daily Limit Exceeded For This Cable IUC NO: " . $iuc_no . ", Contact Admin for Support");
                                                            }
                                                        } else {
                                                            $json_response_array = array("status" => "failed", "desc" => "Error: Cable IUC Number has been blocked");
                                                        }
                                                    } else {
                                                        $json_response_array = array("status" => "failed", "desc" => "Product Locked");
                                                    }
                                                } else {
                                                    $json_response_array = array("status" => "failed", "desc" => "Insufficient Wallet Balance");
                                                }
                                            } else {
                                                $json_response_array = array("status" => "failed", "desc" => "Cable size not available");
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
                if (!empty($iuc_no) && is_numeric($iuc_no) && !empty($isp) && !empty($quantity)) {
                    $cable_type_table_name_arrays = array("startimes" => "sas_cable_status", "dstv" => "sas_cable_status", "gotv" => "sas_cable_status");
                    $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM " . $cable_type_table_name_arrays[$isp] . " WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='$isp'"));
                    $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='cable'");
                    $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='cable' && status='1'");

                    if (mysqli_num_rows($get_api_lists) > 0) {
                        if (mysqli_num_rows($get_api_enabled_lists) == 1) {
                            while ($api_detail = mysqli_fetch_array($get_api_lists)) {
                                if (!empty($api_detail["api_key"])) {
                                    if ($api_detail["status"] == 1) {
                                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                                        if ($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true) {
                                            $cable_type_table_name = $cable_type_table_name_arrays[$isp];
                                            $product_name = strtolower($isp);
                                            $product_status_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM $cable_type_table_name WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                            $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && product_name='" . $product_name . "' LIMIT 1"));
                                        }
                                        if (($product_table["status"] == 1) && ($product_status_table["status"] == 1)) {
                                            $api_gateway_name_file_exists = "cable-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/verify/" . $api_gateway_name_file_exists)) {
                                                $api_gateway_name = "cable-" . str_replace(".", "-", $api_detail["api_base_url"]) . ".php";
                                            } else {
                                                $api_gateway_name = "cable-localserver.php";
                                            }

                                            // Reset variables at the start of each transaction
                                            $api_response = null;
                                            $api_response_description = null;
                                            $api_response_reference = null;
                                            $api_response_text = null;
                                            $api_response_status = null;

                                            include_once($_SERVER['DOCUMENT_ROOT'] . "/func/api-gateway/verify/" . $api_gateway_name);
                                            if (in_array($api_response, array("successful", "pending"))) {
                                                $_SESSION["iuc_number"] = $iuc_no;
                                                $_SESSION["cable_provider"] = $isp;
                                                $_SESSION["cable_package"] = $quantity;
                                                $_SESSION["cable_name"] = $api_response_description;
                                                $json_response_array = array("status" => "success", "desc" => $api_response_description);
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
            $json_response_array = array("status" => "failed", "desc" => "Invalid cable type");
        }
    } else {
        $json_response_array = array("status" => "failed", "desc" => "Purchase Method Not specified");
    }

    if (isset($json_response_array)) {
        $_SESSION["product_purchase_response"] = $json_response_array["desc"];
    }
    header("Location: cable.php");
} else {
    header("Location: cable.php");
}
?>
