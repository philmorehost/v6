<?php
session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
include("../func/bc-config.php");

if(isset($_POST["reset-cable"])){
    unset($_SESSION["iuc_number"]);
    unset($_SESSION["cable_provider"]);
    unset($_SESSION["cable_package"]);
    unset($_SESSION["cable_name"]);
    header("Location: cable.php");
    exit();
}


// Fetch all cable plans and store them in a PHP array
$all_cable_plans = [];
$account_level_table_name_arrays = array(1 => "sas_smart_parameter_values", 2 => "sas_agent_parameter_values", 3 => "sas_api_parameter_values");
if($account_level_table_name_arrays[$get_logged_user_details["account_level"]] == true){
    $acc_level_table_name = $account_level_table_name_arrays[$get_logged_user_details["account_level"]];
    $product_name_array = array("startimes", "dstv", "gotv");

    foreach ($product_name_array as $network_name) {
        $get_status_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_cable_status WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$network_name."'"));
        $get_api_enabled_lists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && id='".$get_status_details["api_id"]."' && api_type='cable' && status='1' LIMIT 1");
        if(mysqli_num_rows($get_api_enabled_lists) == 1){
            $api_detail = mysqli_fetch_array($get_api_enabled_lists);
            $product_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_products WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && product_name='".$network_name."' LIMIT 1"));
            if($product_table["status"] == 1){
                $product_discount_table = mysqli_query($connection_server, "SELECT * FROM $acc_level_table_name WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' && api_id='".$api_detail["id"]."' && product_id='".$product_table["id"]."'");
                if(mysqli_num_rows($product_discount_table) > 0){
                    while($product_details = mysqli_fetch_assoc($product_discount_table)){
                      if($product_details["val_2"] > 0){
                        $plan_text = ucwords(trim(str_replace(["-", "_"], " ", $product_details["val_1"]))) . ' @ N' . $product_details["val_2"];
                        $all_cable_plans[$network_name][] = ["value" => $product_details["val_1"], "text" => $plan_text];
                      }
                    }
                }
            }
        }
    }
}
?>
<?php include 'header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Buy Cable TV Subscription</h1>
</div>

<?php if (isset($_SESSION["product_purchase_response"])): ?>
    <div class="alert alert-info" role="alert">
        <?php echo $_SESSION["product_purchase_response"]; ?>
    </div>
    <?php unset($_SESSION["product_purchase_response"]); ?>
<?php endif; ?>

<div class="row">
    <div class="col-lg-6">
        <?php if (!isset($_SESSION["cable_name"])): ?>
            <div class="card">
                <div class="card-header">
                    Step 1: Verify Decoder IUC
                </div>
                <div class="card-body">
                    <form action="cable-logic.php" method="POST">
                        <div class="mb-3">
                            <label for="provider" class="form-label">Cable Provider</label>
                            <select class="form-select" id="provider" name="provider" required>
                                <option value="">Select Provider</option>
                                <option value="dstv">DSTV</option>
                                <option value="gotv">GOTV</option>
                                <option value="startimes">Startimes</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="iuc" class="form-label">IUC Number</label>
                            <input type="text" class="form-control" id="iuc" name="iuc" placeholder="Enter IUC number" required>
                        </div>
                        <div class="mb-3">
                            <label for="package" class="form-label">Package</label>
                            <select class="form-select" id="package" name="package" required>
                                <option value="">Select a package</option>
                            </select>
                        </div>
                        <button type="submit" name="verify-cable" class="btn btn-primary w-100">Verify</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    Step 2: Confirm Purchase
                </div>
                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        IUC Verified Successfully!
                    </div>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div><h6 class="my-0">Customer Name</h6></div>
                            <span class="text-muted"><?php echo $_SESSION['cable_name']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div><h6 class="my-0">IUC Number</h6></div>
                            <span class="text-muted"><?php echo $_SESSION['iuc_number']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div><h6 class="my-0">Provider</h6></div>
                            <span class="text-muted"><?php echo strtoupper($_SESSION['cable_provider']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div><h6 class="my-0">Package</h6></div>
                            <span class="text-muted"><?php echo ucwords(str_replace("-", " ", $_SESSION['cable_package'])); ?></span>
                        </li>
                    </ul>
                    <form action="cable-logic.php" method="POST" class="d-grid gap-2">
                        <button type="submit" name="buy-cable" class="btn btn-primary">Buy Now</button>
                    </form>
                    <form action="cable.php" method="POST" class="d-grid gap-2 mt-2">
                         <button type="submit" name="reset-cable" class="btn btn-secondary">Reset</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                Recent Cable TV Purchases
            </div>
            <ul class="list-group list-group-flush">
               <?php
                    $get_recent_cable = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE username='".$get_logged_user_details["username"]."' AND type_alternative LIKE '%cable%' ORDER BY id DESC LIMIT 5");
                    while($transaction = mysqli_fetch_assoc($get_recent_cable)){
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
    const allCablePlans = <?php echo json_encode($all_cable_plans); ?>;
    const providerSelect = document.getElementById('provider');
    const packageSelect = document.getElementById('package');

    function updateCablePackages() {
        const selectedProvider = providerSelect.value;
        const plans = allCablePlans[selectedProvider] || [];

        packageSelect.innerHTML = '<option value="">Select a package</option>';
        if (plans.length > 0) {
            plans.forEach(plan => {
                const option = document.createElement('option');
                option.value = plan.value;
                option.textContent = plan.text;
                packageSelect.appendChild(option);
            });
        }
    }

    providerSelect.addEventListener('change', updateCablePackages);
</script>

<?php include 'footer.php'; ?>
