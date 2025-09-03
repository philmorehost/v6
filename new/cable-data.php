<?php
// This file will contain the PHP logic for the cable page.
// For now, we will just use some dummy data.

$cable_providers = [
    [
        "name" => "DSTV",
        "logo" => "/asset/dstv.jpg"
    ],
    [
        "name" => "GOTV",
        "logo" => "/asset/gotv.jpg"
    ],
    [
        "name" => "Startimes",
        "logo" => "/asset/startimes.jpg"
    ]
];

$cable_packages = [
    "dstv" => [
        ["name" => "Padi", "price" => "₦2150"],
        ["name" => "Yanga", "price" => "₦2950"],
        ["name" => "Confam", "price" => "₦5300"],
        ["name" => "Compact", "price" => "₦9000"],
    ],
    "gotv" => [
        ["name" => "Smallie", "price" => "₦900"],
        ["name" => "Jolli", "price" => "₦2800"],
        ["name" => "Max", "price" => "₦4150"],
    ],
    "startimes" => [
        ["name" => "Nova", "price" => "₦900"],
        ["name" => "Basic", "price" => "₦1700"],
        ["name" => "Smart", "price" => "₦2200"],
    ]
];

// Dummy data for verified IUC
$verified_iuc = null;
if (isset($_GET['verify'])) {
    $verified_iuc = [
        "name" => "John Doe",
        "iuc" => "1234567890",
        "provider" => "DSTV",
        "package" => "Compact"
    ];
}

?>
