<?php
session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
include("../func/bc-config.php");
?>
<?php include 'header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Buy Airtime</h1>
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
                <form action="airtime-logic.php" method="POST">
                    <div class="mb-3">
                        <label for="network" class="form-label">Network</label>
                        <select class="form-select" id="network" name="network" required>
                            <option value="">Select Network</option>
                            <?php
                                $get_networks = mysqli_query($connection_server, "SELECT * FROM sas_airtime_status WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' AND status='1'");
                                while($network = mysqli_fetch_assoc($get_networks)){
                                    echo '<option value="'.$network["product_name"].'">'.strtoupper($network["product_name"]).'</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="true" id="bypassVerification" name="bypass">
                        <label class="form-check-label" for="bypassVerification">
                            Bypass Phone Verification (Not Recommended)
                        </label>
                    </div>
                    <button type="submit" name="buy-airtime" class="btn btn-primary w-100">Buy Now</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                Recent Airtime Purchases
            </div>
            <ul class="list-group list-group-flush">
                <?php
                    $get_recent_airtime = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE username='".$get_logged_user_details["username"]."' AND type_alternative LIKE '%airtime%' ORDER BY id DESC LIMIT 5");
                    while($transaction = mysqli_fetch_assoc($get_recent_airtime)){
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
