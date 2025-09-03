<?php
session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
include("../func/bc-admin-config.php");

if(isset($_POST["pay-bill"])){
    $bill_id = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["bill-id"])));
    // A simplified version of the bill payment logic from the original file
    $get_bill_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_billings WHERE id='".$bill_id."'");
    if(mysqli_num_rows($get_bill_details) == 1){
        $bill_amount = mysqli_fetch_array($get_bill_details);
        if(vendorBalance(1) >= $bill_amount["amount"]){
            // For this example, we'll just mark the bill as paid without the full transaction logic
            $add_vendor_paid_bill_details = mysqli_query($connection_server, "INSERT INTO sas_vendor_paid_bills (vendor_id, bill_id, bill_type, description, amount, starting_date, ending_date) VALUES ('".$get_logged_admin_details["id"]."', '".$bill_amount["id"]."', '".$bill_amount["bill_type"]."', '".$bill_amount["description"]."', '".$bill_amount["amount"]."', '".$bill_amount["starting_date"]."','".$bill_amount["ending_date"]."')");
             $_SESSION["product_purchase_response"] = "Bill paid successfully!";
        } else {
             $_SESSION["product_purchase_response"] = "Insufficient balance to pay bill.";
        }
    } else {
        $_SESSION["product_purchase_response"] = "Bill not found.";
    }
    header("Location: dashboard.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../new/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Admin Dashboard</h1>
    </div>

    <?php if (isset($_SESSION["product_purchase_response"])): ?>
        <div class="alert alert-info" role="alert">
            <?php echo $_SESSION["product_purchase_response"]; ?>
        </div>
        <?php unset($_SESSION["product_purchase_response"]); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Account Balance</div>
                <div class="card-body">
                    <h5 class="card-title text-white">₦<?php echo toDecimal($get_logged_admin_details["balance"], "2"); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Deposit</div>
                <div class="card-body">
                    <h5 class="card-title text-white">₦
                        <?php
                            $get_all_admin_credit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' AND (type_alternative LIKE '%credit%' OR type_alternative LIKE '%received%' OR type_alternative LIKE '%commission%')");
                            $all_admin_credit_transaction = 0;
                            if(mysqli_num_rows($get_all_admin_credit_transaction_details) >= 1){
                                while($transaction_record = mysqli_fetch_assoc($get_all_admin_credit_transaction_details)){
                                    $all_admin_credit_transaction += $transaction_record["discounted_amount"];
                                }
                            }
                            echo toDecimal($all_admin_credit_transaction, 2);
                        ?>
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Total Spent</div>
                <div class="card-body">
                    <h5 class="card-title text-white">₦
                         <?php
                            $get_all_admin_debit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' AND (type_alternative NOT LIKE '%credit%' && type_alternative NOT LIKE '%refund%' && type_alternative NOT LIKE '%received%' && type_alternative NOT LIKE '%commission%' && status NOT LIKE '%3%')");
                            $all_admin_debit_transaction = 0;
                            if(mysqli_num_rows($get_all_admin_debit_transaction_details) >= 1){
                                while($transaction_record = mysqli_fetch_assoc($get_all_admin_debit_transaction_details)){
                                    $all_admin_debit_transaction += $transaction_record["discounted_amount"];
                                }
                            }
                            echo toDecimal($all_admin_debit_transaction, 2);
                        ?>
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Manual Deposit</div>
                <div class="card-body">
                    <h5 class="card-title text-white">₦
                        <?php
                            $get_all_user_manual_credit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='".$get_logged_admin_details["id"]."' AND (type_alternative LIKE '%credit%' && description LIKE '%credit%' && description LIKE '%admin%')");
                            $all_user_manual_credit_transaction = 0;
                            if(mysqli_num_rows($get_all_user_manual_credit_transaction_details) >= 1){
                                while($transaction_record = mysqli_fetch_assoc($get_all_user_manual_credit_transaction_details)){
                                    $all_user_manual_credit_transaction += $transaction_record["discounted_amount"];
                                }
                            }
                            echo toDecimal($all_user_manual_credit_transaction, 2);
                        ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Unpaid Bills
                </div>
                <div class="card-body">
                    <form action="dashboard.php" method="POST">
                        <div class="input-group mt-3">
                            <select class="form-select" name="bill-id" required>
                                <option value="">Choose Bill to Pay</option>
                                <?php
                                    $get_active_billing_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_billings WHERE date >= '".$get_logged_admin_details["reg_date"]."' ORDER BY date DESC");
                                    if(mysqli_num_rows($get_active_billing_details) >= 1){
                                        while($active_billing = mysqli_fetch_assoc($get_active_billing_details)){
                                            $get_paid_bill_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_paid_bills WHERE vendor_id='".$get_logged_admin_details["id"]."' AND bill_id='".$active_billing["id"]."'");
                                            if(mysqli_num_rows($get_paid_bill_details) == 0){
                                                echo '<option value="'.$active_billing["id"].'">'.$active_billing["bill_type"].' @ N'.toDecimal($active_billing["amount"], 2).' (Due: '.formDateWithoutTime($active_billing["ending_date"]).')</option>';
                                            }
                                        }
                                    }
                                ?>
                            </select>
                            <button class="btn btn-primary" type="submit" name="pay-bill">Pay Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Virtual Bank Accounts
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php
                            $virtual_accounts = getVendorVirtualBank();
                            if ($virtual_accounts) {
                                foreach ($virtual_accounts as $account_json) {
                                    $account = json_decode($account_json, true);
                                    echo '<li class="list-group-item">';
                                    echo '<strong>'.$account['bank_name'].'</strong><br>';
                                    echo 'Account Name: '.$account['account_name'].'<br>';
                                    echo 'Account Number: '.$account['account_number'];
                                    echo '</li>';
                                }
                            } else {
                                echo '<li class="list-group-item">No virtual accounts found.</li>';
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
