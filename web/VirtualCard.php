<?php session_start();
    include("../func/bc-config.php");
        
    if(isset($_POST["buy-card"])){
        $purchase_method = "web";
        include_once("func/virtualcard.php");
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        //echo '<script>alert("'.$json_response_decode["status"].': '.$json_response_decode["desc"].'");</script>';
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
?>
<!DOCTYPE html>
<head>
    <title>Virtual Card | <?php echo $get_all_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
    
                
    <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

    <script src="https://merchant.beewave.ng/checkout.min.js"></script> 
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
    <?php include("../func/bc-header.php"); ?>  

	<div class="pagetitle">
      <h1>VIRTUAL CARD</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Virtual Card</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">

    
    <div class="card info-card px-5 py-5">
            <form method="post" action="">
                <div style="text-align: center; user-select: auto;" class="container">
                    <img alt="Mastercard" id="mastercard-lg" product-status="enabled" src="/asset/mastercard.png" onclick="tickVirtualCardRechargeCarrier('mastercard'); resetVirtualCardQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="Visa" id="visa-lg" product-status="enabled" src="/asset/visa.png" onclick="tickVirtualCardRechargeCarrier('visa'); resetVirtualCardQuantity();" class="col-2 rounded-5 border m-1 "/>
                    <img alt="Verve" id="verve-lg" product-status="enabled" src="/asset/verve.png" onclick="tickVirtualCardRechargeCarrier('verve'); resetVirtualCardQuantity();" class="col-2 rounded-5 border m-1 "/>
                </div><br/>
                <input id="isprovider" name="isp" type="text" placeholder="Isp" hidden readonly required/>
                <select style="text-align: center;" id="internet-data-type" name="type" onchange="tickVirtualCardRechargeCarrier(); resetVirtualCardQuantity();" class="form-control mb-1" required/>
                    <option value="" default hidden selected>Card Type</option>
                    <option value="nairacard">Naira Card</option>
                    <option value="dollarcard">Dollar Card</option>
                </select><br/>
                <select style="text-align: center;" id="product-amount" name="quantity" onchange="tickVirtualCardRechargeCarrier(); tickVirtualCardRechargeCarrier();" class="form-control mb-1" required/>
                    <option product-category="" value="" default hidden selected>Card Quantity</option>
                    <?php
                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                        if($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true){
                            $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
                            $product_name_array = array("mastercard", "visa", "verve");
                            $data_type_table_name_arrays = array("nairacard"=>"sas_nairacard_status", "dollarcard"=>"sas_dollarcard_status", "dd-data"=>"sas_dd_data_status");
                            
                            //MASTERCARD NAIRACARD
                            $get_mastercard_nairacard_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["nairacard"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."'"));
                            $get_api_enabled_nairacard_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_mastercard_nairacard_status_details["api_id"]."' && api_type='nairacard' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_nairacard_lists) == 1){
                                $get_api_enabled_nairacard_lists = mysqli_fetch_array($get_api_enabled_nairacard_lists);
                                $product_table_mastercard_nairacard = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."' LIMIT 1"));
                                if($product_table_mastercard_nairacard["status"] == 1){
                                    $product_discount_table_mastercard_nairacard = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_nairacard_lists["id"]."' && product_id='".$product_table_mastercard_nairacard["id"]."'");
                                    if(mysqli_num_rows($product_discount_table_mastercard_nairacard) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_mastercard_nairacard)){
                                            echo '<option product-category="mastercard-nairacard" value="'.$product_details["val_1"].'" hidden>MASTERCARD NAIRACARD '.$product_details["val_1"].' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    }
                                }
                            }

                            //MASTERCARD DOLLARCARD
                            $get_mastercard_dollarcard_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["dollarcard"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."'"));
                            $get_api_enabled_dollarcard_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_mastercard_dollarcard_status_details["api_id"]."' && api_type='dollarcard' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_dollarcard_lists) == 1){
                                $get_api_enabled_dollarcard_lists = mysqli_fetch_array($get_api_enabled_dollarcard_lists);
                                $product_table_mastercard_dollarcard = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[0]."' LIMIT 1"));
                                if($product_table_mastercard_dollarcard["status"] == 1){
                                    $product_discount_table_mastercard_dollarcard = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_dollarcard_lists["id"]."' && product_id='".$product_table_mastercard_dollarcard["id"]."'");
                                    if(mysqli_num_rows($product_discount_table_mastercard_dollarcard) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_mastercard_dollarcard)){
                                            echo '<option product-category="mastercard-dollarcard" value="'.$product_details["val_1"].'" hidden>MASTERCARD DOLLARCARD N'.$product_details["val_1"].' @ N'.$product_details["val_2"].'</option>';
                                        }
                                    }
                                }
                            }

                            //VISA NAIRACARD
                            $get_visa_nairacard_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["nairacard"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."'"));
                            $get_api_enabled_nairacard_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_visa_nairacard_status_details["api_id"]."' && api_type='nairacard' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_nairacard_lists) == 1){
                                $get_api_enabled_nairacard_lists = mysqli_fetch_array($get_api_enabled_nairacard_lists);
                                $product_table_visa_nairacard = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."' LIMIT 1"));
                                if($product_table_visa_nairacard["status"] == 1){
                                    $product_discount_table_visa_nairacard = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_nairacard_lists["id"]."' && product_id='".$product_table_visa_nairacard["id"]."'");
                                    if(mysqli_num_rows($product_discount_table_visa_nairacard) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_visa_nairacard)){
                                            echo '<option product-category="visa-nairacard" value="'.$product_details["val_1"].'" hidden>VISA NAIRACARD '.$product_details["val_1"].' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    }
                                }
                            }

                            //VISA DOLLARCARD
                            $get_visa_dollarcard_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["dollarcard"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."'"));
                            $get_api_enabled_dollarcard_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_visa_dollarcard_status_details["api_id"]."' && api_type='dollarcard' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_dollarcard_lists) == 1){
                                $get_api_enabled_dollarcard_lists = mysqli_fetch_array($get_api_enabled_dollarcard_lists);
                                $product_table_visa_dollarcard = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[1]."' LIMIT 1"));
                                if($product_table_visa_dollarcard["status"] == 1){
                                    $product_discount_table_visa_dollarcard = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_dollarcard_lists["id"]."' && product_id='".$product_table_visa_dollarcard["id"]."'");
                                    if(mysqli_num_rows($product_discount_table_visa_dollarcard) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_visa_dollarcard)){
                                            echo '<option product-category="visa-dollarcard" value="'.$product_details["val_1"].'" hidden>VISA DOLLARCARD N'.$product_details["val_1"].' @ N'.$product_details["val_2"].'</option>';
                                        }
                                    }
                                }
                            }

                            //VERVE NAIRACARD
                            $get_verve_nairacard_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["nairacard"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."'"));
                            $get_api_enabled_nairacard_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_verve_nairacard_status_details["api_id"]."' && api_type='nairacard' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_nairacard_lists) == 1){
                                $get_api_enabled_nairacard_lists = mysqli_fetch_array($get_api_enabled_nairacard_lists);
                                $product_table_verve_nairacard = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."' LIMIT 1"));
                                if($product_table_verve_nairacard["status"] == 1){
                                    $product_discount_table_verve_nairacard = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_nairacard_lists["id"]."' && product_id='".$product_table_verve_nairacard["id"]."'");
                                    if(mysqli_num_rows($product_discount_table_verve_nairacard) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_verve_nairacard)){
                                            echo '<option product-category="verve-nairacard" value="'.$product_details["val_1"].'" hidden>VERVE NAIRACARD '.$product_details["val_1"].' @ N'.$product_details["val_2"].' (Validity '.$product_details["val_3"].'days)</option>';
                                        }
                                    }
                                }
                            }

                            //VERVE DOLLARCARD
                            $get_verve_dollarcard_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$data_type_table_name_arrays["dollarcard"]." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."'"));
                            $get_api_enabled_dollarcard_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_verve_dollarcard_status_details["api_id"]."' && api_type='dollarcard' && status='1' LIMIT 1");
                            if(mysqli_num_rows($get_api_enabled_dollarcard_lists) == 1){
                                $get_api_enabled_dollarcard_lists = mysqli_fetch_array($get_api_enabled_dollarcard_lists);
                                $product_table_verve_dollarcard = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$product_name_array[2]."' LIMIT 1"));
                                if($product_table_verve_dollarcard["status"] == 1){
                                    $product_discount_table_verve_dollarcard = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$get_api_enabled_dollarcard_lists["id"]."' && product_id='".$product_table_verve_dollarcard["id"]."'");
                                    if(mysqli_num_rows($product_discount_table_verve_dollarcard) > 0){
                                        while($product_details = mysqli_fetch_assoc($product_discount_table_verve_dollarcard)){
                                            echo '<option product-category="verve-dollarcard" value="'.$product_details["val_1"].'" hidden>VERVE DOLLARCARD N'.$product_details["val_1"].' @ N'.$product_details["val_2"].'</option>';
                                        }
                                    }
                                }
                            }
                        }
                    ?>
                </select><br/>
                <input style="text-align: center;" id="quantity" name="qty-number" onkeyup="tickVirtualCardRechargeCarrier();" type="text" value="1" placeholder="Quantity e.g 1" pattern="[0-9]{1,}" title="Charater must be atleast 1 digit" class="form-control mb-1" hidden readonly required/>
                <input hidden style="text-align: center;" id="" name="firstname" onkeyup="" type="text" value="<?php echo $get_logged_user_details['firstname']; ?>" placeholder="Firstame" class="form-control mb-1" readonly />
                <input hidden style="text-align: center;" id="" name="lastname" onkeyup="" type="text" value="<?php echo $get_logged_user_details['lastname']; ?>" placeholder="Lastname" class="form-control mb-1" readonly />
                <input hidden style="text-align: center;" id="" name="email" onkeyup="" type="text" value="<?php echo $get_logged_user_details['email']; ?>" placeholder="Email" class="form-control mb-1" readonly />
                <input hidden style="text-align: center;" id="" name="phone" onkeyup="" type="text" value="<?php echo $get_logged_user_details['phone_number']; ?>" placeholder="Phone nuumber" class="form-control mb-1" readonly />
                <input style="text-align: center;" id="" name="postal-code" onkeyup="" type="text" value="" placeholder="Postal code" class="form-control mb-1" /><br/>
                <input hidden style="text-align: center;" id="" name="address" onkeyup="" type="text" value="<?php echo $get_logged_user_details['home_address']; ?>" placeholder="Address" class="form-control mb-1" readonly />
                <input style="text-align: center;" id="" name="state" onkeyup="" type="text" value="" placeholder="State" class="form-control mb-1" /><br/>
                <select style="text-align: center;" id="" name="country" onchange="" class="form-control mb-4" required/>
                    <option value="" default hidden selected>Country</option>
                    <option value="nigeria" >NIGERIA</option>
                </select>
                <select style="text-align: center;" id="" name="kyc-mode" onchange="" class="form-control mb-4" required/>
                    <option value="" default hidden selected>KYC Method</option>
                    <option value="nigerian_bvn" >NIGERIAN BVN</option>
                    <option value="nigerian_nin" >NIGERIAN NIN</option>
                </select>
                <input style="text-align: center;" id="" name="kyc-id" onkeyup="" type="text" value="" placeholder="KYC ID eg NIN, BVN" class="form-control mb-1" /><br/>
                <div style="text-align: center;" class="container">
                  <label class="h6 mb-1">Selfie Capture</label>
                  <input type="file" role="uploadcare-uploader" name="selfie" id="selfie-image" data-crop="1:1" data-images-only data-source="camera" class="form-control mb-1" />
                  <img src="" id="preview" accept="image/*" width="150" height="0" style="object-position: cover;" class="rounded-5 my-1" />
                  <input type="hidden" style="text-align: center;" id="selfie-dataurl" name="selfie-dataurl" type="text" value="" placeholder="Selfie Image" class="form-control mb-1" name="selfie-url" required /><br/>
                </div>
                <script>
                  const fileInput = document.getElementById('selfie-image');
                  const preview = document.getElementById('preview');
                  const selfieDataurl = document.getElementById('selfie-dataurl');
                
                  fileInput.addEventListener('change', function () {
                    const file = this.files[0];
                    if (file) {
                      const reader = new FileReader();
                
                      reader.onload = function (e) {
                        const dataURL = e.target.result;
                        preview.style.height = "150px";
                        preview.src = dataURL;
                        selfieDataurl.value = dataURL;
                        // If you want to store or submit it:
                        console.log(dataURL); // data:image/jpeg;base64,...
                      };
                
                      reader.readAsDataURL(file);
                    }
                  });
                </script>

                <button id="proceedBtn" name="buy-card" type="button" style="pointer-events: none; user-select: auto;" class="btn btn-success mb-1 col-12" >
                    GENERATE CARD
                </button><br>
                <div style="text-align: center;" class="col-8">
                    <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                </div>
            </form>
        </div>
      </div>
    </section>

        <?php include("../func/short-trans.php"); ?>
    <?php include("../func/bc-footer.php"); ?>
    
</body>
</html>