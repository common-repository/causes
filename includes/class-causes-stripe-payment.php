<?php

 if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Gateway_Stripe class.
 *
 */
class Causes_Stripe_Payment{


	function __construct(){
		
		// Stripe library
		require_once plugin_dir_path(__DIR__).'includes/stripe/Stripe.php';		
		
		$params = array(
			"testmode"   => get_option('stripe_is_live'),
			"private_live_key" => get_option('stripe_api_secret_key'),
			"public_live_key"  => get_option('stripe_api_publishable_key'),
			"private_test_key" => get_option('stripe_api_secret_key'),
			"public_test_key"  => get_option('stripe_api_publishable_key')
		);

		if ($params['testmode'] == "0") {
			Stripe::setApiKey($params['private_test_key']);
			$pubkey = $params['public_test_key'];
		} else {
			Stripe::setApiKey($params['private_live_key']);
			$pubkey = $params['public_live_key'];
		}
		
	}
	
	/*
		*** Payment response 
	*/
	
	public static function strip_checkout_response(){
		
			$amount_cents = str_replace(".","",$_POST['amount']);;  // Chargeble amount
			$invoiceid = $_POST['order_id'];                      // Invoice ID
			$description = "Invoice #" . $invoiceid . " - " . $invoiceid;
			
		try {
			$charge = Stripe_Charge::create(array(		 
				  "amount" => $amount_cents,
				  "currency" => get_option('causes_currency_codes'),
				  "source" => $_POST['stripeToken'],
				  "description" => $description)			  
			);

			if ($charge->card->address_zip_check == "fail") {
				throw new Exception("zip_check_invalid");
			} else if ($charge->card->address_line1_check == "fail") {
				throw new Exception("address_check_invalid");
			} else if ($charge->card->cvc_check == "fail") {
				throw new Exception("cvc_check_invalid");
			}
			// Payment has succeeded, no exceptions were thrown or otherwise caught				

			$result = "success";

		} catch(Stripe_CardError $e) {			

		$error = $e->getMessage();
			$result = "declined";

			} catch (Stripe_InvalidRequestError $e) {
				$result = "declined";		  
			} catch (Stripe_AuthenticationError $e) {
				$result = "declined";
			} catch (Stripe_ApiConnectionError $e) {
				$result = "declined";
			} catch (Stripe_Error $e) {
				$result = "declined";
			} catch (Exception $e) {

				if ($e->getMessage() == "zip_check_invalid") {
					$result = "declined";
				} else if ($e->getMessage() == "address_check_invalid") {
					$result = "declined";
				} else if ($e->getMessage() == "cvc_check_invalid") {
					$result = "declined";
				} else {
					$result = "declined";
				}		  
			}
			if($result=='success')
			{
					global $wpdb , $donationObj;	
					
					$donatorID =$_POST['order_id'];
					$transactionID = $_POST['stripeToken'];
						
					$table_name = $wpdb->prefix . 'causes_donations';		
					$wpdb->update( 
					$table_name ,
					array( 
						'donator' => $donatorID,	// string
						'is_complete' => 1,	// integer (number) 
						'transactionId' => $transactionID
						), 
					array( 'session_id' => $donationObj->session->session_id ,'is_complete'=>0 ), 
					array( 
							'%s',	// value1
							'%d'	// value2
						), 
					array( '%s' ) 
				);
				
				$table_donations = $wpdb->prefix . 'causes_donators';		
				$donators = $wpdb->get_results( "SELECT * FROM ".$table_donations." cd LEFT JOIN ".$wpdb->prefix."causes_donations cdr on cd.id=cdr.donator  WHERE  cdr.donator=".$donatorID );				
				
				
					$cause_title="";
					foreach($donators as $don):
					$causesPost = get_post( $don->c_id );
					if(count($donators)>1){
					$cause_title .= $causesPost->post_title.", ";
					}else{
						
						$cause_title .= $causesPost->post_title;
					}
					endforeach;
					
					$to = $donators[0]->email;	
				
				
						
					$subj = get_option( 'causes_donation_email_subject' );
					
					$messageBody = get_option( 'causes_donation_response_email' );				
					$messageBody = str_replace('[DONATOR_NAME]', $donators[0]->first_name.' '.$donators[0]->last_name, $messageBody); 
					$messageBody = str_replace('[CAUSE_TITLE]', $cause_title, $messageBody); 
					$headers = "MIME-Version: 1.0\n" .
						"From: ".get_option( 'admin_email' )."\n" .
						"Content-Type: text/html; charset=\"" .
					get_option('blog_charset') . "\"\n";
					
					wp_mail( $to, $subj, $messageBody,$headers );
										
					?>	
		
					<script type="text/javascript" language="javascript">
					 function submitform(){
					 
					 //document.getElementById('doexpress').submit();
					 window.location.href="<?php echo site_url();?>/donation-thank-you/";
					 } 
					 </script>	 

						<body onLoad="submitform();">
						<?php $currencyCode = get_option('causes_currendy_code_position'); ?>			
						
							<?php echo esc_html__('Processing .....','algo'); ?>
							<?php echo algo_causes_currency_position(($_POST['amount']/100));  ?>		
											 
							<a herf="<?php echo site_url();?>/donation-thank-you/">"Please wait...</a>
						
		<?php
				
			}else
			{
				echo "<BR>Stripe Payment Status : ".$result;
			}
			
	}
		
		
}

 new Causes_Stripe_Payment();
