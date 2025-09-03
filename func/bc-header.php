<?php
  $select_user_vendor_status_message = mysqli_query($connection_server, "SELECT * FROM sas_vendor_status_messages WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "'");
if (mysqli_num_rows($select_user_vendor_status_message) == 1) {
	$get_user_vendor_status_message = mysqli_fetch_array($select_user_vendor_status_message);
	if (!isset($_SESSION["product_purchase_response"]) && isset($_SESSION["user_session"])) {
		$user_vendor_status_message_template_encoded_text_array = array("{username}" => $get_logged_user_details["username"]);
		foreach ($user_vendor_status_message_template_encoded_text_array as $array_key => $array_val) {
			$user_vendor_status_message_template_text = str_replace($array_key, $array_val, $get_user_vendor_status_message["message"]);
			$user_vendor_status_message_template_date = formDate($get_user_vendor_status_message["date"]);
		}
	}
}
?>
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
      <a href="#" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block"><img src="<?php echo $web_http_host; ?>/uploaded-image/<?php echo str_replace(['.',':'],'-',$_SERVER['HTTP_HOST']).'_'; ?>logo.png" alt=""></span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
</div>


    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

       

        <li class="nav-item dropdown">

          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span class="badge bg-primary badge-number">1</span>
          </a><!-- End Notification Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
            <li class="dropdown-header">
              Latest Notification
              <!-- <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a> -->
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li class="notification-item">
              <i class="bi bi-exclamation-circle text-warning"></i>
              <div>
                <h4>Message from Admin</h4>
                <p><?php echo str_replace("\n", "<br/>", $user_vendor_status_message_template_text); ?></p>
                <p><?php echo str_replace("\n", "<br/>", $user_vendor_status_message_template_date); ?></p>
              </div>
            </li>
          </ul><!-- End Notification Dropdown Items -->

        </li><!-- End Notification Nav -->

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo'/asset/boy-icon.png'; ?>"" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">
              <?php echo $_SESSION["user_session"]; ?>
            </span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $_SESSION["user_session"]; ?></h6>
              
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo $web_http_host; ?>/web/APIDocs.php">
                <i class="bi bi-person"></i>
                <span>API Documentation</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="<?php echo $web_http_host; ?>/web/AccountSettings.php">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" onclick="javascript:if(confirm('Do you want to logout? ')){window.location.href='/logout.php'}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Log Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">
    <?php if(isset($_SESSION["user_session"]) && isset($get_logged_user_details)){ ?>
      <li class="nav-item">
        <a class="nav-link " href="<?php echo $web_http_host; ?>/web/Dashboard.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span> 
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/BankTransfer.php">
        <i class="bi bi-wifi"></i>
          <span>Bank Transfer</span>
        </a>
      </li><!-- End Bank Transfer Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#manage-fund-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Manage Funds</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="manage-fund-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/web/ShareFund.php">
              <i class="bi bi-circle"></i><span>Share Fund</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/web/ShareFundHistory.php">
              <i class="bi bi-circle"></i><span>Share Fund History</span>
            </a>
          </li>
          
        </ul>
        </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#payment-order-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Payment Order</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="payment-order-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/web/PaymentOrders.php">
              <i class="bi bi-circle"></i><span>Payment Orders</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/web/SubmitPayment.php">
              <i class="bi bi-circle"></i><span>Submit Payment</span>
            </a>
          </li>
          
        </ul>
      </li>
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#transaction-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Transactions</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="transaction-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/web/Transactions.php">
              <i class="bi bi-circle"></i><span>All Transactions</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/web/BatchTransactions.php">
              <i class="bi bi-circle"></i><span>Batch Transactions</span>
            </a>
          </li>
          
          <li>
            <a href="<?php echo $web_http_host; ?>/web/TransactionCalculator.php">
              <i class="bi bi-circle"></i><span>Transaction Calculator</span>
            </a>
          </li>
          
        </ul>
      </li>
     
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
        <i class="bi bi-bank2"></i><span>Add Money To Wallet</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/web/Fund.php">
              <i class="bi bi-circle"></i><span>With ATM Card (ATM)</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/web/VirtualBanks.php">
              <i class="bi bi-circle"></i><span>Bank-Deposit/Transfer</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/web/AutoFunding.php">
              <i class="bi bi-circle"></i><span>Auto Wallet Funding</span>
            </a>
          </li>
        </ul>

      </li><!-- End Tables Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/VirtualCard.php">
        <i class="bi bi-credit-card-2-front"></i>
          <span>Virtual Card</span>
        </a>
      </li><!-- End Profile Page Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/Data.php">
        <i class="bi bi-wifi"></i>
          <span>Buy Data Bundle</span>
        </a>
      </li><!-- End Profile Page Nav -->

      </li><!-- End Tables Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/Airtime.php">
        <i class="bi bi-phone-fill"></i>
          <span>Buy Airtime VTU</span>
        </a>
      </li><!-- End Profile Page Nav -->

      </li><!-- End Tables Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/BulkData.php">
        <i class="bi bi-wifi"></i>
          <span>Buy Bulk Data Bundle</span>
        </a>
      </li><!-- End Profile Page Nav -->

      </li><!-- End Tables Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/BulkAirtime.php">
        <i class="bi bi-telephone-fill"></i>
          <span>Buy Bulk Airtime VTU</span>
        </a>
      </li><!-- End Profile Page Nav -->

      </li><!-- End Tables Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/Cable.php">
        <i class="bi bi-tv"></i>
          <span>Buy CableTv Sub(s)</span>
        </a>
      </li><!-- End Profile Page Nav -->

      </li><!-- End Tables Nav -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/Electric.php">
        <i class="bi bi-lightbulb"></i>
          <span>Buy Electric Token</span>
        </a>
      </li><!-- End Profile Page Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/Betting.php">
        <i class="bi bi-tag"></i>
          <span>Fund Betting</span>
        </a>
      </li><!-- End Profile Page Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/Card.php">
        <i class="bi bi-card-checklist"></i>
          <span>Card Printing</span>
        </a>
      </li><!-- End Profile Page Nav -->
      
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#bulksms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-menu-button-wide"></i><span>Send SMS</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="bulksms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="<?php echo $web_http_host; ?>/web/BulkSMS.php">
              <i class="bi bi-circle"></i><span>Bulk SMS</span>
            </a>
          </li>
          <li>
            <a href="<?php echo $web_http_host; ?>/web/SubmitSenderID.php">
              <i class="bi bi-circle"></i><span>Submit SMS ID</span>
            </a>
          </li>
          
        </ul>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/Pricing.php">
        <i class="bi bi-tag"></i>
          <span>Account Pricing</span>
        </a>
      </li><!-- End Contact Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/Exam.php">
        <i class="bi bi-card-checklist"></i>
          <span>Buy Exam Pin(s) </span>
        </a>
      </li><!-- End Contact Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/AccountSettings.php">
        <i class="bi bi-key-fill"></i>
          <span>Account Settings</span>
        </a>
      </li><!-- End Contact Page Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="<?php echo $web_http_host; ?>/web/APIDocs.php">
        <i class="bi bi-code-slash"></i>
          <span>Developer API </span>
        </a>
      </li><!-- End Contact Page Nav -->

      </li><!-- End Contact Page Nav -->
      <?php } ?>
    </ul>

  </aside><!-- End Sidebar--> 
  
   <main id="main" class="main">