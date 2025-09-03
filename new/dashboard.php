<?php include 'dashboard-data.php'; ?>
<?php include 'header.php'; ?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
            <span data-feather="calendar"></span>
            This week
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Welcome back, <?php echo $username; ?>!</h5>
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
                                <h3 class="text-white">₦<?php echo $account_balance; ?></h3>
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
                            <a href="#" class="btn btn-outline-primary"><i data-feather="dollar-sign" class="align-text-bottom"></i> Fund Wallet</a>
                            <a href="#" class="btn btn-outline-secondary"><i data-feather="arrow-up-right" class="align-text-bottom"></i> Fund Transfer</a>
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
                        <a href="#" class="btn btn-primary">Buy Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i data-feather="wifi" class="mb-3" style="font-size: 2.5rem; color: #0d6efd;"></i>
                        <h5 class="card-title">Buy Data</h5>
                        <p class="card-text">Get internet data bundles for your devices.</p>
                        <a href="#" class="btn btn-primary">Buy Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i data-feather="tv" class="mb-3" style="font-size: 2.5rem; color: #0d6efd;"></i>
                        <h5 class="card-title">Cable TV</h5>
                        <p class="card-text">Pay for your cable TV subscriptions.</p>
                        <a href="#" class="btn btn-primary">Pay Now</a>
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
                <?php foreach ($transactions as $transaction): ?>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span><?php echo $transaction['service']; ?></span>
                            <span class="text-<?php echo $transaction['status']; ?>"><?php echo $transaction['amount']; ?></span>
                        </div>
                        <small class="text-muted"><?php echo $transaction['date']; ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
