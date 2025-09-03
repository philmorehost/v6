<?php
// This file will contain the PHP logic for the data page.
// For now, we will just use some dummy data.

$networks = [
    [
        "name" => "MTN",
        "logo" => "/asset/mtn.png"
    ],
    [
        "name" => "Airtel",
        "logo" => "/asset/airtel.png"
    ],
    [
        "name" => "Glo",
        "logo" => "/asset/glo.png"
    ],
    [
        "name" => "9mobile",
        "logo" => "/asset/9mobile.png"
    ]
];

$data_types = ["SME Data", "Corporate Gifting", "Direct Data"];

$data_plans = [
    "mtn" => [
        "sme-data" => [
            ["plan" => "1GB", "price" => "₦250", "validity" => "30 Days"],
            ["plan" => "2GB", "price" => "₦500", "validity" => "30 Days"],
            ["plan" => "5GB", "price" => "₦1200", "validity" => "30 Days"],
        ],
        "corporate-gifting" => [
            ["plan" => "10GB", "price" => "₦2500", "validity" => "30 Days"],
            ["plan" => "20GB", "price" => "₦5000", "validity" => "30 Days"],
        ],
        "direct-data" => [
            ["plan" => "1.5GB", "price" => "₦1000", "validity" => "30 Days"],
            ["plan" => "2.5GB", "price" => "₦1500", "validity" => "30 Days"],
        ]
    ],
    "airtel" => [
        "sme-data" => [
            ["plan" => "1.5GB", "price" => "₦300", "validity" => "30 Days"],
            ["plan" => "3GB", "price" => "₦600", "validity" => "30 Days"],
        ],
        "corporate-gifting" => [
            ["plan" => "10GB", "price" => "₦2800", "validity" => "30 Days"],
        ],
        "direct-data" => [
            ["plan" => "2GB", "price" => "₦1200", "validity" => "30 Days"],
        ]
    ]
    // Add more networks and plans as needed
];

?>
