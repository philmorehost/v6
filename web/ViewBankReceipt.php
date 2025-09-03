<?php session_start();
include("../func/bc-config.php");

if (isset($_GET["ref"])) {
	$reference = mysqli_real_escape_string($connection_server, trim(strip_tags($_GET["ref"])));

	if (!empty($reference)) {
		$get_bank_transfer = mysqli_query($connection_server, "SELECT * FROM sas_bank_transfer_history WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $get_logged_user_details["username"] . "' && reference='$reference'");
		if (mysqli_num_rows($get_bank_transfer) == 1) {
			$all_bank_transfer = $get_bank_transfer;
		} else {
			if (mysqli_num_rows($get_bank_transfer) < 1) {
				//Invalid Bank Transfer ID
				$json_response_array = array("desc" => "Invalid Bank Transfer ID");
				$json_response_encode = json_encode($json_response_array, true);
			} else {
				if (mysqli_num_rows($get_bank_transfer) > 1) {
					//Duplicated Details, Contact Admin
					$json_response_array = array("desc" => "Duplicated Details, Contact Admin");
					$json_response_encode = json_encode($json_response_array, true);
				}
			}
		}
	} else {
		//Reference ID Empty
		$json_response_array = array("desc" => "Reference ID Empty");
		$json_response_encode = json_encode($json_response_array, true);
	}
	$json_response_decode = json_decode($json_response_encode, true);
	$_SESSION["product_purchase_response"] = $json_response_decode["desc"];
	//header("Location: ".$_SERVER["REQUEST_URI"]);
}
?>
<!DOCTYPE html>

<head>
	<title>View Bank Transfer |
		<?php echo $get_all_site_details["site_title"]; ?>
	</title>
	<meta charset="UTF-8" />
	<meta name=" description" content="<?php echo substr($get_all_site_details["site_desc"], 0, 160); ?>" />
	<meta http-equiv="Content-Type" content="text/html; " />
	<meta name="theme-color" content="black" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
	<link rel="stylesheet" href="/cssfile/bc-style.css">
	<meta name="author" content="BeeCodes Titan">
	<meta name="dc.creator" content="BeeCodes Titan">

	<!-- Google Fonts -->
	<link href="https://fonts.gstatic.com" rel="preconnect">
	<link
		href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
		rel="stylesheet">

	<!-- Vendor CSS Files -->
	<link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
	<link href="../assets-2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
	<link href="../assets-2/vendor/quill/quill.snow.css" rel="stylesheet">
	<link href="../assets-2/vendor/quill/quill.bubble.css" rel="stylesheet">
	<link href="../assets-2/vendor/remixicon/remixicon.css" rel="stylesheet">
	<link href="../assets-2/vendor/simple-datatables/style.css" rel="stylesheet">

	<!-- Template Main CSS File -->
	<link href="../assets-2/css/style.css" rel="stylesheet">

</head>

<body>
	<?php include("../func/bc-header.php"); ?>
	<div style="text-align: center;"
		class="bg-2 m-block-dp s-block-dp m-position-rel s-position-rel br-radius-5px m-width-94 s-width-94 m-height-auto s-height-auto m-padding-lt-1 s-padding-lt-1 m-padding-rt-1 s-padding-rt-1 m-padding-tp-5 s-padding-tp-3 m-padding-bm-1 s-padding-bm-1 m-margin-lt-2 s-margin-lt-2 m-margin-bm-2 s-margin-bm-2">
		<span style="user-select: auto;" class=" color-4 text-bold-500 m-font-size-20 s-font-size-25
		m-inline-block-dp s-inline-block-dp m-margin-bm-1 s-margin-bm-1">TRANSFER RECEIPT</span><br>

		<div style="text-align: center;" id="printable-div-area"
			class="bg-2 m-block-dp s-block-dp m-position-rel s-position-rel m-width-100 s-width-100 m-height-auto s-height-auto">
			<?php
			if ($all_bank_transfer) {
				$bank_transfer_status = array("1" => "Successful", "2" => "Processing", "3" => "Failed");
				if (mysqli_num_rows($all_bank_transfer) == 1) {
					$get_eletric_details = mysqli_fetch_assoc($all_bank_transfer);
					echo '
							<div class="box-shadow bg-2 m-inline-block-dp s-inline-block-dp m-width-100 s-width-100 m-item-horizontal-center s-item-horizontal-center m-item-vertical-center s-item-vertical-center">
								<div style="padding: 5%; margin: 5% 0% 1% 0%;" class="m-inline-block-dp s-inline-block-dp br-style-all-1 br-width-1 br-color-8 m-width-75 s-width-75">
									<div class="m-width-100 s-width-100 m-flex-row-dp s-flex-row-dp m-margin-bm-1 s-margin-bm-1">
										<div class="m-inline-block-dp s-inline-block-dp m-width-50 s-width-50 m-item-horizontal-left s-item-horizontal-left">
											<img class="m-width-30 s-width-30"
											src="/uploaded-image/' . str_replace(['.', ':'], '-', $_SERVER['HTTP_HOST']) . '_' . 'logo.png" />
										</div>

										<div class="m-inline-block-dp s-inline-block-dp m-width-50 s-width-50 m-item-horizontal-right s-item-horizontal-right">
											<!-- <img class="m-width-30 s-width-30"
											src="/asset/' . strtolower($get_eletric_details["gateway_name"]) . '.jpg" /> -->
										</div>
									</div>

									<div class="m-width-100 s-width-100 m-flex-column-dp s-column-row-dp m-margin-bm-5 s-margin-bm-5">
										<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-100 s-width-100 m-item-horizontal-center s-item-horizontal-center">Transaction ' . ucwords($bank_transfer_status[getTransaction($get_eletric_details["reference"], "status")]) . '</div>
										<div class="color-7 m-font-size-30 s-font-size-30 text-bold-500 m-width-100 s-width-100 m-item-horizontal-center s-item-horizontal-center text-bold-800">N' . toDecimal(getTransaction($get_eletric_details["reference"], "amount"), 2) . '</div>
									</div>

									<div class="m-width-100 s-width-100 m-flex-row-dp s-flex-row-dp m-margin-bm-5 s-margin-bm-5">
										<div style="line-height: 25px;" class="m-inline-block-dp s-inline-block-dp m-width-50 s-width-50 m-item-horizontal-left s-item-horizontal-left">
											<span class="color-7 m-font-size-14 s-font-size-16"><a style="text-decoration: underline;" href="mailto:' . $get_logged_user_details["email"] . '">' . $get_logged_user_details["email"] . '</a></span><br/>
											<span class="color-7 m-font-size-14 s-font-size-16">' . $get_logged_user_details["phone_number"] . '</span><br/>
											<span class="color-7 m-font-size-14 s-font-size-16 text-bold-800">Session ID: ' . $get_eletric_details["session_id"] . '</span>
										</div>

										<div style="line-height: 25px;" class="m-inline-block-dp s-inline-block-dp m-width-50 s-width-50 m-item-horizontal-right s-item-horizontal-right">
											<span class="color-7 m-font-size-14 s-font-size-16">Transaction ID: ' . $get_eletric_details["reference"] . '</span><br/>
											<span class="color-7 m-font-size-14 s-font-size-16">Created: ' . $get_eletric_details["date"] . '</span>
										
										</div>
									</div>

									<div class="m-width-100 s-width-100 m-flex-column-dp s-flex-column-dp">
										<div class="bg-8 m-width-100 s-width-100 m-flex-row-dp s-flex-row-dp m-padding-tp-1 s-padding-tp-1 m-padding-bm-1 s-padding-bm-1">
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-20 s-width-20">Bank</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-15 s-width-15">Account Number</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-50 s-width-50">Account Name</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-15 s-width-15">Status</div>
										</div>

										<div class="bg-3 m-width-100 s-width-100 m-flex-row-dp s-flex-row-dp m-padding-tp-1 s-padding-tp-1 m-padding-bm-1 s-padding-bm-1 m-margin-bm-2 s-margin-bm-2	">
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-500 m-width-20 s-width-20">' . strtoupper($get_eletric_details["bank_name"]) . '</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-500 m-width-15 s-width-15">' . strtoupper($get_eletric_details["account_number"]) . '</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-500 m-width-50 s-width-50">' . strtoupper($get_eletric_details["account_name"]) . '</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-500 m-width-15 s-width-15">' . $bank_transfer_status[getTransaction($get_eletric_details["reference"], "status")] . '</div>
										</div>

										<div class="bg-3 m-width-100 s-width-100 m-flex-row-dp s-flex-row-dp m-padding-tp-1 s-padding-tp-1 m-padding-bm-1 s-padding-bm-1">
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-40 s-width-40 m-item-horizontal-left s-item-horizontal-left">Amount Sent</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-500 m-width-60 s-width-60 m-item-horizontal-left s-item-horizontal-left">N' . toDecimal(getTransaction($get_eletric_details["reference"], "amount"), 2) . '</div>
										</div>

										<div class="bg-3 m-width-100 s-width-100 m-flex-row-dp s-flex-row-dp m-padding-tp-1 s-padding-tp-1 m-padding-bm-1 s-padding-bm-1">
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-40 s-width-40 m-item-horizontal-left s-item-horizontal-left">Online Service Charge</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-500 m-width-60 s-width-60 m-item-horizontal-left s-item-horizontal-left">N' . toDecimal((getTransaction($get_eletric_details["reference"], "discounted_amount") - getTransaction($get_eletric_details["reference"], "amount")), 2) . '</div> 
										</div>

										<div class="bg-3 m-width-100 s-width-100 m-flex-row-dp s-flex-row-dp m-padding-tp-1 s-padding-tp-1 m-padding-bm-1 s-padding-bm-1">
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-40 s-width-40 m-item-horizontal-left s-item-horizontal-left">Total Amount Paid</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-500 m-width-60 s-width-60 m-item-horizontal-left s-item-horizontal-left">' . toDecimal(getTransaction($get_eletric_details["reference"], "discounted_amount"), 2) . '</div>
										</div>

										<div class="bg-3 m-width-100 s-width-100 m-flex-row-dp s-flex-row-dp m-padding-tp-1 s-padding-tp-1 m-padding-bm-1 s-padding-bm-1">
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-40 s-width-40 m-item-horizontal-left s-item-horizontal-left">Session ID</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-500 m-width-60 s-width-60 m-item-horizontal-left s-item-horizontal-left">' . $get_eletric_details["session_id"] . '</div>
										</div>

										
										<div class="bg-3 m-width-100 s-width-100 m-flex-row-dp s-flex-row-dp m-padding-tp-1 s-padding-tp-1 m-padding-bm-1 s-padding-bm-1">
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-800 m-width-40 s-width-40 m-item-horizontal-left s-item-horizontal-left">Narration</div>
											<div class="color-7 m-font-size-14 s-font-size-16 text-bold-500 m-width-60 s-width-60 m-item-horizontal-left s-item-horizontal-left">' . strtoupper($get_eletric_details["narration"]) . '</div>
										</div>
									</div>
								</div><br/>
								<div style="padding: 0% 5% 5% 5%; margin: 0% 0% 5% 0%;" class="m-font-size-14 s-font-size-16 m-inline-block-dp s-inline-block-dp m-width-75 s-width-75 m-item-horizontal-left s-item-horizontal-left">
									<a href="' . $web_http_host . '">' . $web_http_host . '</a>
								</div>
							</div>
						';
					echo "<br/>" . "\n";
					echo '<span onclick="printPage();" style="user-select: auto; text-decoration: underline;" class="a-cursor m-inline-block-dp s-inline-block-dp m-position-rel s-position-rel m-font-size-14 s-font-size-16 m-margin-tp-2 s-margin-tp-2">Print Reciept</span>';

				}
			}
			?>
		</div>
	</div>

	<script>
		function printPage() {

			var printableDivArea = document.getElementById("printable-div-area").innerHTML;
			const html = [];
			html.push('<html><head>');
			html.push('<link rel="stylesheet" href="/cssfile/template/bc-style-template-1.css"><link rel="stylesheet" href="/cssfile/bc-style.css">');
			html.push('</head><body onload="window.focus(); window.print()"><div>');
			html.push(printableDivArea);
			html.push('</div></body></html>');

			var mywindow = window.open('', '', 'width=640,height=480');
			mywindow.document.open("text/html");
			mywindow.document.write(html.join(""));
			mywindow.document.close();

		}
	</script>

	<?php include("../func/bc-footer.php"); ?>

</body>

</html>