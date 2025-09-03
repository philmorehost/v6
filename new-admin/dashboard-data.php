<?php
// This file will contain the PHP logic for the admin dashboard.
// For now, we will just use some dummy data.

$admin_username = "Admin";
$account_status = "Active";
$account_balance = "123,456.78";
$total_deposit = "500,000.00";
$total_spent = "376,543.22";
$manual_deposit = "50,000.00";

$unpaid_bills = [
    ["id" => 1, "type" => "Hosting Fee", "amount" => "5,000.00", "start_date" => "2025-08-01", "end_date" => "2025-09-01"],
    ["id" => 2, "type" => "API Subscription", "amount" => "10,000.00", "start_date" => "2025-08-15", "end_date" => "2025-09-15"]
];

$virtual_accounts = [
    ["bank_name" => "Wema Bank", "account_name" => "VTU Platform - Admin", "account_number" => "1234567890"],
    ["bank_name" => "Moniepoint", "account_name" => "VTU Platform - Admin", "account_number" => "0987654321"]
];

?>
