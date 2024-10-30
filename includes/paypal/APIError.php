<?php
/*************************************************
APIError.php

Displays error parameters.

Called by DoDirectPaymentReceipt.php, TransactionDetails.php,
GetExpressCheckoutDetails.php and DoExpressCheckoutPayment.php.

*************************************************/

session_start();
$resArray=$_SESSION['reshash']; 
?>

<html>
<head>
<title><?php echo esc_html__('PayPal API Error','boraj'); ?></title>
<link href="sdk.css" rel="stylesheet" type="text/css"/>
</head>

<body alink=#0000FF vlink=#0000FF>

<center>

<table width="280">
<tr>
		<td colspan="2" class="header"><?php echo esc_html__('The PayPal API has returned an error!','boraj'); ?></td>
	</tr>

<?php  //it will print if any URL errors 
	if(isset($_SESSION['curl_error_no'])) { 
			$errorCode= $_SESSION['curl_error_no'] ;
			$errorMessage=$_SESSION['curl_error_msg'] ;	
			session_unset();	
?>

   
<tr>
		<td><?php echo esc_html__('Error Number:','boraj'); ?></td>
		<td><?php echo $errorCode; ?></td>
	</tr>
	<tr>
		<td><?php echo esc_html__('Error Message:','boraj'); ?></td>
		<td><?php echo $errorMessage; ?></td>
	</tr>
	
	</center>
	</table>
<?php } else {

/* If there is no URL Errors, Construct the HTML page with 
   Response Error parameters.   
   */
?>

<center>
	<font size=2 color=black face=Verdana><b></b></font>
	<br><br>

	<b> <?php echo esc_html__('PayPal API Error','boraj'); ?></b><br><br>
	
    <table width = 400>
    	<?php 
    
    require get_template_directory().'/causes/donation/paypal/ShowAllResponse.php';
    ?>
    </table>
    </center>		
	
<?php 
}// end else
?>
</center>
	</table>
<br>
<a class="home"  id="CallsLink" href="index.html"><font color=blue><B><?php echo esc_html__('Home','boraj'); ?><B><font></a>
</body>
</html>

