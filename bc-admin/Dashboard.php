<?php session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
    include("../func/bc-admin-config.php");
    
    $select_vendor_super_admin_status_message = mysqli_query($connection_server, "SELECT * FROM sas_super_admin_status_messages");
    if(mysqli_num_rows($select_vendor_super_admin_status_message) == 1){
    	$get_vendor_super_admin_status_message = mysqli_fetch_array($select_vendor_super_admin_status_message);
    	if(!isset($_SESSION["product_purchase_response"]) && isset($_SESSION["admin_session"])){
    		$vendor_super_admin_status_message_template_encoded_text_array = array("{firstname}" => $get_logged_admin_details["firstname"]);
    		foreach($vendor_super_admin_status_message_template_encoded_text_array as $array_key => $array_val){
    			$vendor_super_admin_status_message_template_text = str_replace($array_key, $array_val, $get_vendor_super_admin_status_message["message"]);
    		}
    		$_SESSION["product_purchase_response"] = str_replace("\n","<br/>",$vendor_super_admin_status_message_template_text);
    	}
    }
    
    if(isset($_POST["pay-bill"])){
        $purchase_method = "web";
        $purchase_method = strtoupper($purchase_method);
        $purchase_method_array = array("WEB");
        
        if(in_array($purchase_method, $purchase_method_array)){
            if($purchase_method === "WEB"){
                $bill_id = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["bill-id"]))));
            }
            
            if(!empty($bill_id)){
                if(is_numeric($bill_id)){
                    $get_bill_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_billings WHERE id='".$bill_id."'");
                    if(mysqli_num_rows($get_bill_details) == 1){
                    	$check_if_bill_is_paid = mysqli_query($connection_server, "SELECT * FROM sas_vendor_paid_bills WHERE vendor_id='".$get_logged_admin_details["id"]."' && bill_id='".$bill_id."'");
                    	if(mysqli_num_rows($check_if_bill_is_paid) == 0){
                        	$bill_amount = mysqli_fetch_array($get_bill_details);
                        	if(!empty($bill_amount["amount"]) && is_numeric($bill_amount["amount"]) && ($bill_amount["amount"] > 0)){
                            	if(!empty(vendorBalance(1)) && is_numeric(vendorBalance(1)) && (vendorBalance(1) > 0)){
                                	$amount = $bill_amount["amount"];
                                	$discounted_amount = $amount;
                                	$type_alternative = ucwords($bill_amount["bill_type"]);
                                	$reference = substr(str_shuffle("12345678901234567890"), 0, 15);
                                	$description = ucwords(checkTextEmpty($bill_amount["description"])." - Bill charges");
                                	$status = 1;
                                
                                	$debit_vendor = chargeVendor("debit", $bill_amount["bill_type"], $type_alternative, $reference, $amount, $discounted_amount, $description, $_SERVER["HTTP_HOST"], $status);
                                	if($debit_vendor === "success"){
                                    	$add_vendor_paid_bill_details = mysqli_query($connection_server, "INSERT INTO sas_vendor_paid_bills (vendor_id, bill_id, bill_type, description, amount, starting_date, ending_date) VALUES ('".$get_logged_admin_details["id"]."', '".$bill_amount["id"]."', '".$bill_amount["bill_type"]."', '".$bill_amount["description"]."', '$amount', '".$bill_amount["starting_date"]."','".$bill_amount["ending_date"]."')");
                                    	if($add_vendor_paid_bill_details == true){
                                        	//Account ... Bill Successfully
                                        	$json_response_array = array("desc" => "Account ".ucwords($bill_amount["bill_type"])." Bill Successfully");
                                        	$json_response_encode = json_encode($json_response_array,true);
                                    	}else{
                                        	$reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                        	chargeVendor("credit", $bill_amount["bill_type"], "Refund", $reference_2, $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $_SERVER["HTTP_HOST"], "1");
                                        	//Bill Failed, Contact Admin
                                        	$json_response_array = array("desc" => "Bill Failed, Contact Admin");
                                        	$json_response_encode = json_encode($json_response_array,true);
                                    	}
                                	}else{
                                    	//Insufficient Fund
                                    	$json_response_array = array("desc" => "Insufficient Fund");
                                    	$json_response_encode = json_encode($json_response_array,true);
                                	}
                            	}else{
                                	//Balance is LOW
                                	$json_response_array = array("desc" => "Balance is LOW");
                                	$json_response_encode = json_encode($json_response_array,true);
                            	}
                        	}else{
                            	//Pricing Error, Contact Admin
                            	$json_response_array = array("desc" => "Pricing Error, Contact Admin");
                            	$json_response_encode = json_encode($json_response_array,true);
                        	}
                        }else{
                        	//Bill Has Already Been Paid
                        	$json_response_array = array("desc" => "Bill Has Already Been Paid");
                        	$json_response_encode = json_encode($json_response_array,true);
                        }
                    }else{
                        //Error: Billing Details Not Exists, Contact Admin
                        $json_response_array = array("desc" => "Error: Billing Details Not Exists, Contact Admin");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    //Non-numeric Bill ID
                    $json_response_array = array("desc" => "Non-numeric Bill ID");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                //Bill Field Empty
                $json_response_array = array("desc" => "Bill Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }
        }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
	

	if((!empty($get_logged_admin_details["bank_code"]) && is_numeric($get_logged_admin_details["bank_code"]) && !empty($get_logged_admin_details["bvn"]) && is_numeric($get_logged_admin_details["bvn"]) && strlen($get_logged_admin_details["bvn"]) == 11) || (!empty($get_logged_admin_details["bank_code"]) && is_numeric($get_logged_admin_details["bank_code"]) && !empty($get_logged_admin_details["nin"]) && is_numeric($get_logged_admin_details["nin"]) && strlen($get_logged_admin_details["nin"]) == 11)){
		$virtual_account_vaccount_err = "";
		if((!empty($get_logged_admin_details["bvn"]) && is_numeric($get_logged_admin_details["bvn"]) && strlen($get_logged_admin_details["bvn"]) == 11) && (!empty($get_logged_admin_details["nin"]) && is_numeric($get_logged_admin_details["nin"]) && strlen($get_logged_admin_details["nin"]) == 11)){
			$verification_type = 1;
			$bvn_nin_monnify_account_creation = '"bvn" => $get_logged_admin_details["bvn"], "nin" => $get_logged_admin_details["nin"]';
			$bvn_nin_payvessel_account_creation = '"bvn" => $get_logged_admin_details["bvn"]';
		}else{
			if((!empty($get_logged_admin_details["bvn"]) && is_numeric($get_logged_admin_details["bvn"]) && strlen($get_logged_admin_details["bvn"]) == 11)){
				$verification_type = 1;
				$bvn_nin_monnify_account_creation = '"bvn" => $get_logged_admin_details["bvn"]';
				$bvn_nin_payvessel_account_creation = '"bvn" => $get_logged_admin_details["bvn"]';
			}else{
				if((!empty($get_logged_admin_details["nin"]) && is_numeric($get_logged_admin_details["nin"]) && strlen($get_logged_admin_details["nin"]) == 11)){
					$verification_type = 2;
					$bvn_nin_monnify_account_creation = '"nin" => $get_logged_admin_details["nin"]';
				}
			}
		}
		
		$registered_virtual_bank_arr = array();
		$virtual_bank_code_arr = array("232", "035", "50515", "120001");
		if(is_array(getVendorVirtualBank()) == true){
			foreach(getVendorVirtualBank() as $bank_json){
				$bank_json = json_decode($bank_json, true);
				array_push($registered_virtual_bank_arr, $bank_json["bank_code"]);
			}
		}
		if((getVendorVirtualBank() == false) || ((is_array(getVendorVirtualBank()) == true) && (!empty(array_diff($virtual_bank_code_arr, $registered_virtual_bank_arr))))){
		//Monnify
		$get_monnify_access_token = json_decode(getVendorMonnifyAccessToken(), true);
		if($get_monnify_access_token["status"] == "success"){

			//Check If Monnify Virtual Account Exists
			$admin_monnify_account_reference = md5($_SERVER["HTTP_HOST"]."-".$get_logged_admin_details["id"]."-".$get_logged_admin_details["email"]);
			$get_monnify_reserve_account = json_decode(makeMonnifyRequest("get", $get_monnify_access_token["token"], "api/v2/bank-transfer/reserved-accounts/".$admin_monnify_account_reference, ""), true);
			if($get_monnify_reserve_account["status"] == "success"){
				$monnify_reserve_account_response = json_decode($get_monnify_reserve_account["json_result"], true);
				foreach($monnify_reserve_account_response["responseBody"]["accounts"] as $monnify_accounts_json){
					if(in_array($monnify_accounts_json["bankCode"], array("232", "035", "50515"))){
						
                        addVendorVirtualBank($admin_monnify_account_reference, $monnify_accounts_json["bankCode"], $monnify_accounts_json["bankName"], $monnify_accounts_json["accountNumber"], $monnify_reserve_account_response["responseBody"]["accountName"]);
					}
				}
			}else{
				$select_monnify_gateway_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='monnify' LIMIT 1"));
				$monnify_create_reserve_account_array = array("accountReference" => $admin_monnify_account_reference, "accountName" => $get_logged_admin_details["firstname"]." ".$get_logged_admin_details["lastname"]." ".$get_logged_admin_details["othername"], "currencyCode" => "NGN", "contractCode" => $select_monnify_gateway_details["encrypt_key"], "customerEmail" => $get_logged_admin_details["email"], $bvn_nin_monnify_account_creation, "getAllAvailableBanks" => false, "preferredBanks" => ["232", "035", "50515", "058"]);
				makeMonnifyRequest("post", $get_monnify_access_token["token"], "api/v2/bank-transfer/reserved-accounts", $monnify_create_reserve_account_array);
				//$virtual_account_vaccount_err .= '<span class="color-4">Virtual Account Created Successfully</span>';
			}
		}else{
			if($get_monnify_access_token["status"] == "failed"){
				//$virtual_account_vaccount_err .= '<span class="color-4">'.$get_monnify_access_token["message"].'</span>';
			}
		}
		
		//Payvessel
		if((!empty($get_logged_admin_details["bvn"]) && is_numeric($get_logged_admin_details["bvn"]) && strlen($get_logged_admin_details["bvn"]) == 11)){
		$get_payvessel_access_token = json_decode(getVendorPayvesselAccessToken(), true);
		if($get_payvessel_access_token["status"] == "success"){
			$select_payvessel_gateway_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='payvessel' LIMIT 1"));
			$admin_payvessel_account_reference = str_replace([".","-",":"], "", $_SERVER["HTTP_HOST"])."-".$get_logged_admin_details["id"]."-".$get_logged_admin_details["email"];
			$payvessel_create_reserve_account_array = array("email" => $admin_payvessel_account_reference, "name" => $get_logged_admin_details["firstname"]." ".$get_logged_admin_details["lastname"], "phoneNumber" => $get_logged_admin_details["phone_number"], $bvn_nin_payvessel_account_creation, "businessid" => $select_payvessel_gateway_details["encrypt_key"], "bankcode" => ["101", "120001"], "account_type" => "STATIC");
			$get_payvessel_reserve_account = json_decode(makePayvesselRequest("post", $get_payvessel_access_token["token"], "api/external/request/customerReservedAccount/", $payvessel_create_reserve_account_array), true);
			
			if($get_payvessel_reserve_account["status"] == "success"){
				$payvessel_reserve_account_response = json_decode($get_payvessel_reserve_account["json_result"], true);
				
				foreach($payvessel_reserve_account_response["banks"] as $payvessel_accounts_json){
						
					addVendorVirtualBank($payvessel_accounts_json["trackingReference"], $payvessel_accounts_json["bankCode"], $payvessel_accounts_json["bankName"], $payvessel_accounts_json["accountNumber"], $payvessel_accounts_json["accountName"]);
				}
				//$virtual_account_vaccount_err .= '<span class="color-4">Virtual Account Created Successfully</span>';
			}
			
			if($payvessel_reserve_account_response["status"] == "failed"){
				//$virtual_account_vaccount_err .= '<span class="color-4">'.$get_payvessel_access_token["message"].'</span>';
			}
		}else{
			if($get_payvessel_access_token["status"] == "failed"){
				//$virtual_account_vaccount_err .= '<span class="color-4">'.$get_payvessel_access_token["message"].'</span>';
			}
		}
		}
		}else{
			foreach(getVendorVirtualBank() as $monnify_accounts_json){
				$monnify_accounts_json = json_decode($monnify_accounts_json, true);
				if(in_array($monnify_accounts_json["bank_code"], array("232", "035", "50515", "058", "101", "120001"))){
					
				}
			}
		}
	}else{
		if(empty($get_logged_admin_details["bank_code"])){
			//$virtual_account_vaccount_err .= '<span class="color-4">Incomplete Bank Details, Update Your Bank Details In Account Settings</span><br/>';
		}else{
			if(!is_numeric($get_logged_admin_details["bank_code"])){
				//$virtual_account_vaccount_err .= '<span class="color-4">Non-numeric Bank Code</span><br/>';
			}else{
				if(empty($get_logged_admin_details["bvn"])){
					//$virtual_account_vaccount_err .= '<span class="color-4">Update BVN if neccessary</span><br/>';
				}else{
					if(!is_numeric($get_logged_admin_details["bvn"])){
						//$virtual_account_vaccount_err .= '<span class="color-4">Non-numeric BVN</span><br/>';
					}else{
						if(strlen($get_logged_admin_details["bvn"]) !== 11){
							//$virtual_account_vaccount_err .= '<span class="color-4">BVN must be 11 digit long</span><br/>';
						}else{
							if(empty($get_logged_admin_details["nin"])){
								//$virtual_account_vaccount_err .= '<span class="color-4">Update NIN if neccessary</span><br/>';
							}else{
								if(!is_numeric($get_logged_admin_details["nin"])){
									//$virtual_account_vaccount_err .= '<span class="color-4">Non-numeric NIN</span><br/>';
								}else{
									if(strlen($get_logged_admin_details["nin"]) !== 11){
										//$virtual_account_vaccount_err .= '<span class="color-4">NIN must be 11 digit long</span>';
									}
								}
							}
						}
					}
				}
			}
		}
		
	}
?>
<!DOCTYPE html>
<head>
    <title>Admin Dashboard | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_super_admin_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
    
      <!-- Vendor CSS Files -->
  <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets-2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets-2/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets-2/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets-2/css/style.css" rel="stylesheet">

</head>
<body>
    <?php include("../func/bc-admin-header.php"); ?>
    
  	<div class="pagetitle">
      <h1>DASHBOARD</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        
        <!-- Row -->
        <div class="row">
          <!-- Col -->
          <div class="col-6 col-lg-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $get_logged_admin_details["email"]; ?> <span>| Account Type</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6><?php echo accountStatus($get_logged_admin_details["status"]); ?></h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
          
           <!-- Col -->
          <div class="col-6 col-lg-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title">Balance <span>| Today</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6>₦<?php echo toDecimal($get_logged_admin_details["balance"], "2"); ?></h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
          
           <!-- Col -->
          <div class="col-6 col-lg-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title">Deposit <span>| Total</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6>₦
                        <?php
                                $get_all_admin_credit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' && (type_alternative LIKE '%credit%' OR type_alternative LIKE '%received%' OR type_alternative LIKE '%commission%')");
                                if(mysqli_num_rows($get_all_admin_credit_transaction_details) >= 1){
                                    while($transaction_record = mysqli_fetch_assoc($get_all_admin_credit_transaction_details)){
                                        $all_admin_credit_transaction += $transaction_record["discounted_amount"];
                                    }
                                    echo toDecimal($all_admin_credit_transaction, 2);
                                }else{
                                    echo toDecimal(0, 2);
                                }
                            ?>
                        </h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
          
           <!-- Col -->
          <div class="col-6 col-lg-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title">Spent <span>| Total</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6>₦
                          <?php
                                $get_all_admin_debit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' && (type_alternative NOT LIKE '%credit%' && type_alternative NOT LIKE '%refund%' && type_alternative NOT LIKE '%received%' && type_alternative NOT LIKE '%commission%' && status NOT LIKE '%3%')");
                                if(mysqli_num_rows($get_all_admin_debit_transaction_details) >= 1){
                                    while($transaction_record = mysqli_fetch_assoc($get_all_admin_debit_transaction_details)){
                                        $all_admin_debit_transaction += $transaction_record["discounted_amount"];
                                    }
                                    echo toDecimal($all_admin_debit_transaction, 2);
                                }else{
                                    echo toDecimal(0, 2);
                                }
                            ?>
                        </h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
          
          
           <!-- Col -->
          <div class="col-12 col-lg-12">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title">Manual Deposit <span>| Total</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6>₦
                        <?php
                                $get_all_user_manual_credit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' && (type_alternative LIKE '%credit%' && description LIKE '%credit%' && description LIKE '%admin%')");
                                if(mysqli_num_rows($get_all_user_manual_credit_transaction_details) >= 1){
                                    while($transaction_record = mysqli_fetch_assoc($get_all_user_manual_credit_transaction_details)){
                                        $all_user_manual_credit_transaction += $transaction_record["discounted_amount"];
                                    }
                                    echo toDecimal($all_user_manual_credit_transaction, 2);
                                }else{
                                    echo toDecimal(0, 2);
                                }
                            ?>
                        </h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
        </div>  
          <!-- Row End -->
        
		
        <!-- Row -->
        <div class="row">
          <!-- Col -->
          <div class="col-12 col-lg-12">
            <div class="card info-card sales-card p-3 align-items-center">
              <div class="card-body col-12 align-items-center">
                <h5 class="card-title">
                  BILL PAYMENT
                </h5>
              	<form method="post" action="">
              		<select style="text-align: center;" id="" name="bill-id" onchange="" class="form-control mb-1" required/>
              			<option value="" default hidden selected>Choose Bill</option>
              			<?php
              				$get_active_billing_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_billings WHERE date >= '".$get_logged_admin_details["reg_date"]."' ORDER BY date DESC");
              				
              				if(mysqli_num_rows($get_active_billing_details) >= 1){
              					while($active_billing = mysqli_fetch_assoc($get_active_billing_details)){
              						$get_paid_bill_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_paid_bills WHERE vendor_id='".$get_logged_admin_details["id"]."' && bill_id='".$active_billing["id"]."'");
              						if(mysqli_num_rows($get_paid_bill_details) == 0){
              							echo '<option value="'.$active_billing["id"].'">'.$active_billing["bill_type"].' @ N'.toDecimal($active_billing["amount"], 2).' (Starts: '.formDateWithoutTime($active_billing["starting_date"]).', Ends: '.formDateWithoutTime($active_billing["ending_date"]).')</option>';
              						}
              					}
              				}
              			?>
              		</select><br/>
              		<button id="" name="pay-bill" type="submit" style="admin-select: none;" class="btn btn-success col-12" >
              			Pay Bill
              		</button>
              </form>
          </div>
        </div>
      </div>
    <!-- Col End -->
    </div>  
    <!-- Row End -->
    
    <!-- Row -->
    <div class="col-12">
      <!-- Col -->
      <div class="col-12 col-lg-12">
        <div class="card info-card sales-card p-3 align-items-center">
              <div class="card-body col-12 align-items-center">
                	<h5 class="card-title">
                    Auto Funding
                  </h5>
                		<?php
                		  foreach (getUserVirtualBank() as $bank_accounts_json) {
                  			$bank_accounts_json = json_decode($bank_accounts_json, true);
                  			if (in_array($bank_accounts_json["bank_code"], array("110072", "232"))) {
                  				$virtual_account_vaccount_err .=
                  					'<div style="" class="bg-white">
                  					    <div style="" class="bg-success d-inline-block rounded-2 rounded-bottom-0 col-12 px-2 py-2 mt-0">
                      					 <h5 class="text-white">
                                    ' . strtoupper($bank_accounts_json["account_name"]) . '
                                  </h5>
                                </div>
                                <div style="" class="bg-light d-flex rounded-2 rounded-top-0 col-12 px-2 py-1 mt-0 justify-content-center justify-content-between">
                          					<div class="row">
                          						<div style="user-select: auto;" class="d-inline-block text-success h5 mt-2">' . strtoupper($bank_accounts_json["bank_name"]) . '</div><br>
                          						<div style="user-select: auto;" class="d-inline-block text-success h3 mt-1">' . $bank_accounts_json["account_number"] . ' <span onclick="copyText(`Account number copied successfully`,`' . $bank_accounts_json["account_number"] . '`);" class="p-1 card-icon rounded-circle"><i title="Copy Account Number" class="bi bi-copy h3 text-success" ></i></span></div>
                        						</div>
                        						<div class="row">
                        						<div style="user-select: auto;" class="col-12 d-inline-block text-success h5 mt-2 text-end">Charges<br/><h2>#50</h2></div>
                        						</div>
                        						
                        				</div>
                        				<div style="user-select: auto;" class="col-12 d-inline-block text-success fw-bold text-end mt-1 text-decoration-underline">View More</div>
                  					</div>';
                  			}
                  		}
                		?><br>
                		<?php
                		  echo $virtual_account_vaccount_err;
                		?>
              </div>
          </div>
      </div>
      <!-- Col End -->
    
    </div>  
      <!-- Row End -->
    
      </div>
    </section>
        <?php include("../func/admin-short-trans.php"); ?>
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>