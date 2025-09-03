<?php session_start();
include("../func/bc-admin-config.php");

if (isset($_POST["update-key"])) {
    $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
    $apikey = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-key"])));
    $apistatus = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-status"])));

    if (!empty($api_id) && is_numeric($api_id)) {
        if (!empty($apikey)) {
            if (is_numeric($apistatus) && in_array($apistatus, array("0", "1"))) {
                $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='betting'");
                if (mysqli_num_rows($select_api_lists) == 1) {
                    mysqli_query($connection_server, "UPDATE sas_apis SET api_key='$apikey', status='$apistatus' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='betting'");
                    //APIkey Updated Successfully
                    $json_response_array = array("desc" => "APIkey Updated Successfully");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    //API Doesnt Exists
                    $json_response_array = array("desc" => "API Doesnt Exists");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Invalid API Status
                $json_response_array = array("desc" => "Invalid API Status");
                $json_response_encode = json_encode($json_response_array, true);
            }
        } else {
            //Apikey Field Empty
            $json_response_array = array("desc" => "Apikey Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        }
    } else {
        //Invalid Apikey Website
        $json_response_array = array("desc" => "Invalid Apikey Website");
        $json_response_encode = json_encode($json_response_array, true);
    }
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["install-product"])) {
    $api_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["api-id"])));
    $item_status = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["item-status"])));
    $product_name = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["product-name"]))));
    $products_array = array("msport", "naijabet", "nairabet", "bet9ja-agent", "betland", "betlion", "supabet", "bet9ja", "bangbet", "betking", "1xbet", "betway", "merrybet", "mlotto", "western-lotto", "hallabet", "green-lotto");
    $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
    if (!empty($api_id) && is_numeric($api_id)) {
        if (!empty($product_name)) {
            if (in_array($product_name, $products_array)) {
                if (is_numeric($item_status) && in_array($item_status, array("0", "1"))) {
                    $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id' && api_type='betting'");
                    $select_betting_status_lists = mysqli_query($connection_server, "SELECT * FROM sas_betting_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                    if (mysqli_num_rows($select_api_lists) == 1) {
                        if (mysqli_num_rows($select_betting_status_lists) == 0) {
                            mysqli_query($connection_server, "INSERT INTO sas_betting_status (vendor_id, api_id, product_name, status) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '$product_name', '$item_status')");
                        } else {
                            mysqli_query($connection_server, "UPDATE sas_betting_status SET api_id='$api_id', status='$item_status' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                        }

                        foreach ($account_level_table_name_arrays as $account_level_table_name) {
                            $select_product_details = mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$product_name'");
                            if (mysqli_num_rows($select_product_details) == 1) {
                                $get_product_details = mysqli_fetch_array($select_product_details);
                                $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id'"));
                                $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id!='$api_id' && api_type='" . $get_selected_api_list["api_type"] . "' LIMIT 1");
                                if (mysqli_num_rows($select_api_list_with_api_type) == 1) {
                                    $get_api_list_with_api_type = mysqli_fetch_array($select_api_list_with_api_type);
                                    $select_api_list_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_api_list_with_api_type["id"] . "' && product_id='" . $get_product_details["id"] . "'");
                                    if (mysqli_num_rows($select_api_list_product_pricing_table) == 1) {
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
                                    } else {
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
                                } else {
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
                                $select_all_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='betting'");
                                $product_pricing_table = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='$api_id' && product_id='" . $get_product_details["id"] . "'");
                                if (mysqli_num_rows($product_pricing_table) == 0) {
                                    mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '" . $get_product_details["id"] . "', '$pro_val_1')");
                                } else {
                                    if (mysqli_num_rows($select_all_api_lists) >= 1) {
                                        while ($api_details = mysqli_fetch_assoc($select_all_api_lists)) {
                                            if ($api_details["id"] !== $api_id) {
                                                mysqli_query($connection_server, "DELETE FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $api_details["id"] . "' && product_id='" . $get_product_details["id"] . "'");
                                            } else {
                                                $check_product_pricing_row_exists = mysqli_query($connection_server, "SELECT * FROM $account_level_table_name WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='$api_id' && product_id='" . $get_product_details["id"] . "'");
                                                if (mysqli_num_rows($check_product_pricing_row_exists) == 0) {
                                                    mysqli_query($connection_server, "INSERT INTO $account_level_table_name (vendor_id, api_id, product_id, val_1) VALUES ('" . $get_logged_admin_details["id"] . "', '$api_id', '" . $get_product_details["id"] . "', '$pro_val_1')");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //Product Updated Successfully
                        $json_response_array = array("desc" => "Product Updated Successfully");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        //API Doesnt Exists
                        $json_response_array = array("desc" => "API Doesnt Exists");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //Invalid Betting Status
                    $json_response_array = array("desc" => "Invalid Betting Status");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Invalid Product Name
                $json_response_array = array("desc" => "Invalid Product Name");
                $json_response_encode = json_encode($json_response_array, true);
            }
        } else {
            //Product Name Field Empty
            $json_response_array = array("desc" => "Product Name Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        }
    } else {
        //Invalid Apikey Website
        $json_response_array = array("desc" => "Invalid Apikey Website");
        $json_response_encode = json_encode($json_response_array, true);
    }
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-price"])) {
    $api_id_array = $_POST["api-id"];
    $product_id_array = $_POST["product-id"];
    $smart_price_array = $_POST["smart-price"];
    $agent_price_array = $_POST["agent-price"];
    $api_price_array = $_POST["api-price"];
    $account_level_table_name_arrays = array("sas_smart_parameter_values", "sas_agent_parameter_values", "sas_api_parameter_values");
    if (count($api_id_array) == count($product_id_array)) {
        foreach ($api_id_array as $index => $api_id) {
            $api_id = $api_id_array[$index];
            $product_id = $product_id_array[$index];
            $smart_price = $smart_price_array[$index];
            $agent_price = $agent_price_array[$index];
            $api_price = $api_price_array[$index];
            $get_selected_api_list = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='$api_id'"));
            $select_api_list_with_api_type = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='" . $get_selected_api_list["api_type"] . "'");
            if (mysqli_num_rows($select_api_list_with_api_type) > 0) {
                while ($refined_api_id = mysqli_fetch_assoc($select_api_list_with_api_type)) {
                    $smart_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[0] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id'");
                    if (mysqli_num_rows($smart_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[0] . " (vendor_id, api_id, product_id, val_1) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$smart_price')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[0] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$smart_price' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id'");
                    }

                    $agent_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[1] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id'");
                    if (mysqli_num_rows($agent_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[1] . " (vendor_id, api_id, product_id, val_1) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$agent_price')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[1] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$agent_price' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id'");
                    }

                    $api_product_pricing_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[2] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id'");
                    if (mysqli_num_rows($api_product_pricing_table) == 0) {
                        mysqli_query($connection_server, "INSERT INTO " . $account_level_table_name_arrays[2] . " (vendor_id, api_id, product_id, val_1) VALUES ('" . $get_logged_admin_details["id"] . "', '" . $refined_api_id["id"] . "', '$product_id', '$api_price')");
                    } else {
                        mysqli_query($connection_server, "UPDATE " . $account_level_table_name_arrays[2] . " SET vendor_id='" . $get_logged_admin_details["id"] . "', api_id='" . $refined_api_id["id"] . "', product_id='$product_id', val_1='$api_price' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $refined_api_id["id"] . "' && product_id='$product_id'");
                    }
                }
            }
        }
        //Price Updated Successfully
        $json_response_array = array("desc" => "Price Updated Successfully");
        $json_response_encode = json_encode($json_response_array, true);
    } else {
        //Product Connection Error
        $json_response_array = array("desc" => "Product Connection Error");
        $json_response_encode = json_encode($json_response_array, true);
    }
    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

$csv_price_level_array = [];
$csv_price_level_array[] = "product_name,smart_level,agent_level,api_level";

?>
<!DOCTYPE html>

<head>
    <title>Betting API | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_super_admin_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
      <h1>BETTING API</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Betting</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
      
        <span style="user-select: auto;"
            class="h4 fw-bold">API
            SETTING</span><br>
        <form method="post" action="">
            <select style="text-align: center;" id="" name="api-id" onchange="getWebApikey(this);"
                class="form-control mb-1"
                required />
            <?php
            //All Betting API
            $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='betting'");
            if (mysqli_num_rows($get_api_lists) >= 1) {
                echo '<option value="" default hidden selected>Choose API</option>';
                while ($api_details = mysqli_fetch_assoc($get_api_lists)) {
                    if (empty(trim($api_details["api_key"]))) {
                        $apikey_status = "( Empty Key )";
                    } else {
                        $apikey_status = "";
                    }

                    echo '<option value="' . $api_details["id"] . '" api-key="' . $api_details["api_key"] . '" api-status="' . $api_details["status"] . '">' . strtoupper($api_details["api_base_url"]) . ' ' . $apikey_status . '</option>';
                }
            } else {
                echo '<option value="" default hidden selected>No API</option>';
            }
            ?>
            </select><br />
            <select style="text-align: center;" id="web-apikey-status" name="api-status" onchange=""
                class="form-control mb-1"
                required />
            <option value="" default hidden selected>Choose API Status</option>
            <option value="1">Enabled</option>
            <option value="0">Disabled</option>
            </select><br />
            <input style="text-align: center;" id="web-apikey-input" name="api-key" onkeyup="" type="text" value=""
                placeholder="Api Key"
                class="form-control mb-1"
                required /><br />
            <button name="update-key" type="submit" style="user-select: auto;"
                class="btn btn-primary col-12 mb-1">
                UPDATE KEY
            </button><br>
            <div style="text-align: center;"
                class="container">
                <span id="product-status-span" class="h5" style="user-select: auto;"></span>
            </div><br />
        </form>
          </div>
        </div>
        
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
        <span style="user-select: auto;"
            class="h4 fw-bold">PRODUCT
            INSTALLATION</span><br>
        <div style="text-align: center; user-select: auto;"
            class="container">
            <img alt="msport" id="msport-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/msport.jpg"
                onclick="tickProduct(this, 'msport', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="naijabet" id="naijabet-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/naijabet.jpg"
                onclick="tickProduct(this, 'naijabet', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="nairabet" id="nairabet-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/nairabet.jpg"
                onclick="tickProduct(this, 'nairabet', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="bet9ja-agent" id="bet9ja-agent-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/bet9ja-agent.jpg"
                onclick="tickProduct(this, 'bet9ja-agent', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="betland" id="betland-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/betland.jpg"
                onclick="tickProduct(this, 'betland', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="betlion" id="betlion-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/betlion.jpg"
                onclick="tickProduct(this, 'betlion', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="supabet" id="supabet-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/supabet.jpg"
                onclick="tickProduct(this, 'supabet', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="bet9ja" id="bet9ja-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/bet9ja.jpg"
                onclick="tickProduct(this, 'bet9ja', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="bangbet" id="bangbet-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/bangbet.jpg"
                onclick="tickProduct(this, 'bangbet', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="betking" id="betking-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/betking.jpg"
                onclick="tickProduct(this, 'betking', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="1xbet" id="1xbet-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/1xbet.jpg"
                onclick="tickProduct(this, '1xbet', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="betway" id="betway-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/betway.jpg"
                onclick="tickProduct(this, 'betway', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="merrybet" id="merrybet-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/merrybet.jpg"
                onclick="tickProduct(this, 'merrybet', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="mlotto" id="mlotto-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/mlotto.jpg"
                onclick="tickProduct(this, 'mlotto', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="western-lotto" id="western-lotto-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/western-lotto.jpg"
                onclick="tickProduct(this, 'western-lotto', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="hallabet" id="hallabet-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/hallabet.jpg"
                onclick="tickProduct(this, 'hallabet', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

            <img alt="green-lotto" id="green-lotto-lg"
                product-name-array="msport,naijabet,nairabet,bet9ja-agent,betland,betlion,supabet,bet9ja,bangbet,betking,1xbet,betway,merrybet,mlotto,western-lotto,hallabet,green-lotto"
                src="/asset/green-lotto.jpg"
                onclick="tickProduct(this, 'green-lotto', 'api-product-name', 'install-product', 'jpg');"
                class="col-2 rounded-5 border m-1  " />

        </div><br />
        <form method="post" action="">
            <input id="api-product-name" name="product-name" type="text" placeholder="Product Name" hidden readonly
                required />
            <select style="text-align: center;" id="" name="api-id" onchange=""
                class="form-control mb-1"
                required />
            <?php
            //All Betting API
            $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_type='betting'");
            if (mysqli_num_rows($get_api_lists) >= 1) {
                echo '<option value="" default hidden selected>Choose API</option>';
                while ($api_details = mysqli_fetch_assoc($get_api_lists)) {
                    if (empty(trim($api_details["api_key"]))) {
                        $apikey_status = "( Empty Key )";
                    } else {
                        $apikey_status = "";
                    }

                    echo '<option value="' . $api_details["id"] . '">' . strtoupper($api_details["api_base_url"]) . ' ' . $apikey_status . '</option>';
                }
            } else {
                echo '<option value="" default hidden selected>No API</option>';
            }
            ?>
            </select><br />
            <div style="text-align: center;"
                class="container">
                <span id="user-status-span" class="h5" style="user-select: auto;">BETTING STATUS</span>
            </div><br />
            <select style="text-align: center;" id="" name="item-status" onchange=""
                class="form-control mb-1"
                required />
            <option value="" default hidden selected>Choose Betting Status</option>
            <option value="1">Enabled</option>
            <option value="0">Disabled</option>
            </select><br />
            <button id="install-product" name="install-product" type="submit"
                style="pointer-events: none; user-select: auto;"
                class="btn btn-primary col-12 mb-1">
                INSTALL PRODUCT
            </button><br>
        </form>
      </div>
      </div>
  
      <div class="card info-card px-5 py-5">
        <div class="row mb-3">
        <span style="user-select: auto;"
            class="h4 fw-bold">INSTALLED
            BETTING STATUS</span><br>
        <div style="user-select: auto; cursor: grab;" class="overflow-auto mt-1">
          <table style="" class="table table-responsive table-striped table-bordered" title="Horizontal Scroll: Shift + Mouse Scroll Button">
              <thead class="thead-dark">
                <tr>
                    <th>Product Name</th><th>API Route</th><th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $item_name_array = array("msport", "naijabet", "nairabet", "bet9ja-agent", "betland", "betlion", "supabet", "bet9ja", "bangbet", "betking", "1xbet", "betway", "merrybet", "mlotto", "western-lotto", "hallabet", "green-lotto");
                foreach ($item_name_array as $products) {
                    $items_statement .= "product_name='$products' OR ";
                }
                $items_statement = "(" . trim(rtrim($items_statement, " OR ")) . ")";
                $select_item_lists = mysqli_query($connection_server, "SELECT * FROM sas_betting_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && $items_statement");
                if (mysqli_num_rows($select_item_lists) >= 1) {
                    while ($list_details = mysqli_fetch_assoc($select_item_lists)) {
                        $select_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $list_details["api_id"] . "' && api_type='betting'");
                        if (mysqli_num_rows($select_api_lists) == 1) {
                            $api_details = mysqli_fetch_array($select_api_lists);
                            $api_route_web = strtoupper($api_details["api_base_url"]);
                        } else {
                            if (mysqli_num_rows($select_api_lists) == 0) {
                                $api_route_web = "Invalid API Website";
                            } else {
                                $api_route_web = "Duplicated API Website";
                            }
                        }
                        if (strtolower(itemStatus($list_details["status"])) == "enabled") {
                            $item_status = '<span style="color: green;">' . itemStatus($list_details["status"]) . '</span>';
                        } else {
                            $item_status = '<span style="color: grey;">' . itemStatus($list_details["status"]) . '</span>';
                        }

                        echo
                            '<tr>
                                    <td>' . strtoupper(str_replace(["-", "_"], " ", $list_details["product_name"])) . '</td><td>' . $api_route_web . '</td><td>' . $item_status . '</td>
                                </tr>';
                    }
                }
                ?>
              </tbody>
            </table>
        </div>
      </div>
    </div><br />
    
        <div class="card info-card px-5 py-5">
          <div class="row mb-3">
        <span style="user-select: auto;"
            class="h4 fw-bold">BETTING
            DISCOUNT</span><br>
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
                          <th>Product Name</th><th>Smart Earner</th><th>Agent Vendor</th><th>API Vendor</th>
                      </tr>
                    </thead>
                    <tbody>

                    <?php
                    $item_name_array_2 = array("msport", "naijabet", "nairabet", "bet9ja-agent", "betland", "betlion", "supabet", "bet9ja", "bangbet", "betking", "1xbet", "betway", "merrybet", "mlotto", "western-lotto", "hallabet", "green-lotto");
                    foreach ($item_name_array_2 as $products) {
                        $get_item_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_betting_status WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products'"));
                        $get_api_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && id='" . $get_item_status_details["api_id"] . "' && api_type='betting'");
                        $account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
                        $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_name='$products' LIMIT 1"));
                        $product_smart_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[1] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                        $product_agent_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[2] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");
                        $product_api_table = mysqli_query($connection_server, "SELECT * FROM " . $account_level_table_name_arrays[3] . " WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_id='" . $get_item_status_details["api_id"] . "' && product_id='" . $product_table["id"] . "'");

                        if ((mysqli_num_rows($get_api_lists) == 1) && (mysqli_num_rows($product_smart_table) > 0) && (mysqli_num_rows($product_agent_table) > 0) && (mysqli_num_rows($product_api_table) > 0)) {
                            while (($product_smart_details = mysqli_fetch_assoc($product_smart_table)) && ($product_agent_details = mysqli_fetch_assoc($product_agent_table)) && ($product_api_details = mysqli_fetch_assoc($product_api_table))) {
                                echo
                                    '<tr style="background-color: transparent !important;">
                                                <td style="">
                                                    ' . strtoupper($products) . '
                                                    <input style="text-align: center;" name="api-id[]" type="text" value="' . $product_smart_details["api_id"] . '" hidden readonly required/>
                                                    <input style="text-align: center;" name="product-id[]" type="text" value="' . $product_smart_details["product_id"] . '" hidden readonly required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_smart_level" name="smart-price[]" type="number" value="' . $product_smart_details["val_1"] . '" placeholder="Percentage" step="0.1" min="0" max="100" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_agent_level" name="agent-price[]" type="number" value="' . $product_agent_details["val_1"] . '" placeholder="Percentage" step="0.1" min="0" max="100" class="product-price form-control mb-1" required/>
                                                </td>
                                                <td>
                                                    <input style="text-align: center;" id="' . strtolower(trim($products)) . '_api_level" name="api-price[]" type="number" value="' . $product_api_details["val_1"] . '" placeholder="Percentage" step="0.1" min="0" max="100" class="product-price form-control mb-1" required/>
                                                </td>
                                            </tr>';
                                $csv_price_level_array[] = strtolower(trim($products)) . "," . $product_smart_details["val_1"] . "," . $product_agent_details["val_1"] . "," . $product_api_details["val_1"];
                            }
                        } else {

                        }
                    }
                    ?>
                  </tbody>
                </table>
                <button id="" name="update-price" type="submit" style="user-select: auto;"
                    class="btn btn-primary col-12 mb-1">
                    UPDATE PRICE
                </button><br>
            </form>
        </div>
      </div>
    </div><br />

    <div class="card info-card px-5 py-5">
      <div class="row mb-3">
        <span style="user-select: auto;"
            class="h4 fw-bold">FILL
            PRICE TABLE USING CSV</span><br>
        <div style="user-select: auto; cursor: grab;" class="container col-12 border rounded-2 px-5 py-3 lh-lg py-5">
            <form method="post" enctype="multipart/form-data" action="">
                <input style="text-align: center;" id="csv-chooser" type="file" accept=""
                    class="form-control mb-1"
                    required /><br />
                <button onclick="getCSVDetails('4');" type="button" style="user-select: auto;"
                    class="btn btn-primary col-12 mb-1">
                    PROCESS
                </button>
            </form>
        </div><br />

        <a onclick='downloadFile(`<?php echo implode("\n", $csv_price_level_array); ?>`, "betting.csv");'
            style="text-decoration: underline; user-select: auto;"
            class="h5 text-danger mt-3">Download Price CSV</a>
      </div>
      </div>
    </div>
  </div>
</section>
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>

</html>