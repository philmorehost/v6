<?php session_start();
    include("../func/bc-admin-config.php");
        
    if(isset($_POST["update-key"])){
        $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
        $apikey = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-key"])));
        $apistatus = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-status"])));
        
        if(!empty($api_id) && is_numeric($api_id)){
            if(!empty($apikey)){
                if(is_numeric($apistatus) && in_array($apistatus, array("0", "1"))){
                    $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id' && api_type='dd-data'");
                    if(mysqli_num_rows($select_api_lists) == 1){
                        mysqli_query($connection_server, "UPDATE sas_apis SET api_key='$apikey', status='$apistatus' WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id' && api_type='dd-data'");
                        //APIkey Updated Successfully
                        $json_response_array = array("desc" => "APIkey Updated Successfully");
                        $json_response_encode = json_encode($json_response_array,true);
                    }else{
                        //API Doesnt Exists
                        $json_response_array = array("desc" => "API Doesnt Exists");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    //Invalid API Status
                    $json_response_array = array("desc" => "Invalid API Status");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                //Apikey Field Empty
                $json_response_array = array("desc" => "Apikey Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }
        }else{
            //Invalid Apikey Website
            $json_response_array = array("desc" => "Invalid Apikey Website");
            $json_response_encode = json_encode($json_response_array,true);
        }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

    if(isset($_POST["install-product"])){
        $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
        $item_status = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["item-status"])));
        $product_name = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["product-name"]))));
        $products_array = array("mtn", "airtel", "glo", "9mobile");
        $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
        $array_mtn_product_variety = array("1gb_weekly_plan_plus_free_1gb_for_youtube_plus_100mb_for_youtube_music_plus_5mins","1gb_plus_1gb_youtube_night_plus_1hr_100mb_youtube_daily_plus_10mins","1.5gb_plus_1.4gb_youtube_night_plus_1hr_100mb_youtube_daily_plus_10mins","1.8gb_plus_5mins","4.25gb_plus_10mins","5gb_weekly","7gb_weekly","5.5gb_monthly","8gb_plus_2gb_youtube_night_300mb_youtube_music_plus_20mins","11gb_plus_25mins","15gb_plus_25mins","20gb_monthly","25gb_monthly","32gb_30days_plus_40mins","75gb_30days_plus_40mins","120gb_30days_plus_80mins","150gb_30days_plus_80mins","400gb_1year");
        $array_airtel_product_variety = array("75mb_1day","100mb_1day","200mb_1day","300mb_1day","500mb_7days","1gb_7days","1.5gb_7days","3.5gb_7days","6gb_7days","10gb_7days","18gb_7days","2gb_30days","3gb_30days","4gb_30days","8gb_30days","10gb_30days","13gb_30days","18gb_30days","25gb_30days","35gb_30days","60gb_30days","100gb_30days","160gb_30days","210gb_30days","300gb_30days","350gb_30days","650gb_30days");
        $array_glo_product_variety = array("150mb_1day_115mb_plus_35mb","350mb_2days_240mb_plus_110mb","3.9gb_30days_1.9gb_plus_2gb","7.5gb_30days_3.5gb_plus_4gb","9.2gb_30days_5.2gb_plus_4gb","10.8gb_30days_6.8gb_plus_4gb","14gb_30days_10gb_plus_4gb","18gb_30days_14gb_plus_4gb","24gb_30days_20gb_plus_4gb","29.5gb_30days_27.5gb_plus_2gb","50gb_30days_46gb_plus_4gb","93gb_30days_86gb_plus_7gb","119gb_30days_109gb_plus_10gb","138gb_30days_126gb_plus_12gb");
        $array_9mobile_product_variety = array("1gb_daily_plus_100mb_social","2gb_3days_plus_100mb_social","7gb_plus_100mb_social_weekly","4.2gb_2gb_all_time_plus_2.2gb_night_30days","6.5gb_2.5gb_all_time_plus_4gb_night_30days","9.5gb_5.5gb_all_time_plus_4gb_night_30days","11gb_7gb_all_time_plus_4gb_night_30days","12gb_30days","18.5gb_15gb_all_time_plus_3.5gb_night_30days","24gb_30days","35gb_30days","50gb_30days","80gb_30days","125gb_30days");
        
        if(!empty($api_id) && is_numeric($api_id)){
            if(!empty($product_name)){
                if(in_array($product_name, $products_array)){
                    if(is_numeric($item_status) && in_array($item_status, array("0", "1"))){
                        $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id' && api_type='dd-data'");
                        $select_dd_data_status_lists = mysqli_query($connection_server, "SELECT * FROM sas_dd_data_status WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_name='$product_name'");
                        if(mysqli_num_rows($select_api_lists) == 1){    
                            if(mysqli_num_rows($select_dd_data_status_lists) == 0){
                                mysqli_query($connection_server, "INSERT INTO sas_dd_data_status (vendor_id, api_id, product_name, status) VALUES ('".$get_logged_admin_details["id"]."', '$api_id', '$product_name', '$item_status')");
                            }else{
                                mysqli_query($connection_server, "UPDATE sas_dd_data_status SET api_id='$api_id', status='$item_status' WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_name='$product_name'");
                            }
                                
                            foreach($account_level_table_name_arrays as $account_level_table_name){ 
                                $select_product_details = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_name='$product_name'");
                                if(mysqli_num_rows($select_product_details) == 1){
                                    $get_product_details = mysqli_fetch_array($select_product_details);
                                    $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id'"));
                                    $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id!='$api_id' && api_type='".$get_selected_api_list["api_type"]."' LIMIT 1");
                                    if(mysqli_num_rows($select_api_list_with_api_type) == 1){
                                        $get_api_list_with_api_type = mysqli_fetch_array($select_api_list_with_api_type);
                                        $select_api_list_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$get_api_list_with_api_type["id"]."' && product_id='".$get_product_details["id"]."'");                    		
                                        if(mysqli_num_rows($select_api_list_product_pricing_table) == 1){
                                            $get_api_list_product_pricing_table = mysqli_fetch_array($select_api_list_product_pricing_table);
                                            $pro_val_1 = $get_api_list_product_pricing_table["val_1"];
                                            $pro_val_2 = $get_api_list_product_pricing_table["val_2"];
                                            $pro_val_3 = $get_api_list_product_pricing_table["val_3"];
                                            $pro_val_4 = $get_api_list_product_pricing_table["val_4"];
                                            $pro_val_5 = $get_api_list_product_pricing_table["val_5"];
                                            $pro_val_6 = $get_api_list_product_pricing_table["val_6"];
                                            $pro_val_7 = $get_api_list_product_pricing_table["val_7"];
                                            $pro_val_8 = $get_api_list_product_pricing_table["val_8"];
                                            $pro_val_9 = $get_api_list_product_pricing_table["val_9"];
                                            $pro_val_10 = $get_api_list_product_pricing_table["val_10"];
                                        }else{
                                            $pro_val_1 = "0";
                                            $pro_val_2 = "0";
                                            $pro_val_3 = "0";
                                            $pro_val_4 = "0";
                                            $pro_val_5 = "0";
                                            $pro_val_6 = "0";
                                            $pro_val_7 = "0";
                                            $pro_val_8 = "0";
                                            $pro_val_9 = "0";
                                            $pro_val_10 = "0";
                                        }
                                    }else{
                                        $pro_val_1 = "0";
                                        $pro_val_2 = "0";
                                        $pro_val_3 = "0";
                                        $pro_val_4 = "0";
                                        $pro_val_5 = "0";
                                        $pro_val_6 = "0";
                                        $pro_val_7 = "0";
                                        $pro_val_8 = "0";
                                        $pro_val_9 = "0";
                                        $pro_val_10 = "0";
                                    }
                                    $select_all_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='dd-data'");
                                    $product_array_string_name = "array_".$product_name."_product_variety";
                                    $product_variety = $$product_array_string_name;
                                    $count_product_variety = count($product_variety);
                                    if($count_product_variety >= 1){
                                        foreach($product_variety as $product_val_1){
                                            $product_val_1 = trim($product_val_1);
                                            $product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='$api_id' && product_id='".$get_product_details["id"]."' && val_1='$product_val_1'");                            
                                            if(mysqli_num_rows($product_pricing_table) == 0){
                                                mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('".$get_logged_admin_details["id"]."', '$api_id', '".$get_product_details["id"]."', '$product_val_1', '$pro_val_2', '$pro_val_3')");
                                            }else{
                                                if(mysqli_num_rows($select_all_api_lists) >= 1){
                                                    while($api_details = mysqli_fetch_assoc($select_all_api_lists)){
                                                        if($api_details["id"] !== $api_id){
                                                            mysqli_query($connection_server, "DELETE FROM $account_level_table_name WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$api_details["id"]."' && product_id='".$get_product_details["id"]."' && val_1='$product_val_1'");
                                                        }else{
                                                            $check_product_pricing_row_exists = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='$api_id' && product_id='".$get_product_details["id"]."' && val_1='$product_val_1'");                         
                                                            if(mysqli_num_rows($check_product_pricing_row_exists) == 0){
                                                                mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('".$get_logged_admin_details["id"]."', '$api_id', '".$get_product_details["id"]."', '$product_val_1', '$pro_val_2', '$pro_val_3')");
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }       
                            //Product Updated Successfully
                            $json_response_array = array("desc" => "Product Updated Successfully");
                            $json_response_encode = json_encode($json_response_array,true);
                        }else{
                            //API Doesnt Exists
                            $json_response_array = array("desc" => "API Doesnt Exists");
                            $json_response_encode = json_encode($json_response_array,true);
                        }
                    }else{
                        //Invalid DIRECT DATA Status
                        $json_response_array = array("desc" => "Invalid dd-data Status");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }else{
                    //Invalid Product Name
                    $json_response_array = array("desc" => "Invalid Product Name");
                    $json_response_encode = json_encode($json_response_array,true);
                }
            }else{
                //Product Name Field Empty
                $json_response_array = array("desc" => "Product Name Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }
        }else{
            //Invalid Apikey Website
            $json_response_array = array("desc" => "Invalid Apikey Website");
            $json_response_encode = json_encode($json_response_array,true);
        }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

    if(isset($_POST["update-price"])){
        $api_id_array = $_POST["api-id"];
        $product_id_array = $_POST["product-id"];
        $product_code_1_array = $_POST["product-code-1"];
        $product_days_array = $_POST["product-days"];
        $smart_price_array = $_POST["smart-price"];
        $agent_price_array = $_POST["agent-price"];
        $api_price_array = $_POST["api-price"];
        $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
        if(count($api_id_array) == count($product_id_array)){
            foreach($api_id_array as $index => $api_id){
                $api_id = $api_id_array[$index];
                $product_id = $product_id_array[$index];
                $product_code_1 = $product_code_1_array[$index];
                $product_days = $product_days_array[$index];
                $smart_price = $smart_price_array[$index];
                $agent_price = $agent_price_array[$index];
                $api_price = $api_price_array[$index];
                $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='$api_id'"));
                $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='".$get_selected_api_list["api_type"]."'");
                if(mysqli_num_rows($select_api_list_with_api_type) > 0){
                    while($refined_api_id = mysqli_fetch_assoc($select_api_list_with_api_type)){
                        $smart_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[0]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");                          
                        if(mysqli_num_rows($smart_product_pricing_table) == 0){
                            mysqli_query($connection_server, "INSERT INTO ".$account_level_table_name_arrays[0]." (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('".$get_logged_admin_details["id"]."', '".$refined_api_id["id"]."', '$product_id', '$product_code_1', '$smart_price', '$product_days')");
                        }else{
                            mysqli_query($connection_server, "UPDATE ".$account_level_table_name_arrays[0]." SET vendor_id='".$get_logged_admin_details["id"]."', api_id='".$refined_api_id["id"]."', product_id='$product_id', val_1='$product_code_1', val_2='$smart_price', val_3='$product_days' WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");
                        }
                        
                        $agent_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[1]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");                          
                        if(mysqli_num_rows($agent_product_pricing_table) == 0){
                            mysqli_query($connection_server, "INSERT INTO ".$account_level_table_name_arrays[1]." (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('".$get_logged_admin_details["id"]."', '".$refined_api_id["id"]."', '$product_id', '$product_code_1', '$agent_price', '$product_days')");
                        }else{
                            mysqli_query($connection_server, "UPDATE ".$account_level_table_name_arrays[1]." SET vendor_id='".$get_logged_admin_details["id"]."', api_id='".$refined_api_id["id"]."', product_id='$product_id', val_1='$product_code_1', val_2='$agent_price', val_3='$product_days' WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");
                        }
                        
                        $api_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[2]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");                            
                        if(mysqli_num_rows($api_product_pricing_table) == 0){
                            mysqli_query($connection_server, "INSERT INTO ".$account_level_table_name_arrays[2]." (vendor_id, api_id, product_id, val_1, val_2, val_3) VALUES ('".$get_logged_admin_details["id"]."', '".$refined_api_id["id"]."', '$product_id', '$product_code_1', '$api_price', '$product_days')");
                        }else{
                            mysqli_query($connection_server, "UPDATE ".$account_level_table_name_arrays[2]." SET vendor_id='".$get_logged_admin_details["id"]."', api_id='".$refined_api_id["id"]."', product_id='$product_id', val_1='$product_code_1', val_2='$api_price', val_3='$product_days' WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$refined_api_id["id"]."' && product_id='$product_id' && val_1='$product_code_1'");
                        }
                    }
                }
            }
            //Price Updated Successfully
            $json_response_array = array("desc" => "Price Updated Successfully");
            $json_response_encode = json_encode($json_response_array,true);
        }else{
            //Product Connection Error
            $json_response_array = array("desc" => "Product Connection Error");
            $json_response_encode = json_encode($json_response_array,true);
        }
        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }
    
    $csv_price_level_array = [];
    $csv_price_level_array[] = "product_name,smart_level,agent_level,api_level,days";
    
?>
<!DOCTYPE html>
<head>
    <title>Direct Data API | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
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
      <h1>DIRECT DATA API</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Direct Data</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
        
            <span style="user-select: auto;" class="h4 fw-bold">API SETTING</span><br>
            <form method="post" action="">
                <select style="text-align: center;" id="" name="api-id" onchange="getWebApikey(this);" class="form-control mb-1" required/>
                    <?php
                        //All DIRECT DATA API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='dd-data'");
                        if(mysqli_num_rows($get_api_lists) >= 1){
                            echo '<option value="" default hidden selected>Choose API</option>';
                            while($api_details = mysqli_fetch_assoc($get_api_lists)){
                                if(empty(trim($api_details["api_key"]))){
                                    $apikey_status = "( Empty Key )";
                                }else{
                                    $apikey_status = "";
                                }
                                
                                echo '<option value="'.$api_details["id"].'" api-key="'.$api_details["api_key"].'" api-status="'.$api_details["status"].'">'.strtoupper($api_details["api_base_url"]).' '.$apikey_status.'</option>';
                            }
                        }else{
                            echo '<option value="" default hidden selected>No API</option>';
                        }
                    ?>
                </select><br/>
                <select style="text-align: center;" id="web-apikey-status" name="api-status" onchange="" class="form-control mb-1" required/>
                    <option value="" default hidden selected>Choose API Status</option>
                    <option value="1" >Enabled</option>
                    <option value="0" >Disabled</option>
                </select><br/>
                <input style="text-align: center;" id="web-apikey-input" name="api-key" onkeyup="" type="text" value="" placeholder="Api Key" class="form-control mb-1" required/><br/>
                <button name="update-key" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    UPDATE KEY
                </button><br>
                <div style="text-align: center;" class="container">
                    <span id="product-status-span" class="h5" style="user-select: auto;"></span>
                </div><br/>
            </form>
          </div>
        </div>
        
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">PRODUCT INSTALLATION</span><br>
            <div style="text-align: center; user-select: auto;" class="container">
                <img alt="Airtel" id="airtel-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/airtel.png" onclick="tickProduct(this, 'airtel', 'api-product-name', 'install-product', 'png');" class="col-2 rounded-5 border m-1  "/>
                <img alt="MTN" id="mtn-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/mtn.png" onclick="tickProduct(this, 'mtn', 'api-product-name', 'install-product', 'png');" class="col-2 rounded-5 border m-1 "/>
                <img alt="Glo" id="glo-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/glo.png" onclick="tickProduct(this, 'glo', 'api-product-name', 'install-product', 'png');" class="col-2 rounded-5 border m-1 "/>
                <img alt="9mobile" id="9mobile-lg" product-name-array="mtn,airtel,glo,9mobile" src="/asset/9mobile.png" onclick="tickProduct(this, '9mobile', 'api-product-name', 'install-product', 'png');" class="col-2 rounded-5 border m-1 "/>
            </div><br/>
            <form method="post" action="">
                <input id="api-product-name" name="product-name" type="text" placeholder="Product Name" hidden readonly required/>
                <select style="text-align: center;" id="" name="api-id" onchange="" class="form-control mb-1" required/>
                    <?php
                        //All DIRECT DATA API
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_type='dd-data'");
                        if(mysqli_num_rows($get_api_lists) >= 1){
                            echo '<option value="" default hidden selected>Choose API</option>';
                            while($api_details = mysqli_fetch_assoc($get_api_lists)){
                                if(empty(trim($api_details["api_key"]))){
                                    $apikey_status = "( Empty Key )";
                                }else{
                                    $apikey_status = "";
                                }
                                
                                echo '<option value="'.$api_details["id"].'">'.strtoupper($api_details["api_base_url"]).' '.$apikey_status.'</option>';
                            }
                        }else{
                            echo '<option value="" default hidden selected>No API</option>';
                        }
                    ?>
                </select><br/>
                <div style="text-align: center;" class="container">
                    <span id="user-status-span" class="h5" style="user-select: auto;">DIRECT DATA STATUS</span>
                </div><br/>
                <select style="text-align: center;" id="" name="item-status" onchange="" class="form-control mb-1" required/>
                    <option value="" default hidden selected>Choose DIRECT DATA Status</option>
                    <option value="1" >Enabled</option>
                    <option value="0" >Disabled</option>
                </select><br/>
                <button id="install-product" name="install-product" type="submit" style="pointer-events: none; user-select: auto;" class="btn btn-primary col-12 mb-1" >
                    INSTALL PRODUCT
                </button><br>
            </form>
          </div>
        </div>
        
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">INSTALLED DIRECT DATA STATUS</span><br>
            <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
              <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                  <thead class="thead-dark">
                    <tr>
                        <th>Product Name</th><th>API Route</th><th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                        $item_name_array = array("mtn", "airtel", "glo", "9mobile");
                        foreach($item_name_array as $products){
                            $items_statement .= "product_name='$products' OR ";
                        }
                        $items_statement = "(".trim(rtrim($items_statement," OR ")).")";
                        $select_item_lists = mysqli_query($connection_server, "SELECT * FROM sas_dd_data_status WHERE vendor_id='".$get_logged_admin_details["id"]."' && $items_statement");
                        if(mysqli_num_rows($select_item_lists) >= 1){
                            while($list_details = mysqli_fetch_assoc($select_item_lists)){
                                $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$list_details["api_id"]."' && api_type='dd-data'");
                                if(mysqli_num_rows($select_api_lists) == 1){
                                    $api_details = mysqli_fetch_array($select_api_lists);
                                    $api_route_web = strtoupper($api_details["api_base_url"]);
                                }else{
                                    if(mysqli_num_rows($select_api_lists) == 0){
                                        $api_route_web = "Invalid API Website";
                                    }else{
                                        $api_route_web = "Duplicated API Website";
                                    }
                                }
                                if(strtolower(itemStatus($list_details["status"])) == "enabled"){
                                    $item_status = '<span style="color: green;">'.itemStatus($list_details["status"]).'</span>';
                                }else{
                                    $item_status = '<span style="color: grey;">'.itemStatus($list_details["status"]).'</span>';
                                }
                                
                                echo 
                                '<tr>
                                    <td>'.strtoupper(str_replace(["-","_"], " ", $list_details["product_name"])).'</td><td>'.$api_route_web.'</td><td>'.$item_status.'</td>
                                </tr>';
                            }
                        }
                    ?>
                  </tbody>
                </table>
            </div>
          </div>
        </div><br/>
        
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">DIRECT DATA DISCOUNT</span><br>
            <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
              <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                <thead class="thead-dark">
                  <tr>
                      <th>Digit</th><th>Mode</th><th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                    <input style="text-align: center;" id="price-upgrade-input" name="" onkeyup="" type="text" value="" placeholder="Amount/Percent" class="form-control mb-1" required/>
                  </td>
                  <td>
                    <select style="text-align: center;" id="price-upgrade-type" name="" onchange="" class="form-control mb-1" required/>
                        <option value="" default hidden selected>Choose Update Type</option>
                        <option value="amount+" >Amount Increase</option>
                        <option value="amount-" >Amount Decrease</option>
                        <option value="percent+" >Percentage Increase</option>
                        <option value="percent-" >Percentage Decrease</option>
                    </select>
                  </td>
                  <td>
                    <button onclick="upgradeePriceDiscount();" type="button" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                      SAVE
                    </button>
                  </td>
                  </tr>
                </tbody>
              </table>
                <form method="post" action="">
                  <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
                    <thead class="thead-dark">
                      <tr>
                          <th>Product Name</th><th>Smart Earner</th><th>Agent Vendor</th><th>API Vendor</th><th>Days</th>
                      </tr>
                    </thead>
                    <tbody>
                        
                        <?php
                            $item_name_array_2 = array("mtn", "airtel", "glo", "9mobile");
                            foreach($item_name_array_2 as $products){
                                $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_dd_data_status WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_name='$products'"));
                                $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_admin_details["id"]."' && id='".$get_item_status_details["api_id"]."' && api_type='dd-data'");
                                $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                                $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_admin_details["id"]."' && product_name='$products' LIMIT 1"));
                                $product_smart_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[1]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$get_item_status_details["api_id"]."' && product_id='".$product_table["id"]."'");                         
                                $product_agent_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[2]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$get_item_status_details["api_id"]."' && product_id='".$product_table["id"]."'");                         
                                $product_api_table = mysqli_query($connection_server, "SELECT * FROM ".$account_level_table_name_arrays[3]." WHERE vendor_id='".$get_logged_admin_details["id"]."' && api_id='".$get_item_status_details["api_id"]."' && product_id='".$product_table["id"]."'");                           
                                
                                if((mysqli_num_rows($get_api_lists) == 1) && (mysqli_num_rows($product_smart_table) > 0) && (mysqli_num_rows($product_agent_table) > 0) && (mysqli_num_rows($product_api_table) > 0)){
                                    while(($product_smart_details = mysqli_fetch_assoc($product_smart_table)) && ($product_agent_details = mysqli_fetch_assoc($product_agent_table)) && ($product_api_details = mysqli_fetch_assoc($product_api_table))){
                                        echo 
                                            '<tr style="background-color: transparent !important;">
                                                <td style="">
                                                    '.strtoupper($products." DIRECT DATA ".str_replace(["_","-"]," ",$product_smart_details["val_1"])).'
                                                    <input style="text-align: center;" name="api-id[]" type="text" value="'.$product_smart_details["api_id"].'" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-id[]" type="text" value="'.$product_smart_details["product_id"].'" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-code-1[]" type="text" value="'.$product_smart_details["val_1"].'" hidden readonly required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="'.strtolower(trim($products)).'_direct_data_'.str_replace(["_","-"],"_",$product_smart_details["val_1"]).'_smart_level" name="smart-price[]" type="text" value="'.$product_smart_details["val_2"].'" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="'.strtolower(trim($products)).'_direct_data_'.str_replace(["_","-"],"_",$product_smart_details["val_1"]).'_agent_level" name="agent-price[]" type="text" value="'.$product_agent_details["val_2"].'" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="'.strtolower(trim($products)).'_direct_data_'.str_replace(["_","-"],"_",$product_smart_details["val_1"]).'_api_level" name="api-price[]" type="text" value="'.$product_api_details["val_2"].'" placeholder="Price" pattern="[0-9.]{1,}" title="Amount Must Be A Digit" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="'.strtolower(trim($products)).'_direct_data_'.str_replace(["_","-"],"_",$product_smart_details["val_1"]).'_days" name="product-days[]" type="text" value="'.$product_api_details["val_3"].'" placeholder="Days" pattern="[0-9.]{1,}" title="Days Must Be A Digit" class="form-control mb-1" required/>
                                                </td>
                                            </tr>'; 
                                            $csv_price_level_array[] = strtolower(trim($products)).'_direct_data_'.str_replace(["_","-"],"_",$product_smart_details["val_1"]).",".$product_smart_details["val_2"].",".$product_agent_details["val_2"].",".$product_api_details["val_2"].",".$product_api_details["val_3"];
                                    }
                                }else{
                                    
                                }
                            }
                        ?>
                      </tbody>
                    </table>
                    <button id="" name="update-price" type="submit" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
                        UPDATE PRICE
                    </button><br>
                </form>
            </div>
          </div>
        </div><br/>
          
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
            <span style="user-select: auto;" class="h4 fw-bold">FILL PRICE TABLE USING CSV</span><br>
            <div style="user-select: auto; cursor: grab;" class="container col-12 border rounded-2 px-5 py-3 lh-lg py-5">
            	<form method="post" enctype="multipart/form-data" action="">
            		<input style="text-align: center;" id="csv-chooser" type="file" accept="" class="form-control mb-1" required/><br/>
            		<button onclick="getCSVDetails('5');" type="button" style="user-select: auto;" class="btn btn-primary col-12 mb-1" >
            			PROCESS
            		</button>
            	</form>
            </div><br/>
            
            <a onclick='downloadFile(`<?php echo implode("\n",$csv_price_level_array); ?>`, "direct-data.csv");' style="text-decoration: underline; user-select: auto;" class="h5 text-danger mt-3">Download Price CSV</a>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>