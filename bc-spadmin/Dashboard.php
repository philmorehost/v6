<?php session_start();
    include("../func/bc-spadmin-config.php");
?>
<!DOCTYPE html>
<head>
    <title></title>
    <meta charset="UTF-8" />
    <meta name="description" content="" />
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
    <?php include("../func/bc-spadmin-header.php"); ?> 
    
  	<div class="pagetitle">
      <h1>DASHBOARD</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
        
        <!-- Row -->
        <div class="row">
          <!-- Col -->
          <div class="col-6 col-lg-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title"><?php echo strtolower($web_http_host); ?> <span>| Account Type</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6><?php echo accountStatus($get_logged_spadmin_details["status"]); ?></h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
          
           <!-- Col -->
          <div class="col-6 col-lg-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title">Manual Deposit <span>| Total</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6>₦<?php
                                $get_all_user_manual_credit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_transactions WHERE (type_alternative LIKE '%credit%' && description LIKE '%credit%' && description LIKE '%spadmin%')");
                                if(mysqli_num_rows($get_all_user_manual_credit_transaction_details) >= 1){
                                    while($transaction_record = mysqli_fetch_assoc($get_all_user_manual_credit_transaction_details)){
                                        $all_user_manual_credit_transaction += $transaction_record["discounted_amount"];
                                    }
                                    echo toDecimal($all_user_manual_credit_transaction, 2);
                                }else{
                                    echo toDecimal(0, 2);
                                }
                            ?></h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
          
           <!-- Col -->
          <div class="col-6 col-lg-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title">Deposit <span>| Total</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6>₦
                        <?php
                                $get_all_spadmin_credit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_transactions WHERE (type_alternative LIKE '%credit%' OR type_alternative LIKE '%received%' OR type_alternative LIKE '%commission%')");
                                if(mysqli_num_rows($get_all_spadmin_credit_transaction_details) >= 1){
                                    while($transaction_record = mysqli_fetch_assoc($get_all_spadmin_credit_transaction_details)){
                                        $all_spadmin_credit_transaction += $transaction_record["discounted_amount"];
                                    }
                                    echo toDecimal($all_spadmin_credit_transaction, 2);
                                }else{
                                    echo toDecimal(0, 2);
                                }
                            ?>
                        </h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
          
           <!-- Col -->
          <div class="col-6 col-lg-6">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title">Spent <span>| Total</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6>₦
                          <?php
                                $get_all_spadmin_debit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_transactions WHERE (type_alternative NOT LIKE '%credit%' && type_alternative NOT LIKE '%refund%' && type_alternative NOT LIKE '%received%' && type_alternative NOT LIKE '%commission%' && status NOT LIKE '%3%')");
                                if(mysqli_num_rows($get_all_spadmin_debit_transaction_details) >= 1){
                                    while($transaction_record = mysqli_fetch_assoc($get_all_spadmin_debit_transaction_details)){
                                        $all_spadmin_debit_transaction += $transaction_record["discounted_amount"];
                                    }
                                    echo toDecimal($all_spadmin_debit_transaction, 2);
                                }else{
                                    echo toDecimal(0, 2);
                                }
                            ?>
                        </h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
          
          
           <!-- Col -->
          <div class="col-12 col-lg-12">
            <div class="card info-card sales-card">
              <div class="card-body">
                <div class="card-body">
                    <h5 class="card-title">Manual Deposit <span>| Total</span></h5>
  
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-activity"></i>  
                      </div>
                      <div class="ps-3 overflow-hidden">
                        <h6>₦
                        <?php
                                $get_all_user_manual_credit_transaction_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_transactions WHERE (type_alternative LIKE '%credit%' && description LIKE '%credit%' && description LIKE '%spadmin%')");
                                if(mysqli_num_rows($get_all_user_manual_credit_transaction_details) >= 1){
                                    while($transaction_record = mysqli_fetch_assoc($get_all_user_manual_credit_transaction_details)){
                                        $all_user_manual_credit_transaction += $transaction_record["discounted_amount"];
                                    }
                                    echo toDecimal($all_user_manual_credit_transaction, 2);
                                }else{
                                    echo toDecimal(0, 2);
                                }
                            ?>
                        </h6>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <!-- Col End -->
        </div>  
          <!-- Row End -->
        
      </div>
    </section>
		
		<?php include("../func/spadmin-short-trans.php"); ?>
    <?php include("../func/bc-spadmin-footer.php"); ?>
    
</body>
</html>