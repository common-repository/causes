<?php
/**
 * donation express checkout page
 *
 * @package:	 	Causes
 * @subpackage:	 	Includes/Exparess Checkout
 * @author:	 		Damodar Prasad
 * @version:	 	1.0.0
 */
 if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
 
 error_reporting(E_ALL);
 
 
$_POST = stripslashes_deep( $_POST );
$_GET = stripslashes_deep( $_GET );
$_REQUEST = stripslashes_deep( $_REQUEST );


require_once plugin_dir_path(__DIR__) .'includes/paypal/CallerService.php';
	
	if(!isset($_REQUEST['token'])){
		
		//$serverName = $_SERVER['SERVER_NAME'];
		//$serverPort = $_SERVER['SERVER_PORT'];
		$url=site_url();	
		$currencyCodeType = get_option('causes_currency_codes'); 
		
		$paymentType=urlencode('Sale');
	
		$personName        = $_POST['first_name']." ".$_POST['last_name'];
		$SHIPTOSTREET      = @$_POST['address1'];
		$SHIPTOCITY        = @$_POST['SHIPTOCITY'];
		$SHIPTOSTATE	   = @$_POST['SHIPTOSTATE'];
		$SHIPTOCOUNTRYCODE = @$_POST['SHIPTOCOUNTRYCODE'];
		$SHIPTOZIP         = @$_POST['SHIPTOZIP'];
		
		$returnURL	=	urlencode($url."/donation-checkout");
		$cancelURL	=	$returnURL;
	
		
		$nvpstr="";
	
		$shiptoAddress = "&SHIPTONAME=$personName&SHIPTOSTREET=$SHIPTOSTREET&SHIPTOCITY=$SHIPTOCITY&SHIPTOSTATE=$SHIPTOSTATE&SHIPTOCOUNTRYCODE=$SHIPTOCOUNTRYCODE&SHIPTOZIP=$SHIPTOZIP";
	
		$nvpstr="&ADDRESSOVERRIDE=1$shiptoAddress";
		$nvpstr.=$checkoutdetail;
		$nvpstr.="&CALLBACKTIMEOUT=4";
		$nvpstr.="&L_SHIPPINGOPTIONAMOUNT0=0.00";
		$nvpstr.="&L_SHIPPINGOPTIONNAME0=No shipping";
		$nvpstr.="&L_SHIPPINGOPTIONISDEFAULT0=true";
		$nvpstr.="&INSURANCEAMT=0.00";
		$nvpstr.="&INSURANCEOPTIONOFFERED=true";
		$nvpstr.="&CALLBACK=https://www.ppcallback.com/callback.pl";
		$nvpstr.="&SHIPPINGAMT=0.00";
		$nvpstr.="&SHIPDISCAMT=0.00";
		$nvpstr.="&TAXAMT=0.00";
		$nvpstr.="&L_DESC0=";
		$nvpstr.="&ReturnUrl=".$returnURL;
		$nvpstr.="&CANCELURL=".$cancelURL;
		$nvpstr.="&CURRENCYCODE=".$currencyCodeType;
		$nvpstr.="&PAYMENTACTION=".$paymentType;
		$nvpstr.="&LOCALECODE=US";		
		$nvpstr.="&BRANDNAME=".get_bloginfo( 'name' );
		$nvpstr.="&CUSTOM=".$donatorId;		
		
		$resArray=hash_call("SetExpressCheckout",$nvpstr);			
	
		$ack = strtoupper($resArray["ACK"]);
	
		if($ack=="SUCCESS"){			
			$token = urldecode($resArray["TOKEN"]);
			$payPalURL = PAYPAL_URL.$token.'&useraction=commit';		
			
			echo json_encode(array('loggedin'=>true, 'message'=> esc_html__('Please wait redirecting to payment.','causes'),'redirect'=>$payPalURL));
			die;
		}
		else{			
				$str = implode(" | ",$resArray);				
				echo json_encode(array('message'=> esc_html__('Kindly use another payment details, current details are not accepting by PayPal.<br>'.$str, 'causes')));
				die;
			}
	}
	else{		
			$token =urlencode( $_REQUEST['token']);
			
			$nvpstr="&TOKEN=".$token;
			
			$resArray=hash_call("GetExpressCheckoutDetails",$nvpstr);			
			
			$ack = strtoupper($resArray["ACK"]);

			if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING'){
				
				require_once plugin_dir_path(__DIR__) .'includes/paypal/GetExpressCheckoutDetails.php';						
			}// if error occured then follow
			else{
				
				$msg = '<h2 style="color:red" class="algo-blog-entry-title entry-title post-title">Failure in Processing the Payment</h2><h4>Note: Possible reasons for payment failure include: <ul><li>The billing address associated with the Financial Instrument could not be confirmed </li><li>
					The transaction exceeds the card limit</li><li>
					The transaction was denied by the card issuer
					</li>
					</ul>
					<br/>
					Please try again. <br/>
					
					</h4>';
						echo $msg;  
				
			}
				
	}