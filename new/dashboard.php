<?php
session_start([
    'cookie_lifetime' => 286400,
	'gc_maxlifetime' => 286400,
]);
include("../func/bc-config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Welcome back, <?php echo $get_logged_user_details["username"]; ?>!</h5>
                    <p class="card-text">Here's a quick overview of your account.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title text-white">Account Balance</h5>
                                    <h3 class="text-white">₦<?php echo toDecimal($get_logged_user_details["balance"], 2); ?></h3>
                                </div>
                                <div>
                                    <i data-feather="dollar-sign" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Quick Actions</h5>
                            <div class="d-grid gap-2">
                                <a href="Fund.php" class="btn btn-outline-primary"><i data-feather="dollar-sign" class="align-text-bottom"></i> Fund Wallet</a>
                                <a href="ShareFund.php" class="btn btn-outline-secondary"><i data-feather="arrow-up-right" class="align-text-bottom"></i> Fund Transfer</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i data-feather="smartphone" class="mb-3" style="font-size: 2.5rem; color: #0d6efd;"></i>
                            <h5 class="card-title">Buy Airtime</h5>
                            <p class="card-text">Top up your mobile phone with airtime.</p>
                            <a href="airtime.php" class="btn btn-primary">Buy Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i data-feather="wifi" class="mb-3" style="font-size: 2.5rem; color: #0d6efd;"></i>
                            <h5 class="card-title">Buy Data</h5>
                            <p class="card-text">Get internet data bundles for your devices.</p>
                            <a href="data.php" class="btn btn-primary">Buy Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i data-feather="tv" class="mb-3" style="font-size: 2.5rem; color: #0d6efd;"></i>
                            <h5 class="card-title">Cable TV</h5>
                            <p class="card-text">Pay for your cable TV subscriptions.</p>
                            <a href="cable.php" class="btn btn-primary">Pay Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    Recent Transactions
                </div>
                <ul class="list-group list-group-flush">
                     <?php
                        $get_recent_transactions = mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE username='".$get_logged_user_details["username"]."' ORDER BY id DESC LIMIT 5");
                        while($transaction = mysqli_fetch_assoc($get_recent_transactions)){
                            echo '<li class="list-group-item">';
                            echo '<div class="d-flex justify-content-between">';
                            echo '<span>'.strtoupper($transaction['type_alternative']).'</span>';
                            if($transaction['type_alternative'] == 'credit'){
                                echo '<span class="text-success">+₦'.toDecimal($transaction['amount'], 2).'</span>';
                            } else {
                                echo '<span class="text-danger">-₦'.toDecimal($transaction['amount'], 2).'</span>';
                            }
                            echo '</div>';
                            echo '<small class="text-muted">'.formDate($transaction['date']).'</small>';
                            echo '</li>';
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
