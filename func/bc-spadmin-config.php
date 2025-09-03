<?php
	if(isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")){
		$web_http_host = "https://".$_SERVER["HTTP_HOST"];
	}else{
		$web_http_host = "http://".$_SERVER["HTTP_HOST"];
	}

	include("bc-connect.php");
	include("bc-func.php");
	include("bc-tables.php");

	if($connection){
	$checkmate_super_admin_table_exists = mysqli_query($connection_server, "SELECT * FROM sas_super_admin");
	if(mysqli_num_rows($checkmate_super_admin_table_exists) >= 1){
	//Select Super Admin Table
	$select_super_admin_table = mysqli_query($connection_server, "SELECT * FROM sas_super_admin");
	if(mysqli_num_rows($select_super_admin_table) > 0){
		if(isset($_SESSION["spadmin_session"])){
			$get_logged_spadmin_query = mysqli_query($connection_server, "SELECT * FROM sas_super_admin WHERE email='".$_SESSION["spadmin_session"]."'");
			if(mysqli_num_rows($get_logged_spadmin_query) == 1){
				$get_logged_spadmin_details = mysqli_fetch_array($get_logged_spadmin_query);
				if($get_logged_spadmin_details["status"] == 1){
					if(in_array(explode("?",trim($_SERVER["REQUEST_URI"]))[0], array("/bc-spadmin/Login.php", "/bc-spadmin/PasswordRecovery.php"))){
						header("Location: /bc-spadmin/Dashboard.php");
					}else{
						
					}
					alterVendor($get_logged_spadmin_details["id"], "last_login", date('Y-m-d H:i:s'));
				}else{
					header("Location: /spadmin-logout.php");
				}
			}else{
				header("Location: /spadmin-logout.php");
			}
		}else{
			if(!in_array(explode("?",trim($_SERVER["REQUEST_URI"]))[0], array("/bc-spadmin/Login.php", "/bc-spadmin/PasswordRecovery.php"))){
				$redirecturl = trim($_SERVER["REQUEST_URI"]);
				if(!empty(trim($redirecturl)) && file_exists("..".$redirecturl)){
					header("Location: /bc-spadmin/Login.php?redirecturl=".$redirecturl);
				}else{
					header("Location: /bc-spadmin/Login.php");
				}
			}
		}
	}else{
		header("Location: /bc-spadmin/Error.php");
	}
	}else{
		if(!in_array(explode("?",trim($_SERVER["REQUEST_URI"]))[0], array("/saSetup.php"))){
			header("Location: /saSetup.php");
		}
	}
	}else{
		//If Database Is Having Issue
		if(!in_array(explode("?",trim($_SERVER["REQUEST_URI"]))[0], array("/dbSetup.php"))){
			header("Location: /dbSetup.php");
		}
	}

	//CSS Template Update
    $css_style_template_location = "/cssfile/template/bc-style-template-1.css";
    $select_spadmin_style_template = mysqli_query($connection_server, "SELECT * FROM sas_spadmin_style_templates");
    if(mysqli_num_rows($select_spadmin_style_template) == 1){
        $get_spadmin_style_template = mysqli_fetch_array($select_spadmin_style_template);
        $style_template_name = $get_spadmin_style_template["template_name"];
        if(!empty($style_template_name)){
            $style_template_location = "/cssfile/template/".$style_template_name;
			if(file_exists("..".$style_template_location)){
				$css_style_template_location =  $style_template_location;
			}
        }
    }
    
	/*if(emailTemplateTableExist('student-reg','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'student-reg', 'Student Registration', 'Hello {{student_name}} ,\n\nYour registration has been successful with {{school_name}}. You can now access your account. \n\nUser Name : {{user_name}}\nClass Name : {{class_name}}\nEmail : {{email}}\n\n\nRegards From {{school_name}}.')");
	}
	if(emailTemplateTableExist('add-user','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'add-user', 'Your have been assigned role of {{role}} in {{school_name}}.', 'Dear {{user_name}},\n\n         You are Added by admin in {{school_name}} . Your have been assigned role of {{role}} in {{school_name}}.  You can sign in using this link. {{login_link}}\n\nUserName : {{username}}\nPassword : {{password}}\n\nRegards From {{school_name}}.')");
	}
	if(emailTemplateTableExist('fees-alert','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'fees-alert', 'Fees Alert', 'Dear {{parent_name}},\n\n        You have a new invoice.  You can check the invoice on your portal.\n.')");
	}
	if(emailTemplateTableExist('student-assign-teacher','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'student-assign-teacher', 'New Student has been assigned to you.', 'Dear {{teacher_name}},\n\n         New Student {{student_name}} has been assigned to you.\n \nRegards From {{school_name}}.')");
	}
	if(emailTemplateTableExist('student-assigned-teacher','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'student-assigned-teacher', 'You have been Assigned {{teacher_name}} at {{school_name}}', 'Dear {{student_name}},\n\n         You are assigned to  {{teacher_name}}. {{teacher_name}} belongs to {{class_name}}.\n \nRegards From {{school_name}}.')");
	}
	if(emailTemplateTableExist('attendance-absent','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'attendance-absent', 'Your Child {{child_name}} is absent today', 'Your Child {{child_name}} is absent today.\n\nRegards From {{school_name}}.')");
	}
	if(emailTemplateTableExist('payment-invoice','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'payment-invoice', 'Payment Received against Invoice', 'Dear {{student_name}},\n\n        Your have successfully paid your invoice {{invoice_no}}. You can check the invoice receipt on your portal.\n \nRegards From {{school_name}}.')");
	}
	if(emailTemplateTableExist('notice','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'notice', 'New Notice For You', 'New Notice For You.\n\nNotice Title : {{notice_title}}\n\nNotice Date  : {{notice_date}}\n\nNotice For  : {{notice_for}}\n\nNotice Comment :  {{notice_comment}}\n\nRegards From {{school_name}}\n')");
	}
	if(emailTemplateTableExist('holiday','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'holiday', 'Holiday Announcement', 'Holiday Announcement\n\nHoliday Title : {{holiday_title}}\n\nHoliday Date : {{holiday_date}}\n\nRegards From {{school_name}}\n')");
	}
	if(emailTemplateTableExist('school-bus','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'school-bus', 'School Bus Allocation', 'School Bus Allocation\n	\n	Route Name : {{route_name}}\n	\n	Vehicle Identifier : {{vehicle_identifier}}\n	\n	Vehicle Registration Number : {{vehicle_registration_number}}\n	\n	Driver Name : {{driver_name}}\n	\n	Driver Phone Number : {{driver_phone_number}}\n	\n	Driver Address : {{driver_address}}\n	\n	Route Fare  : {{route_fare}}\n	\n	Regards From {{school_name}}\n\n')");
	}
	if(emailTemplateTableExist('hostel-bed','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'hostel-bed', 'Hostel Bed Assigned', 'Hello {{student_name}} ,\n\n		You have been assigned new hostel bed in {{school_name}}.\n\nHostel Name : {{hostel_name}}\nRoom Number : {{room_id}}\nBed Number : {{bed_id}}\n\nRegards From {{school_name}}.')");
	}
	if(emailTemplateTableExist('subject-assigned','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'subject-assigned', 'New subject has been assigned to you', 'Dear {{teacher_name}},\n\nNew subject {{subject_name}} has been assigned to you.\n\nRegards From \n{{school_name}}')");
	}
	if(emailTemplateTableExist('issue-book','','verify') == false){
		mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'issue-book', 'New book has been issue to you', 'Dear {{student_name}},\n\nNew book {{book_name}} has been issue to you.\n\nRegards From \n{{school_name}}')");
	}*/
	
	//include("./email-design.php");
	
?>