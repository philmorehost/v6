<?php
session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
include("../func/bc-config.php");

if(isset($_POST["reset-electric"])){
    unset($_SESSION["meter_amount"]);
    unset($_SESSION["meter_number"]);
    unset($_SESSION["meter_provider"]);
    unset($_SESSION["meter_type"]);
    unset($_SESSION["meter_name"]);
    header("Location: electric.php");
    exit();
}
?>
<?php include 'header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Buy Electricity</h1>
</div>

<?php if (isset($_SESSION["product_purchase_response"])): ?>
    <div class="alert alert-info" role="alert">
        <?php echo $_SESSION["product_purchase_response"]; ?>
    </div>
    <?php unset($_SESSION["product_purchase_response"]); ?>
<?php endif; ?>

<div class="row">
    <div class="col-lg-6">
        <?php if (!isset($_SESSION["meter_name"])): ?>
            <div class="card">
                <div class="card-header">
                    Step 1: Verify Meter Number
                </div>
                <div class="card-body">
                    <form action="electric-logic.php" method="POST">
                        <div class="mb-3">
                            <label for="provider" class="form-label">Electricity Provider</label>
                            <select class="form-select" id="provider" name="provider" required>
                                <option value="">Select Provider</option>
                                <?php
                                    $electric_type_array = array("ekedc", "eedc", "ikedc", "jedc", "kedco", "ibedc", "phed", "aedc", "yedc");
                                    foreach ($electric_type_array as $provider) {
                                        echo '<option value="'.$provider.'">'.strtoupper($provider).'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="meter-type" class="form-label">Meter Type</label>
                            <select class="form-select" id="meter-type" name="meter-type" required>
                                <option value="">Select Meter Type</option>
                                <option value="prepaid">Prepaid</option>
                                <option value="postpaid">Postpaid</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="meter-number" class="form-label">Meter Number</label>
                            <input type="text" class="form-control" id="meter-number" name="meter-number" placeholder="Enter meter number" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
                        </div>
                        <button type="submit" name="verify-meter" class="btn btn-primary w-100">Verify</button>
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
                        Meter Verified Successfully!
                    </div>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div><h6 class="my-0">Customer Name</h6></div>
                            <span class="text-muted"><?php echo $_SESSION['meter_name']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div><h6 class="my-0">Meter Number</h6></div>
                            <span class="text-muted"><?php echo $_SESSION['meter_number']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div><h6 class="my-0">Provider</h6></div>
                            <span class="text-muted"><?php echo strtoupper($_SESSION['meter_provider']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div><h6 class="my-0">Meter Type</h6></div>
                            <span class="text-muted"><?php echo ucwords($_SESSION['meter_type']); ?></span>
                        </li>
                         <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div><h6 class="my-0">Amount</h6></div>
                            <span class="text-muted">₦<?php echo toDecimal($_SESSION['meter_amount'], 2); ?></span>
                        </li>
                    </ul>
                    <form action="electric-logic.php" method="POST" class="d-grid gap-2">
                        <button type="submit" name="buy-electric" class="btn btn-primary">Buy Now</button>
                    </form>
                    <form action="electric.php" method="POST" class="d-grid gap-2 mt-2">
                         <button type="submit" name="reset-electric" class="btn btn-secondary">Reset</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                Recent Electricity Purchases
            </div>
            <ul class="list-group list-group-flush">
               <?php
                    $get_recent_electric = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE username='".$get_logged_user_details["username"]."' AND type_alternative LIKE '%electric%' ORDER BY id DESC LIMIT 5");
                    while($transaction = mysqli_fetch_assoc($get_recent_electric)){
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

<?php include 'footer.php'; ?>
