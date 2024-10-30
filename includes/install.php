<?php
/**
 * cauese plugin activation hook  
 *
 * @package:	 	Cuases
 * @subpackage:	 	includes/install
 * @author:	 		Damodar Prasad
 * @version:	 	1.0.0
 */
 
 if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly
 
	/**
	* install donation system on plugin activation	*/
	
	function algo_causes_install() {	 
	global $wpdb;	
	$table_name = $wpdb->prefix . 'causes_donations';	
	$charset_collate = $wpdb->get_charset_collate();
	// causes_donations
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		c_id mediumint(9)  NULL,
		donator mediumint(9) NULL,
		transactionId tinytext NULL,
		donation FLOAT(10,2) NULL,		
		time TIMESTAMP NOT NULL,
		is_complete  tinyint(1),
		session_id   tinytext NULL,		
		UNIQUE KEY id (id)
	) $charset_collate;";
	
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
	
	$table_name = $wpdb->prefix . 'causes_donators';	
	$sql2 = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9) NULL,
		total_donation FLOAT(10,2) NULL,
		date_time TIMESTAMP NOT NULL,
		first_name tinytext  NULL,
		last_name tinytext  NULL,
		email tinytext  NULL,
		phone tinytext  NULL,
		address MEDIUMTEXT NULL,
		paid tinyint(1),
		session_id tinytext NULL,		
		transactionId tinytext NULL,		
		UNIQUE KEY id (id)
	) $charset_collate;";
	
	dbDelta( $sql2 );
	
	//thank you page	
	$thankyoupage = array(
	  'post_type'	  =>'page',
	  'post_title'    => 'Donation Thank You',
	  'page-name'	  =>'donation-thank-you',
	  'post_content'  => '[donation-thankyou]',
	  'post_status'   => 'publish',
	  'post_author'   => 1  
	);
	$the_page = get_page_by_title( 'Donation Thank You' );
	if ( ! $the_page ) {
	wp_insert_post( $thankyoupage );
	}
    //checkout page
	$checkoutpage = array(
	  'post_type'	  =>'page',
	  'page-name'	  =>'donation-checkout',
	  'post_title'    => 'Donation Checkout',
	  'post_content'  => '[donation-checkout]',
	  'post_status'   => 'publish',
	  'post_author'   => 1  
	);

	// Insert the post into the database
	$checkoutPayapl_page = get_page_by_title( 'Donation Checkout' );
	if ( ! $checkoutPayapl_page ) {
		$checkout_page_id =wp_insert_post( $checkoutpage );
	}
	else{
		$checkout_page_id = $checkoutPayapl_page->ID ;
	}
	
	$checkout_page = get_post($checkout_page_id);
    $checkout_page_url = $checkout_page->guid;
    
    if (get_option('causes_checkout_url')=='') {
			
            update_option('causes_checkout_url', $checkout_page_url);
    }
	
	
	// Test paypal sandbox details update
	if(get_option('paypal_is_active')=='')
	  update_option('paypal_is_active','1');

	if(get_option('causes_paypal_sandbox_api_username')=='')
	update_option('causes_paypal_sandbox_api_username','dmdrprsd-facilitator-1_api1.gmail.com');
	
    if(get_option('causes_paypal_sandbox_api_password')=='')
	update_option('causes_paypal_sandbox_api_password','DJVC7ET27HMPVLZR');

    if(get_option('causes_paypal_sandbox_api_signature')=='')
    update_option('causes_paypal_sandbox_api_signature','AFcWxV21C7fd0v3bYYYRCpSSRl31AiM4MEH5s5tQCGdUReHW7PfWm6NM');
	
	/// currency settings
	if(get_option('causes_currency_codes')=='')
	update_option('causes_currency_codes','USD');

   if(get_option('causes_currendy_code_position')=='')
	update_option('causes_currendy_code_position','before');
	
	/// donation suggetions value
	if(get_option('causes_donate_suggestion_1')=='')
	update_option('causes_donate_suggestion_1','5');

	if(get_option('dcauses_donate_suggestion_2')=='')
	update_option('causes_donate_suggestion_2','10');

	if(get_option('causes_donate_suggestion_3')=='')
	update_option('causes_donate_suggestion_3','15');
	
	/// email default contents
	if(get_option('causes_donation_thankyou')=='')
	update_option('causes_donation_thankyou','Thank you for your donation!!!');

	if(get_option('causes_donation_email_subject')=='')
	update_option('causes_donation_email_subject','Thanks for donation');
	$str = 'Hi [DONATOR_NAME]

Thank you for donation:

[CAUSE_TITLE]

Best Regards';
	if(get_option('causes_donation_response_email')=='')
	update_option('causes_donation_response_email',$str);
	
}	
?>