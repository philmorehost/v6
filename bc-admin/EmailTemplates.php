<?php session_start();
    include("../func/bc-admin-config.php");
    
    if(isset($_POST["update-template"])){
        $subject = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["subject"])));
        $body = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["body"])));
        $email_type = mysqli_real_escape_string($connection_server, trim(strip_tags(strtolower($_POST["type"]))));
        
        if(!empty($subject) && !empty($body) && !empty($email_type)){
            $template_details = mysqli_query($connection_server, "SELECT * FROM sas_email_templates WHERE vendor_id='".$get_logged_admin_details["id"]."' && email_type='$email_type'");
            if(mysqli_num_rows($template_details) == 1){
                mysqli_query($connection_server, "UPDATE sas_email_templates SET subject='$subject', body='$body' WHERE vendor_id='".$get_logged_admin_details["id"]."' && email_type='$email_type'");
                //Email Template Updated Successfully
                $json_response_array = array("desc" => "Email Template Updated Successfully");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(mysqli_num_rows($template_details) > 1){
                    //Duplicated Details
                    $json_response_array = array("desc" => "Duplicated Details");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(mysqli_num_rows($template_details) == 0){
                        mysqli_query($connection_server, "INSERT INTO sas_email_templates (vendor_id, email_type, subject, body) VALUES ('".$get_logged_admin_details["id"]."', '$email_type', '$subject', '$body')");
                        //Email Template Created Successfully
                        $json_response_array = array("desc" => "Email Template Created Successfully");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }else{
            if(empty($subject)){
                //Subject Field Empty
                $json_response_array = array("desc" => "Subject Field Empty");
                $json_response_encode = json_encode($json_response_array,true);
            }else{
                if(empty($body)){
                    //Body Field Empty
                    $json_response_array = array("desc" => "Body Field Empty");
                    $json_response_encode = json_encode($json_response_array,true);
                }else{
                    if(empty($email_type)){
                        //Email Type Field Empty
                        $json_response_array = array("desc" => "Email Type Field Empty");
                        $json_response_encode = json_encode($json_response_array,true);
                    }
                }
            }
        }

        $json_response_decode = json_decode($json_response_encode,true);
        $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
        header("Location: ".$_SERVER["REQUEST_URI"]);
    }

?>
<!DOCTYPE html>
<head>
    <title>Email Template | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_super_admin_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="black" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
    
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
    <?php include("../func/bc-admin-header.php"); ?>    
    
    <div class="pagetitle">
      <h1>EMAIL TEMPLATE</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Email Template</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">

    	<div style="text-align: center;" class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="text-dark h5">USER REGISTRATION TEMPLATE</span><br>
            <form method="post" enctype="multipart/form-data" action="">
    			<div style="text-align: center;" class="container">
                    <span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
                    <span id="" class="h6"><span style="user-select: auto;">Email address:</span> <span id="" class="" style="user-select: auto;">{email}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Phone number:</span> <span id="" class="" style="user-select: auto;">{phone}</span></span><br/>
					<span id="" class="h6"><span style="user-select: auto;">Username:</span> <span id="" class="" style="user-select: auto;">{username}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Home address:</span> <span id="" class="" style="user-select: auto;">{address}</span></span><br/>
    			</div><br/>
                <input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-reg" placeholder="Email Type" hidden readonly required/>
                <input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-reg','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
                <textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-reg','body'); ?></textarea><br>
    			<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
    				UPDATE TEMPLATE
    			</button><br>
    		</form>	
    	</div><br/>
    	
        <div style="text-align: center;" class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="text-dark h5">USER LOGIN TEMPLATE</span><br>
            <form method="post" enctype="multipart/form-data" action="">
    			<div style="text-align: center;" class="container">
                    <span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
					<span id="" class="h6"><span style="user-select: auto;">Username:</span> <span id="" class="" style="user-select: auto;">{username}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">IP address:</span> <span id="" class="" style="user-select: auto;">{ip_address}</span></span><br/>
                </div><br/>
                <input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-log" placeholder="Email Type" hidden readonly required/>
                <input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-log','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
                <textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-log','body'); ?></textarea><br>
    			<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
    				UPDATE TEMPLATE
    			</button><br>
    		</form>	
    	</div><br/>
    	
		<div style="text-align: center;" class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="text-dark h5">USER PASSWORD UPDATE TEMPLATE</span><br>
    	            <form method="post" enctype="multipart/form-data" action="">
    			<div style="text-align: center;" class="container">
    	                    <span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
    	                </div><br/>
    	                <input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-pass-update" placeholder="Email Type" hidden readonly required/>
    	                <input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-pass-update','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
    	                <textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-pass-update','body'); ?></textarea><br>
    			<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
    				UPDATE TEMPLATE
    			</button><br>
    		</form>	
    	</div><br/>
		
		<div style="text-align: center;" class="card info-card px-5 py-5">
			<span style="user-select: auto;" class="text-dark h5">USER ACCOUNT UPDATE TEMPLATE</span><br>
			<form method="post" enctype="multipart/form-data" action="">
				<div style="text-align: center;" class="container">
					<span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
					<span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
					<span id="" class="h6"><span style="user-select: auto;">Email address:</span> <span id="" class="" style="user-select: auto;">{email}</span></span>, 
					<span id="" class="h6"><span style="user-select: auto;">Phone number:</span> <span id="" class="" style="user-select: auto;">{phone}</span></span><br/>
					<span id="" class="h6"><span style="user-select: auto;">Home address:</span> <span id="" class="" style="user-select: auto;">{address}</span></span>, 
					<span id="" class="h6"><span style="user-select: auto;">Security answer:</span> <span id="" class="" style="user-select: auto;">{security_answer}</span></span><br/>
					
				</div><br/>
				<input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-account-update" placeholder="Email Type" hidden readonly required/>
				<input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-account-update','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
				<textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-account-update','body'); ?></textarea><br>
				<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
					UPDATE TEMPLATE
				</button><br>
			</form>	
		</div><br/>
		
        <div style="text-align: center;" class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="text-dark h5">USER PASSWORD RECOVERY TEMPLATE</span><br>
            <form method="post" enctype="multipart/form-data" action="">
    			<div style="text-align: center;" class="container">
                    <span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
                    <span id="" class="h6"><span style="user-select: auto;">Recovery Code:</span> <span id="" class="" style="user-select: auto;">{recovery_code}</span></span><br/>
                </div><br/>
                <input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-account-recovery" placeholder="Email Type" hidden readonly required/>
                <input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-account-recovery','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
                <textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-account-recovery','body'); ?></textarea><br>
    			<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
    				UPDATE TEMPLATE
    			</button><br>
    		</form>	
    	</div><br/>
        
        <div style="text-align: center;" class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="text-dark h5">USER ACCOUNT STATUS TEMPLATE</span><br>
            <form method="post" enctype="multipart/form-data" action="">
    			<div style="text-align: center;" class="container">
                    <span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
                    <span id="" class="h6"><span style="user-select: auto;">Account Status:</span> <span id="" class="" style="user-select: auto;">{account_status}</span></span><br/>
                </div><br/>
                <input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-account-status" placeholder="Email Type" hidden readonly required/>
                <input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-account-status','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
                <textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-account-status','body'); ?></textarea><br>
    			<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
    				UPDATE TEMPLATE
    			</button><br>
    		</form>	
    	</div><br/>

        <div style="text-align: center;" class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="text-dark h5">USER API STATUS TEMPLATE</span><br>
            <form method="post" enctype="multipart/form-data" action="">
    			<div style="text-align: center;" class="container">
                    <span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
                    <span id="" class="h6"><span style="user-select: auto;">API Status:</span> <span id="" class="" style="user-select: auto;">{api_status}</span></span><br/>
                </div><br/>
                <input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-api-status" placeholder="Email Type" hidden readonly required/>
                <input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-api-status','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
                <textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-api-status','body'); ?></textarea><br>
    			<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
    				UPDATE TEMPLATE
    			</button><br>
    		</form>	
    	</div><br/>
    	
    	<div style="text-align: center;" class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="text-dark h5">USER UPGRADE TEMPLATE</span><br>
    		<form method="post" enctype="multipart/form-data" action="">
    			<div style="text-align: center;" class="container">
    				<span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
    				<span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
    				<span id="" class="h6"><span style="user-select: auto;">Account Level:</span> <span id="" class="" style="user-select: auto;">{account_level}</span></span><br/>
    			</div><br/>
    			<input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-upgrade" placeholder="Email Type" hidden readonly required/>
    			<input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-upgrade','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
    			<textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-upgrade','body'); ?></textarea><br>
    			<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
    				UPDATE TEMPLATE
    			</button><br>
    		</form>	
    	</div><br/>
		
		<div style="text-align: center;" class="card info-card px-5 py-5">
			<span style="user-select: auto;" class="text-dark h5">USER REFERRAL COMMISSION TEMPLATE</span><br>
			<form method="post" enctype="multipart/form-data" action="">
				<div style="text-align: center;" class="container">
					<span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
					<span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
					<span id="" class="h6"><span style="user-select: auto;">Referral Commission:</span> <span id="" class="" style="user-select: auto;">{referral_commission}</span></span><br/>
					<span id="" class="h6"><span style="user-select: auto;">Referree:</span> <span id="" class="" style="user-select: auto;">{referree}</span></span>, 
					<span id="" class="h6"><span style="user-select: auto;">Account Level:</span> <span id="" class="" style="user-select: auto;">{account_level}</span></span><br/>
				</div><br/>
				<input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-referral-commission" placeholder="Email Type" hidden readonly required/>
				<input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-referral-commission','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
				<textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-referral-commission','body'); ?></textarea><br>
				<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
					UPDATE TEMPLATE
				</button><br>
			</form>	
		</div><br/>
		
        <div style="text-align: center;" class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="text-dark h5">USER TRANSACTION (ADMIN) TEMPLATE</span><br>
            <form method="post" enctype="multipart/form-data" action="">
    			<div style="text-align: center;" class="container">
					<span id="" class="h6"><span style="user-select: auto;">Admin Fullname:</span> <span id="" class="" style="user-select: auto;">{admin_firstname}</span>, <span id="" class="" style="user-select: auto;">{admin_lastname}</span></span><br/>
    		        <span id="" class="h6"><span style="user-select: auto;">Username:</span> <span id="" class="" style="user-select: auto;">{username}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span><br/>
                    <span id="" class="h6"><span style="user-select: auto;">Balance Before:</span> <span id="" class="" style="user-select: auto;">{balance_before}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Balance After:</span> <span id="" class="" style="user-select: auto;">{balance_after}</span></span><br/>
                    <span id="" class="h6"><span style="user-select: auto;">Amount Charged:</span> <span id="" class="" style="user-select: auto;">{amount}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Description:</span> <span id="" class="" style="user-select: auto;">{description}</span></span><br/>
                    <span id="" class="h6"><span style="user-select: auto;">Transaction Type:</span> <span id="" class="" style="user-select: auto;">{type}</span></span><br/>
                </div><br/>
                <input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-transactions" placeholder="Email Type" hidden readonly required/>
                <input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-transactions','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
                <textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-transactions','body'); ?></textarea><br>
    			<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
    				UPDATE TEMPLATE
    			</button><br>
    		</form>	
    	</div><br/>

        <div style="text-align: center;" class="card info-card px-5 py-5">
    		<span style="user-select: auto;" class="text-dark h5">USER CREDIT/DEBIT TEMPLATE</span><br>
            <form method="post" enctype="multipart/form-data" action="">
    			<div style="text-align: center;" class="container">
                    <span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
                    <span id="" class="h6"><span style="user-select: auto;">Balance Before:</span> <span id="" class="" style="user-select: auto;">{balance_before}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Balance After:</span> <span id="" class="" style="user-select: auto;">{balance_after}</span></span><br/>
                    <span id="" class="h6"><span style="user-select: auto;">Amount Charged:</span> <span id="" class="" style="user-select: auto;">{amount}</span></span>, 
    		        <span id="" class="h6"><span style="user-select: auto;">Description:</span> <span id="" class="" style="user-select: auto;">{description}</span></span><br/>
    		        <span id="" class="h6"><span style="user-select: auto;">Transaction Type:</span> <span id="" class="" style="user-select: auto;">{type}</span></span><br/>
                </div><br/>
                <input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-funding" placeholder="Email Type" hidden readonly required/>
                <input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-funding','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
                <textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-funding','body'); ?></textarea><br>
    			<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
    				UPDATE TEMPLATE
    			</button><br>
    		</form>	
    	</div><br/>

		
		<div style="text-align: center;" class="card info-card px-5 py-5">
			<span style="user-select: auto;" class="text-dark h5">USER REFUND TEMPLATE</span><br>
			<form method="post" enctype="multipart/form-data" action="">
				<div style="text-align: center;" class="container">
					<span id="" class="h6"><span style="user-select: auto;">Firstname:</span> <span id="" class="" style="user-select: auto;">{firstname}</span></span>, 
					<span id="" class="h6"><span style="user-select: auto;">Lastname:</span> <span id="" class="" style="user-select: auto;">{lastname}</span></span><br/>
					<span id="" class="h6"><span style="user-select: auto;">Amount:</span> <span id="" class="" style="user-select: auto;">{amount}</span></span>, 
					<span id="" class="h6"><span style="user-select: auto;">Description:</span> <span id="" class="" style="user-select: auto;">{description}</span></span><br/>
				</div><br/>
				<input style="text-align: left;" id="" name="type" onkeyup="" type="text" value="user-refund" placeholder="Email Type" hidden readonly required/>
				<input style="text-align: left;" id="" name="subject" onkeyup="" type="text" value="<?php echo getVendorEmailTemplate('user-refund','subject'); ?>" placeholder="Email Subject" class="form-control mb-1" required/><br/>
				<textarea style="text-align: left; resize: none;" id="" name="body" onkeyup="" placeholder="Email Body" class="form-control mb-1" rows="10" required><?php echo getVendorEmailTemplate('user-refund','body'); ?></textarea><br>
				<button name="update-template" type="submit" style="user-select: auto;" class="btn btn-success col-12" >
					UPDATE TEMPLATE
				</button><br>
			</form>	
		</div><br/>
		
        
      </div>
      </section>
        
    <?php include("../func/bc-admin-footer.php"); ?>
    
</body>
</html>