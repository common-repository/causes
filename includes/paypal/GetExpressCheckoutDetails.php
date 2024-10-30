<?php
	
/********************************************************

********************************************************/

/* Collect the necessary information to complete the

   authorization for the PayPal payment

   */

/* Display the  API response back to the browser .

   If the response from PayPal was a success, display the response parameters

   */

/* Gather the information to make the final call to
   finalize the PayPal payment.  The variable nvpstr
   holds the name value pairs
   */
 
	$token =urlencode( $_REQUEST['token']);

	$paymentAmount =urlencode ($resArray['AMT']);
	$paymentType = urlencode('Sale');
	$currCodeType = urlencode(get_option('causes_currency_codes')); 
	$payerID = urlencode($_REQUEST['PayerID']);
	$serverName = urlencode($_SERVER['SERVER_NAME']);


	$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName.'&useraction=commit' ; 

	 /* Make the call to PayPal to finalize payment
		If an error occured, show the resulting errors  */	
		
	  $resArrayDone = hash_call("DoExpressCheckoutPayment",$nvpstr);


	/* Display the API response back to the browser.
	   If the response from PayPal was a success, display the response parameters'
	   If the response was an error, display the errors received using APIError.php.
	   */
	$ack = strtoupper($resArrayDone["ACK"]);


	if($ack != 'SUCCESS' && $ack != 'SUCCESSWITHWARNING'):
		
		$str = implode(" | ",$resArrayDone);
		
		?>
		<h2 style="color:red" class="algo-blog-entry-title entry-title post-title"><?php echo esc_html__('Failure in Processing the Payment','algo'); ?></h2><h4><?php echo esc_html__('Note: Possible reasons for payment failure include:','algo'); ?> <ul><li><?php echo esc_html__('The billing address associated with the Financial Instrument could not be confirmed ','algo'); ?></li><li>
	<?php echo esc_html__('The transaction exceeds the card limit','algo'); ?></li><li>
	<?php echo esc_html__('The transaction was denied by the card issuer','algo'); ?>
	</li>
	</ul>
	<br/>
	<?php echo esc_html__('Please use another payment method to complete your order.','algo'); ?> <br/>
	
	</h4>  
	<?php 
	
		else:
	
			$donatorID =$resArray['CUSTOM'];
			$transactionID =$resArrayDone['TRANSACTIONID'];
			
			// update donation table
			
			$table_name = $wpdb->prefix . 'causes_donations';		
			$wpdb->update( 
			$table_name ,
			array( 
				'donator' => $donatorID,	// string
				'is_complete' => 1,	// integer (number) 
				'transactionId' => $transactionID
				), 
			array( 'session_id' => $this->session->session_id ,'is_complete'=>0 ), 
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
			<?php echo algo_causes_currency_position($resArrayDone['AMT']);  ?>		
							 
			<a herf="<?php echo site_url();?>/donation-thank-you/">"Please wait...</a>
		
		<?php
endif;