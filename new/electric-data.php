<?php
// This file will contain the PHP logic for the electric page.
// For now, we will just use some dummy data.

$electric_providers = [
    ["name" => "Eko Electricity (EKEDC)", "logo" => "/asset/ekedc.jpg"],
    ["name" => "Ikeja Electricity (IKEDC)", "logo" => "/asset/ikedc.jpg"],
    ["name" => "Abuja Electricity (AEDC)", "logo" => "/asset/aedc.jpg"],
    ["name" => "Kano Electricity (KEDCO)", "logo" => "/asset/kedco.jpg"],
    ["name" => "Port Harcourt Electricity (PHED)", "logo" => "/asset/phed.jpg"],
    ["name" => "Ibadan Electricity (IBEDC)", "logo" => "/asset/ibedc.jpg"],
    ["name" => "Jos Electricity (JEDC)", "logo" => "/asset/jedc.jpg"],
    ["name" => "Enugu Electricity (EEDC)", "logo" => "/asset/eedc.jpg"],
    ["name" => "Yola Electricity (YEDC)", "logo" => "/asset/yedc.jpg"]
];

$meter_types = ["Prepaid", "Postpaid"];

// Dummy data for verified meter
$verified_meter = null;
if (isset($_GET['verify'])) {
    $verified_meter = [
        "name" => "Jane Doe",
        "meter_number" => "0123456789",
        "provider" => "Ikeja Electricity (IKEDC)",
        "type" => "Prepaid",
        "amount" => "5000"
    ];
}

?>
