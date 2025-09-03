<?php
session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
include("../func/bc-config.php");

// Fetch all data plans and store them in a PHP array
$all_data_plans = [];
$account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
if($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true){
    $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
    $product_name_array = array("mtn", "airtel", "glo", "9mobile");
    $data_type_table_name_arrays = array("shared-data"=>"sas_shared_data_status", "sme-data"=>"sas_sme_data_status", "cg-data"=>"sas_cg_data_status", "dd-data"=>"sas_dd_data_status");

    foreach ($product_name_array as $network_name) {
        foreach ($data_type_table_name_arrays as $data_type => $table_name) {
            $get_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM ".$table_name." WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$network_name."'"));
            $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_status_details["api_id"]."' && api_type='".$data_type."' && status='1' LIMIT 1");
            if(mysqli_num_rows($get_api_enabled_lists) == 1){
                $api_detail = mysqli_fetch_array($get_api_enabled_lists);
                $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$network_name."' LIMIT 1"));
                if($product_table["status"] == 1){
                    $product_discount_table = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$api_detail["id"]."' && product_id='".$product_table["id"]."'");
                    if(mysqli_num_rows($product_discount_table) > 0){
                        while($product_details = mysqli_fetch_assoc($product_discount_table)){
                          if($product_details["val_2"] > 0){
                            $plan_text = strtoupper(str_replace("-", " ", $data_type)) . " " . str_replace("_", " ", $product_details["val_1"]) . ' @ N' . $product_details["val_2"] . ' (Validity ' . $product_details["val_3"] . 'days)';
                            $all_data_plans[$network_name][$data_type][] = ["value" => $product_details["val_1"], "text" => $plan_text];
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Buy Data</h1>
    </div>

    <?php if (isset($_SESSION["product_purchase_response"])): ?>
        <div class="alert alert-info" role="alert">
            <?php echo $_SESSION["product_purchase_response"]; ?>
        </div>
        <?php unset($_SESSION["product_purchase_response"]); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="card mt-4">
                <div class="card-header">
                    Enter Details
                </div>
                <div class="card-body">
                    <form action="data-logic.php" method="POST">
                        <div class="mb-3">
                            <label for="network" class="form-label">Network</label>
                            <select class="form-select" id="network" name="network" required>
                                <option value="">Select Network</option>
                                <option value="mtn">MTN</option>
                                <option value="airtel">Airtel</option>
                                <option value="glo">Glo</option>
                                <option value="9mobile">9mobile</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number" required>
                        </div>
                        <div class="mb-3">
                            <label for="data-type" class="form-label">Data Type</label>
                            <select class="form-select" id="data-type" name="data-type" required>
                                <option value="">Select Data Type</option>
                                <option value="sme-data">SME Data</option>
                                <option value="cg-data">Corporate Gifting</option>
                                <option value="dd-data">Direct Data</option>
                                <option value="shared-data">Shared Data</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="data-plan" class="form-label">Data Plan</label>
                            <select class="form-select" id="data-plan" name="data-plan" required>
                                <option value="">Select a data plan</option>
                            </select>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="true" id="bypass" name="bypass">
                            <label class="form-check-label" for="bypass">
                                Bypass Phone Verification
                            </label>
                        </div>
                        <button type="submit" name="buy-data" class="btn btn-primary w-100">Buy Now</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    Recent Data Purchases
                </div>
                <ul class="list-group list-group-flush">
                    <?php
                        $get_recent_data = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE username='".$get_logged_user_details["username"]."' AND type_alternative LIKE '%data%' ORDER BY id DESC LIMIT 5");
                        while($transaction = mysqli_fetch_assoc($get_recent_data)){
                            echo '<li class="list-group-item">';
                            echo '<div class="d-flex justify-content-between">';
                            echo '<span>'.strtoupper($transaction['type_alternative']).' ('.$transaction['type'].')</span>';
                            echo '<span class="text-danger">-₦'.toDecimal($transaction['amount'], 2).'</span>';
                            echo '</div>';
                            echo '<small class="text-muted">'.formDate($transaction['date']).'</small>';
                            echo '</li>';
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const allDataPlans = <?php echo json_encode($all_data_plans); ?>;
        const networkSelect = document.getElementById('network');
        const dataTypeSelect = document.getElementById('data-type');
        const dataPlanSelect = document.getElementById('data-plan');

        function updateDataPlans() {
            const selectedNetwork = networkSelect.value;
            const selectedDataType = dataTypeSelect.value;
            const plans = allDataPlans[selectedNetwork] ? allDataPlans[selectedNetwork][selectedDataType] || [] : [];

            dataPlanSelect.innerHTML = '<option value="">Select a data plan</option>';
            if (plans.length > 0) {
                plans.forEach(plan => {
                    const option = document.createElement('option');
                    option.value = plan.value;
                    option.textContent = plan.text;
                    dataPlanSelect.appendChild(option);
                });
            }
        }

        networkSelect.addEventListener('change', updateDataPlans);
        dataTypeSelect.addEventListener('change', updateDataPlans);
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
