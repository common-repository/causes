<?php
/**
 * cauese donation class
 *
 * @package:	 	Cuases
 * @subpackage:		causes/donation
 * @author:	 		Damodar Prasad
 * @version:	 	1.0.0
 * @copyright 		Copyright (c) 2017, Algo Themes
 * @license     	http://opensource.org/licenses/gpl-2.0.php GNU Public License
 
 */
 
 if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly 
//donation object
global $donationObj ;

if ( ! class_exists( 'CausesDonation' ) ) :
/**
 * donation class
 */
Class CausesDonation{
	
	public $session;
	
	function __construct(){			
		
		require_once( plugin_dir_path(__DIR__).'includes/session/causes-session.php' );
		
		 $wpsession = WP_Session::get_instance();	
			
		 		
	    $this->session = $wpsession ;
		
		//add menu in admin bar
		add_action( 'admin_menu', array($this , 'algo_causes_add_menu')) ;
		
		/// 	
		add_action( 'admin_head', array($this, 'algo_causes_save_settings') );	
				
		//Add Ajax actions
		add_action('wp_ajax_algo_causes_add_donation', array($this ,'algo_causes_add_donation'));
		add_action('wp_ajax_nopriv_algo_causes_add_donation', array($this ,'algo_causes_add_donation'));
		
		//add donator detail and go to checkout 
		add_action('wp_ajax_algo_causes_checkout', array($this,'algo_causes_checkout'));
		add_action('wp_ajax_nopriv_algo_causes_checkout', array($this , 'algo_causes_checkout'));
		
		//donation page shortcode 
		add_shortcode( 'donation-checkout', array($this,'algo_causes_donation_checkout'));
		
		//add filter for query vars
		add_filter( 'query_vars', array($this,'algo_causes_query_vars_filter') );
		
		// print script in footer
		if ( ! is_admin() && !stristr( $_SERVER['REQUEST_URI'], 'wp-login' )  ) :
		
		add_action( 'wp_print_footer_scripts' ,array(&$this,'algo_causes_donatenow_script'),2 );
		
		//page popup
		add_action( 'wp_print_footer_scripts', array(&$this,'algo_causes_donate_popup' ),2);
		
		add_action('wp_head',array($this,'algo_causes_cart_actions'),1);	
			
		endif;
		
	}
	


/**
 * Add submenu page
 */
function algo_causes_add_menu(){
add_submenu_page(
			'edit.php?post_type=cause',
			'Donations',
			esc_html__('Donations','algo'),
			'manage_options',
			'donations',
			array($this,'algo_causes_admin_donations_list')	
			
		 );
add_submenu_page(
			'edit.php?post_type=cause',
			'Settings',
			esc_html__('Settings','algo'),
			'manage_options',
			'settings',
			array($this,'algo_causes_settings')				
		 );
}

/**
 * Save causes settings
 */
function algo_causes_save_settings(){
	
	if(isset($_POST['_wpnonce']) && $_POST['_wpnonce']!=''){
	//register our settings
	if(isset($_GET['tab']) && ($_GET['tab']=='paypal' || $_GET['tab']=='donation' || $_GET['tab']=='mail' || $_GET['tab']=='postEditor')){		
		
		foreach($_POST['causes_settings'] as $key=>$settings){
			
			$updated = update_option( $key, $settings );			
		}		
			$url_parameters = isset($_GET['tab'])? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
			wp_redirect(admin_url('edit.php?post_type=cause&page=settings&'.$url_parameters));
		
		}
	
	}
	
}

/**
 * Settings 
 */
function algo_causes_settings(){
	require_once plugin_dir_path(__DIR__).'admin/settings.php';
}

/**
 * Donation List
 */
function algo_causes_admin_donations_list(){
	
	require_once plugin_dir_path(__DIR__).'admin/admin-donations-list.php';
}


/**
 * add to cart donation action
 */
function algo_causes_add_donation(){
	
	global $wpdb;
	
	
	$table_name = $wpdb->prefix . 'causes_donations';	
	
	$CID =  $_POST['CID'];
	$customdonation = $_POST['custom-donation'];
	$fixdonation = $_POST['fixed-donation'];
	
	$donationAmt =  $customdonation;
	
	if($customdonation==""):	
		$donationAmt = $fixdonation;
	endif;
	// checkif already in cart		
	$inserId = $wpdb->insert( 
	$table_name, 
	array( 
		'c_id' => $CID, 
		'donation' => $donationAmt ,
		'session_id' => $this->session->session_id, 
		'is_complete' => 0 
	), 
	array( 
		'%d', 
		'%f' , 
		'%s' , 
		'%d' 
	) 
);
	echo $inserId;

die;
}

/**
 * donation checkout
 */
function algo_causes_checkout(){ 
	
  global $wpdb;
     $donator_id ="";
    if(is_user_logged_in()){
		
	   $donator_id = get_current_user_id();	
	  
	}
    $table_name = $wpdb->prefix . 'causes_donators';	
	$email = sanitize_email($_POST['email']);
	$address1 = sanitize_text_field($_POST['address1']);
	$address2 = sanitize_text_field($_POST['address2']);
	$first_name = sanitize_text_field($_POST['first_name']);
	$last_name = sanitize_text_field($_POST['last_name']);
	$phone = sanitize_text_field($_POST['phone']);
	$total_donation = $_POST['total_donation'];
	
	$fulladdress = $address1." ".$address2;
	
	 $wpdb->insert( 
	$table_name, 
	array( 		
		'total_donation' => $total_donation ,
		'first_name' => $first_name, 
		'last_name' => $last_name ,
		'email' => $email ,
		'phone' => $phone ,
		'address' => $fulladdress, 
		'user_id' => $donator_id, 
		'session_id' => $this->session->session_id
		), 
		array( '%f','%s' ,'%s' , '%s' , '%s' , '%s' , '%s','%s') 
	);
	$donatorId = $wpdb->insert_id;
  
	$table_name = $wpdb->prefix . 'causes_donations';	
	$cartdata = $wpdb->get_results( "SELECT * FROM ".$table_name." WHERE session_id ='".$this->session->session_id."' and is_complete=0 " );
		
	$checkoutdetail="";
	$itemno=0;
	foreach($cartdata as $data):						
						
		$checkoutdetail.="&L_NAME".$itemno."=".get_the_title( $data->c_id );
		$checkoutdetail.="&L_AMT".$itemno."=".$data->donation;
		$checkoutdetail.="&L_QTY".$itemno."=1";
		         
		$itemno++;
	endforeach;	
			$checkoutdetail.="&AMT=".(string)$_POST['total_donation'];
			$checkoutdetail.="&ITEMAMT=".(string)$_POST['total_donation'];
			$checkoutdetail.="&MAXAMT=".(string)$_POST['total_donation'];
	
	
	require_once plugin_dir_path(__DIR__) .'includes/express-checkout.php';
	
	die;
	
}

/**
 * custom query var for url parameters
 */
function algo_causes_query_vars_filter( $vars ){
  $vars[] = "action";
  $vars[] = "cid";
  $vars[] = "token";
  $vars[] = "PayerID";
  return $vars;
}

/**
 * Cart Action Delete
 */
function algo_causes_cart_actions(){
	global $wpdb;	
	$action		=	get_query_var('action',1);
	$actionid	=	get_query_var('cid',1);
	
	if($action !='' && $action=='remove'):
	$table_name = $wpdb->prefix . 'causes_donations';	
	$wpdb->delete( $table_name, array( 'id' => $actionid,'session_id'=>$this->session->session_id ), array( '%d','%s' ) );	
	endif;	
}


/**
 * Donation checkout page
 */
function algo_causes_donation_checkout(){
	global $wpdb;	
	// return from paypal
	if(isset($_REQUEST['token']) && $_REQUEST['token']!=''):			
			require_once plugin_dir_path(__DIR__) .'/includes/express-checkout.php';
	  endif;
	  
	// end conditon of return from paypal	
	$table_name = $wpdb->prefix . 'causes_donations';	
	$cartdata = $wpdb->get_results( "SELECT * FROM ".$table_name." WHERE session_id ='".$this->session->session_id."' and is_complete=0 " );
	$total =0;
	ob_start();
	?>
				<div class="section-content">                
                	<div class="table-responsive">
						<?php if(count($cartdata ) > 0):?>
                        <table class="table table-bordered bg-white donation-table">                        
                            <thead>
                                <tr class="bg-primary">
                                    <th><?php echo esc_html__('Remove','algo'); ?></th>
                                    <th><?php echo esc_html__('Image','algo'); ?></th>
                                    <th><?php echo esc_html__('Name','algo'); ?></th>
                                    <th><?php echo esc_html__('Ammount','algo'); ?></th>
                                </tr>
                            </thead>
                            
                            <tbody>
							<?php							
							foreach($cartdata as $data):
							$total = $total + $data->donation;
								
							?>
                                <tr>
                                    <td class="cart-remove">
									<a href="<?php echo site_url();?>/donation-checkout/?action=remove&cid=<?php echo $data->id ?>" class="button btn-danger btn-xs">x</a></td>
                                    <td class="cart-thum"><?php if ( has_post_thumbnail($data->c_id)) :						
									    echo get_the_post_thumbnail( $data->c_id,array('100','100'),array('class'=>'cart-thum') ); 
									    endif;
									 ?> 
									</td>
                                    <td class="cart-name"><?php echo get_the_title( $data->c_id ); ?> </td>
                                    <td class="cart-price"><?php $currencyCode = get_option('causes_currendy_code_position'); ?><?php if($currencyCode=='before'){ do_action('causes_currency_code');  echo $data->donation; } else {  echo $data->donation; do_action('causes_currency_code');  } ?>
									
									</td>
                                </tr>
							<?php endforeach;?>	
                                
                            </tbody>
                            
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        <a href="<?php echo site_url();?>/causes" class="causes-button pull-left"><i class="fa fa-arrow-left"></i> <?php echo esc_html__('Continue Donation','algo'); ?></a>
                                        										
											<button class="causes-button btn-yellow pull-right" id="continue-doante"><?php echo esc_html__('Continue','algo'); ?> <i class="fa fa-arrow-right"></i></button>											
											
										
                                    </td>
                                </tr>
                                <tr>
                                	<td colspan="3">
                                    	<ul class="payment-icons">
                                        	<li><img src="<?php echo esc_url( plugin_dir_url(__DIR__).'assets' ); ?>/images/payment/discover.jpg" alt="Discover" title="Discover"></li>
                                            <li><img src="<?php echo esc_url( plugin_dir_url(__DIR__).'assets' ); ?>/images/payment/mastercard.jpg" alt="Mastercard" title="Mastercard"></li>
                                            <li><img src="<?php echo esc_url( plugin_dir_url(__DIR__).'assets' ); ?>/images/payment/payment.jpg" alt="American Express" title="American Express"></li>
                                            <li><img src="<?php echo esc_url( plugin_dir_url(__DIR__).'assets' ); ?>/images/payment/paypal.jpg" alt="Paypal" title="Paypal"></li>
                                        </ul>
                                    </td>
                                    <td class="cart-total-row text-right">
                                        <span class="cart-text"><?php echo esc_html__('Total:','algo'); ?></span>
                                        <span class="cart-total text-primary"><?php if($currencyCode=='before'){ do_action('causes_currency_code');  echo $total; } else {  echo $total; do_action('causes_currency_code');  } ?></span>
                                    </td>
                                </tr>
                            </tfoot>
                            
                        </table>
						<?php else:?>
						<div class="page-notfound text-center">
						<span><?php echo esc_html__('Donations cart empty!','algo'); ?> <br></span>
						
						<a class="algo-button" href="<?php echo site_url(); ?>/causes"><i class="fa fa-arrow-left"></i> &nbsp; <?php echo esc_html__('GO TO Causes','algo'); ?></a>
						</div>
						<?php endif;?>
                    </div>
					
					<?php
					$first_name;$last_name;$email;
					if(is_user_logged_in()){
							$current_user = wp_get_current_user();
							$email=	$current_user->user_email ;
							$first_name=	$current_user->user_firstname ;
							$last_name=	$current_user->user_lastname ;
					}
					?>
					
					<div class="personal-info" style="display:none;">
                    	<h4><?php echo esc_html__('Personal Info','algo'); ?></h4>
                        <div class="padding-30 bg-white clearfix margin-b-30">
						 <div id="user-alredy-use"></div>
						 
                            <form method="post" id="personal-info" >
							<input type="hidden" name="total_donation" value="<?php echo $total;?>" />
							                
                                <div class="row">								
									
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                <input name="first_name" type="text" required class="form-control" placeholder="First Name" value="<?php echo $first_name;?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                                <input name="last_name" type="text" required class="form-control"  placeholder="Last Name" value="<?php echo $last_name;?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                                <input name="email" type="email"class="form-control" required  placeholder="Email" value="<?php echo $email;?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                                <input name="phone" type="text"class="form-control" required  placeholder="Phone">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                                <input name="address1" type="text"class="form-control" required  placeholder="Address 1">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                                <input name="address2" type="text"class="form-control" required  placeholder="Address 2">
                                            </div>
                                        </div>
                                    </div>
								
									
									<div class="col-md-12">
                                        <input name="submit" type="submit" value="Donate" class="algo-button margin-r-10">
										<i style="display:none" id="loading" class="fa fa-spin fa-spinner font-size-20"></i>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
				</div>
	<?php
	$output = ob_get_contents();
	ob_end_clean();
    return  $output ;
}

/**
 * add donate now script
 */
function algo_causes_donatenow_script(){
	?>
	 <script type="text/javascript" >
	 // model
jQuery('document').ready(function(){	

	jQuery('#donate-now').on('shown.bs.modal', function (event) { 
   
  var button = jQuery(event.relatedTarget) // Button that triggered the modal
  var cuaseID = button.data('cid') // Extract info from data-* attributes
  // If necessary, you could initiate an AJAX request here (and then do the updating in a callback). 
 // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead. 
  var modal = jQuery(this)
  modal.find('.modal-body input#CID').val(cuaseID)  
  
});

jQuery('#confirm').on('show.bs.modal', function (event){ jQuery('#donate-now').modal('hide');  }); 

jQuery('#confirm').on('shown.bs.modal', function (event){ jQuery("body").addClass('modal-open');  }); 

			// submit donation form
			var donationform = jQuery( '#donation-form form' );
            
            jQuery('#submitdonation').on("click", function( e ) { 
					e.preventDefault();            
              
			    jQuery('#submitdonation').html('<i class="fa fa-spin fa-spinner font-size-20"></i>'); 	
 
            	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            	jQuery.post(causesLocalize.ajaxurl, donationform.serialize() + '&action=algo_causes_add_donation', function(response) { 
            	 
				// jQuery('#myPleaseWait').modal('hide');
								    
				  jQuery('#confirm').modal('show');   
				  jQuery('#confirm').modal('handleUpdate');   
				   jQuery('#submitdonation').html('Donate Now'); 
                   
				  
				   
            	});
            });
			
			/// Go To checkout button click event
			
			jQuery("#goToCheckout").on("click",function(){
				 jQuery('#goToCheckout').html('<i class="fa fa-spin fa-spinner font-size-20"></i>');
			});

			// scroll the page when show personal info form
			 jQuery("#continue-doante").on("click",function(){			 
				
				//jQuery(window).scrollTop(jQuery('.donation-table').offset().top,1000);
				jQuery("html, body").delay(0).animate({
									scrollTop: jQuery('.donation-table').offset().top 
							}, 500);
				
				 jQuery(".personal-info").slideDown(1000); 
			 });
			 
			 
			//########### Submit To checkout form ############//
			
				jQuery('#continue-checkout').on("click", function( e ) {
				
					e.preventDefault();
					
				jQuery("#loading").show('fast');	
					
            	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            	jQuery.post(causesLocalize.ajaxurl, jQuery("#personal-info").serialize() + '&action=algo_causes_checkout', function(response) {
					
				if(response.loggedin == true){
						window.location.href=response.redirect;
				}
									
					else{							
							alert('Error occurred please try again.');
					}
					
				   
            	},"json");
					
				   
            	}
				);
			
			
			//########### Submit To checkout form ############//
			
			jQuery('#personal-info').on("submit", function( e ) {
					e.preventDefault();            
             
				jQuery("#loading").show('fast');
				
            	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            	jQuery.post(causesLocalize.ajaxurl, jQuery("#personal-info").serialize() + '&action=algo_causes_checkout', function(response) {
            	
				 if(response.loggedin == false){
						
						jQuery('#user-alredy-use').text(response.message);
						jQuery("#loading").hide('fast');
					}
				if(response.loggedin == true){
						jQuery('#user-alredy-use').text(response.message);
						window.location.href=response.redirect;
				}
						
					else{
							jQuery('#user-alredy-use').text(response.message);							
					}
					
				   
            	},"json");
				
				
				
            });
			
});			
	</script>
<?php	
}

/**
 * donation pop up model
 */
function algo_causes_donate_popup(){
	
	global $post;
?>	
	<!-- Donation Modal -->
        <div id="donate-now" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><?php echo esc_html__('X','algo'); ?></button>
                        <h4 class="modal-title"><?php echo esc_html__('Make a Donation','algo'); ?></h4>
                    </div>
                    <div class="modal-body donate-popup clearfix row" id="donation-form">
                        <form method="post">
                        
                        	<div class="col-md-4">
                            	<div class="text-center padding-tb-30 border-inr form-group">
								 <?php $currencyCode = get_option('causes_currendy_code_position');
                                       $box1     = get_option('causes_donate_suggestion_1') ? get_option('causes_donate_suggestion_1') : esc_html__( '9.99', 'algo' );  
                                       $box2     = get_option('causes_donate_suggestion_2') ? get_option('causes_donate_suggestion_2') : esc_html__( '29.99', 'algo' );  
                                       $box3     = get_option('causes_donate_suggestion_3') ? get_option('causes_donate_suggestion_3') : esc_html__( '99.99', 'algo' );  
								 ?>
                                    <div class="radio">
                                        <input id="low" type="radio" name="fixed-donation" value="<?php echo esc_attr( $box1 ); ?>" >
                                        <label for="low"><strong><?php echo esc_html__('Donate','algo'); ?></strong></label>
                                    </div>									
                                    <h1><?php if($currencyCode=='before'){ do_action('causes_currency_code');  echo esc_attr( $box1 ); } else {  echo esc_attr( $box1 ); do_action('causes_currency_code');  } ?></h1>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                            	<div class="text-center padding-tb-30 border-inr form-group">
                                    <div class="radio">
                                        <input id="mid" type="radio" name="fixed-donation" value="<?php echo esc_attr( $box2 ); ?>" >
                                        <label for="mid"><strong><?php echo esc_html__('Donate','algo'); ?></strong></label>
                                    </div>
                                   <h1><?php if($currencyCode=='before'){ do_action('causes_currency_code');  echo esc_attr( $box2 ); } else {  echo esc_attr( $box2 ); do_action('causes_currency_code');  } ?></h1>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                            	<div class="text-center padding-tb-30 border-inr form-group">
                                    <div class="radio">
                                        <input id="high" type="radio" name="fixed-donation" value="<?php echo esc_attr( $box3 ); ?>" checked>
                                        <label for="high"><strong><?php echo esc_html__('Donate','algo'); ?></strong></label>
                                    </div>
                                    <h1><?php if($currencyCode=='before'){ do_action('causes_currency_code');  echo esc_attr( $box3 ); } else {  echo esc_attr( $box3 ); do_action('causes_currency_code');  } ?></h1>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                	<label><?php echo esc_html__('Custom donation amount','algo'); ?></label>
                                    <div class="input-group">
                                        <i class="input-group-addon fa "><?php do_action('causes_currency_code'); ?></i>
                                        <input name="custom-donation" type="text" class="form-control" placeholder="Enter Amount">
                                        <input name="CID" type="hidden" id="CID">
										
                                        <span class="input-group-btn">
                                        	<button type="button" class="causes-button" id="submitdonation"><?php echo esc_html__('Donate Now','algo'); ?></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                    <div class="modal-footer bg-gray"></div>
                </div>
            </div>
        </div>
        <!-- Modal END--> 

		<!--Confirm Modal -->
        <div id="confirm" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><?php echo esc_html__('X','algo'); ?></button>
                        <h4 class="modal-title"><?php echo esc_html__('Donation Added Successfully.','algo'); ?></h4>
                    </div>
                    <div class="modal-body donate-popup clearfix row" id="donation-forms">
                                                   
                            <div class="col-md-12">
                                <div class="form-group">
                                	<label><?php echo esc_html__('Do you want more donation or continue to checkout?','algo'); ?></label>
									
							</div>
                            </div>	
								<div class="col-md-8">
                                    <div class="input-group">										
                                        <span class="input-group-btn">
                                        	<button type="button" class="causes-button" data-dismiss="modal" ><?php echo esc_html__('Continue','algo'); ?></button>
                                        </span>
									</div>
									</div>
									
									<div class="col-md-4">
									<div class="input-group">
										<span class="input-group-btn">
                                        	<a class="causes-button btn-yellow" id="goToCheckout" href="<?php echo site_url();?>/donation-checkout"><?php echo esc_html__('Go To Checkout','algo'); ?></a>
                                        </span>
                                  </div>  
                                  </div>                                
                            
                        
                    </div>
                    <div class="modal-footer bg-gray"></div>
                </div>
            </div>
        </div>
		  <!-- Modal END--> 
		  
		  <!-- Modal Start here-->
<div class="modal fade" id="myPleaseWait" tabindex="-1"
    role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <span class="glyphicon glyphicon-time">
                    </span><?php echo esc_html__('Please Wait','algo'); ?>
                 </h4>
            </div>
            <div class="modal-body">
                <div class="progress">
                    <div class="progress-bar progress-bar-info
                    progress-bar-striped active"
                    style="width: 100%">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal ends Here -->
<?php	
}
} // end class

endif; /// check end;

$donationObj  = new CausesDonation();
?>