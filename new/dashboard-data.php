<?php
// This file will contain the PHP logic for the dashboard.
// For now, we will just use some dummy data.

$username = "John Doe";
$account_balance = "12,345.67";

$transactions = [
    [
        "service" => "MTN Airtime",
        "amount" => "-₦1,000",
        "date" => "Today, 10:30 AM",
        "status" => "danger"
    ],
    [
        "service" => "Wallet Funding",
        "amount" => "+₦5,000",
        "date" => "Yesterday, 03:15 PM",
        "status" => "success"
    ],
    [
        "service" => "DSTV Subscription",
        "amount" => "-₦4,500",
        "date" => "August 28, 2025",
        "status" => "danger"
    ]
];
?>
