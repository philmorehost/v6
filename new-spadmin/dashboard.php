<?php
session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
include("../func/bc-spadmin-config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../new/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Super Admin Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Total Vendors</div>
                <div class="card-body">
                    <h5 class="card-title text-white">
                        <?php
                            $get_total_vendors = mysqli_query($connection_server, "SELECT * FROM sas_vendors");
                            echo mysqli_num_rows($get_total_vendors);
                        ?>
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Active Vendors</div>
                <div class="card-body">
                    <h5 class="card-title text-white">
                         <?php
                            $get_active_vendors = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE status='1'");
                            echo mysqli_num_rows($get_active_vendors);
                        ?>
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-header">Total Users</div>
                <div class="card-body">
                    <h5 class="card-title text-white">
                        <?php
                            $get_total_users = mysqli_query($connection_server, "SELECT * FROM sas_users");
                            echo mysqli_num_rows($get_total_users);
                        ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Platform Balance (All Vendors)</div>
                <div class="card-body">
                    <h5 class="card-title text-white">₦
                        <?php
                            $get_all_vendor_balance = mysqli_query($connection_server, "SELECT SUM(balance) AS total_balance FROM sas_vendors");
                            $total_balance = mysqli_fetch_assoc($get_all_vendor_balance)['total_balance'];
                            echo toDecimal($total_balance, 2);
                        ?>
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Platform Deposit (All Vendors)</div>
                <div class="card-body">
                    <h5 class="card-title text-white">₦
                        <?php
                            $get_all_vendor_deposits = mysqli_query($connection_server, "SELECT SUM(discounted_amount) AS total_deposit FROM sas_vendor_transactions WHERE type_alternative LIKE '%credit%'");
                            $total_deposit = mysqli_fetch_assoc($get_all_vendor_deposits)['total_deposit'];
                            echo toDecimal($total_deposit, 2);
                        ?>
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Platform Spent (All Vendors)</div>
                <div class="card-body">
                    <h5 class="card-title text-white">₦
                        <?php
                            $get_all_vendor_spent = mysqli_query($connection_server, "SELECT SUM(discounted_amount) AS total_spent FROM sas_vendor_transactions WHERE type_alternative NOT LIKE '%credit%' AND type_alternative NOT LIKE '%refund%'");
                            $total_spent = mysqli_fetch_assoc($get_all_vendor_spent)['total_spent'];
                            echo toDecimal($total_spent, 2);
                        ?>
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Recent Platform Transactions (All Vendors)
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Vendor</th>
                        <th scope="col">Service</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $get_recent_transactions = mysqli_query($connection_server, "SELECT * FROM sas_vendor_transactions ORDER BY id DESC LIMIT 10");
                        while($transaction = mysqli_fetch_assoc($get_recent_transactions)){
                            $vendor_details = mysqli_fetch_assoc(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='".$transaction['vendor_id']."'"));
                            echo '<tr>';
                            echo '<td>'.$vendor_details['firstname'].' '.$vendor_details['lastname'].'</td>';
                            echo '<td>'.$transaction['type_alternative'].'</td>';
                            echo '<td>₦'.toDecimal($transaction['amount'], 2).'</td>';
                            echo '<td>'.formDate($transaction['date']).'</td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
