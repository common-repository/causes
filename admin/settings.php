<?php 
/*
 * Donation pament gateway settings 
 * @package		    Causes
 * @subpackage		Admin/settings
 * @author     		Damodar Prasad
 * @version    		1.0.0 
*/
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly


function causes_admin_tabs( $current = 'homepage' ) { 
    $tabs = array( 'causes-settings'=>esc_html__('Causes Settings','algo'),'paypal' => esc_html__('Paypal Settings','algo'), 'stripe' => esc_html__('Stripe Settings','algo'),'donation' => esc_html__('Thank You Page','algo'), 'mail' => esc_html__('Donation E-Mail','algo')); 
    $links = array();
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ){
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?post_type=cause&page=settings&tab=$tab'>$name</a>";
        
    }
    echo '</h2>';
}
?>

<div class="wrap">
<div class="donations_header"><h2><?php echo esc_html__('Causes Settings','algo'); ?></h2>

<?php
	if ( isset($_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] )  ) echo '<div class="updated" ><p>Settings updated.</p></div>';
			
	if ( isset ( $_GET['tab'] ) ) causes_admin_tabs($_GET['tab']); else causes_admin_tabs('paypal');
		
	if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab']; 
		else $tab = 'paypal'; 
		
		?>
</div>

<div class="donations_wrap">
<form method="post" action="<?php echo admin_url(); ?>edit.php?post_type=cause&page=settings&tab=<?php echo $tab ?>">
<?php wp_nonce_field( "causes-settings-page" ); ?>
<table class="wp-list-table widefat fixed striped posts" >
	<tbody >
	<tr >
	<td>
	
	<table  class="form-table">
	<tbody>
	
		<?php 
		switch ( $tab ){
		
		case 'causes-settings':
		?>
			
			<tr>
			<th><label><?php echo esc_html__('Checkout Result Page URL:','algo'); ?></label></th>			
			<td>
			
			<input type="text" name="causes_settings[causes_checkout_url]" size="100" style=" height:30px; padding:5px;" value="<?php echo get_option('causes_checkout_url' );?>" >  <p>This page is automatically created for you when you install the plugin. Do not delete this page as the plugin will send the customer to this page after the payment.</p>
			</td>
			</tr>
			
			<tr >
				<th scope="row"><label><?php echo esc_html__('Currency ','algo'); ?> <label></th><td>				
					<?php					  
					    do_action('causes_currency');					                      
					?>                   
				</td>
			</tr>
			
			<tr >
				<th scope="row"><label><?php echo esc_html__('Currency Code Positions:','algo'); ?> <label></th><td>				
				<select name="causes_settings[causes_currendy_code_position]" id="currendy_code_position">
				<option value="before" <?php if( get_option('causes_currendy_code_position')=='before' ) { echo 'selected'; } ?>><?php echo esc_html__('Before Currency','algo'); ?></option>
				<option value="after" <?php if( get_option('causes_currendy_code_position')=='after' ) { echo 'selected'; } ?>><?php echo esc_html__('After Currency','algo'); ?></option>
				</select>				
			</tr>

			
			
			<tr>		
				<th class="manage-column column-title column-primary sortable asc" id="title" scope="col"><h2><?php echo esc_html__(' Donation Suggested Amounts','algo'); ?></h2></th>		
			</tr>
			
			<tr>
				<th><label><?php echo esc_html__('Suggestion  Amount 1:','algo'); ?> <label></th><td>
				<input type="text" name="causes_settings[causes_donate_suggestion_1]" style=" height:30px; padding:5px;" size="20" value="<?php echo esc_attr( get_option('causes_donate_suggestion_1') ); ?>"></td>
			</tr>
			
			<tr>
				<th><label><?php echo esc_html__('Suggestion Amount 2:','algo'); ?> <label></th><td>
				<input type="text" name="causes_settings[causes_donate_suggestion_2]" style=" height:30px; padding:5px;" size="20" value="<?php echo esc_attr( get_option('causes_donate_suggestion_2') ); ?>"></td>
			</tr>
			
			<tr>
				<th><label><?php echo esc_html__('Suggestion Amount 3:','algo'); ?> <label></th><td>
				<input type="text" name="causes_settings[causes_donate_suggestion_3]" style="height:30px; padding:5px;" size="20" value="<?php echo esc_attr( get_option('causes_donate_suggestion_3') ); ?>"></td>
			</tr>
			
		<?php
		break;			
		case 'paypal' :
		?>
		<tr>	
		<th class="manage-column column-title column-primary sortable asc" id="title" scope="col"><h2><?php echo esc_html__('Paypal Live Details','algo'); ?></h2></th>		
		</tr>			
			
			<tr>
			<th><label><?php if(get_option('paypal_is_active')==1) {echo esc_html__('Active:','algo');} else{ echo esc_html__('Activate:','algo');} ?></label></th><td>
			<?php settings_fields('paypal_api'); ?>
				<?php do_settings_sections('paypal_api');?>
			<input type="hidden" name="causes_settings[paypal_is_active]" value="0" />
			<input type="checkbox" name="causes_settings[paypal_is_active]" value="1" <?php if(get_option('paypal_is_active')==1) echo "checked";?>> <p class="description"><?php echo esc_html__('Check this checkbox to activate paypal payment method.','algo'); ?> </p>
			</td>
			</tr>
			
			
			<tr>
			<th><label><?php echo esc_html__('Live/Sandbox:','algo'); ?></label></th><td>
			<?php settings_fields('paypal_api'); ?>
				<?php do_settings_sections('paypal_api');?>
				
			<input type="hidden" name="causes_settings[enable_live]" value="0" />
			<input type="checkbox" name="causes_settings[enable_live]" value="1" <?php if(get_option('enable_live')==1) echo "checked";?>> <p class="description"><?php echo esc_html__('Check this to run the transaction in live mode. When unchecked it will run in test mode.','algo'); ?> </p>
			</td>
			</tr>
			
			<tr>
				<th><label><?php echo esc_html__('API USERNAME:','algo'); ?> <label>
				</th><td>
				<input type="text" name="causes_settings[causes_paypal_live_api_username]" size="50" style=" height:30px; padding:5px;"value="<?php echo esc_attr( get_option('causes_paypal_live_api_username') );
				?>"></td>
			</tr>
			
			<tr>
				<th><label><?php echo esc_html__('API PASSWORD:','algo'); ?> <label></th><td>
				<input type="text" name="causes_settings[causes_paypal_live_api_password]" size="50" style="height:30px; padding:5px;"value="<?php echo esc_attr( get_option('causes_paypal_live_api_password') ); ?>"></td>
			</tr>
			
			<tr>
				<th><label><?php echo esc_html__('API SIGNATURE:','algo'); ?> <label></th><td>
				<input type="text" name="causes_settings[causes_paypal_live_api_signatur]" size="50" style="height:30px; padding:5px;"value="<?php echo esc_attr( get_option('causes_paypal_live_api_signatur') ); ?>"></td>
			</tr>
			
			<tr>		
				<th class="manage-column column-title column-primary sortable asc" id="title" scope="col"><h2><?php echo esc_html__('Paypal Sanbox Details','algo'); ?></h2></th>		
			</tr>
		
			<tr>
				<th><label><?php echo esc_html__('API USERNAME:','algo'); ?> <label></th><td>
				<input type="text" name="causes_settings[causes_paypal_sandbox_api_username]" size="50" style=" height:30px; padding:5px;" value="<?php echo esc_attr( get_option('causes_paypal_sandbox_api_username') );
				

				?>"></td>
			</tr>
			
			<tr>
				<th><label><?php echo esc_html__('API PASSWORD:','algo'); ?> <label></th><td>
				<input type="text" name="causes_settings[causes_paypal_sandbox_api_password]" size="50" style="height:30px; padding:5px;" value="<?php echo esc_attr( get_option('causes_paypal_sandbox_api_password') ); ?>"></td>
			</tr>
			
			<tr>
				<th><label><?php echo esc_html__('API SIGNATURE:','algo'); ?> <label></th><td>
				<input type="text" name="causes_settings[causes_paypal_sandbox_api_signature]" size="50" style="height:30px; padding:5px;" value="<?php echo esc_attr( get_option('causes_paypal_sandbox_api_signature') ); ?>"></td>
			</tr>
			
			
			
			
			<?php /*?>
				<tr>	
				<th class="manage-column column-title column-primary sortable asc" id="title" scope="col"><h2><?php echo esc_html__('Header Donate Now Button','algo'); ?></h2></th>		
				</tr>			
				<tr>
				<th><label><?php echo esc_html__('Button Text:','algo'); ?></label></th>				
				<td>							
				<input type="text" name="causes_settings[header_donate_now_text]" style="width:100%; height:30px; padding:5px;" value="<?php echo esc_attr( get_option('header_donate_now_text') ); ?>">
				</td>				
				</tr>
				
				<tr>
				<th><label><?php echo esc_html__('Button Enable/Disable:','algo'); ?></label></th>				
				<td>							
				<select name="causes_settings[header_donate_now]" id="header_donate_now">
				<option value="yes" <?php if( get_option('header_donate_now')=='yes' ) { echo 'selected'; } ?>><?php echo esc_html__('Enable','algo'); ?></option>				
				<option value="no" <?php if( get_option('header_donate_now')=='no' ) { echo 'selected'; } ?>><?php echo esc_html__('Disable','causes'); ?></option>
				</select>	
				</td>				
				</tr>
				
				<tr>
				<th><label><?php echo esc_html__('Url:','algo'); ?></label></th>				
				<td>							
					<input type="text" name="causes_settings[header_donate_now_url]" style="width:100%; height:30px; padding:5px;" value="<?php echo esc_attr( get_option('header_donate_now_url') ); ?>">
				</td>				
				</tr> <?php */?>
				
			<?php 
			
			break;
			case 'stripe' : 			
						
				settings_fields( 'CausesStripePayments-settings-group' );

				 do_settings_sections( 'causes_stripe_payment' ); 	
			
			break;
			case 'donation' : 
			?>
			<tr>		
				<th class="manage-column column-title column-primary sortable asc" id="title" scope="col"><h2><?php echo esc_html__('Donation Thank You Page Data','algo'); ?></h2>
				<p><?php echo esc_html__('Please enter here a message for thank you page.','algo');?></p>
				</th>		
			</tr>

			<tr>
		
			<td colspan="2">
			<?php
			
			$args = array(
			'textarea_name' => 'causes_settings[causes_donation_thankyou]'		
			); 
			wp_editor( html_entity_decode( get_option('causes_donation_thankyou')), 'causes_donation_thankyou', $args );
			?>
			</td>
			</tr>	

			<?php
			break;
			case 'mail' : 
			?>
			
		<tr>		
			<th class="manage-column column-title column-primary sortable asc" id="title" scope="col" colspan="2"><h2><?php echo esc_html__('Donator Email Message','algo'); ?></h2>
			
			</th>		
		</tr>
			<tr>
				<th><label><?php echo esc_html__('Email Subject:','algo'); ?> <label></th><td>
				<input type="text" name="causes_settings[causes_donation_email_subject]" size="60" style="height:30px; padding:5px;" value="<?php echo esc_attr( get_option('causes_donation_email_subject') ); ?>"></td>
			</tr>
		<tr>		
			<td colspan="2">
			
				<?php
				// Use nonce for verification
				wp_nonce_field( plugin_basename( __FILE__ ), 'causes_noncename' );
  
				$args = array(
				'textarea_name' => 'causes_settings[causes_donation_response_email]'			
				);
	 
				wp_editor( html_entity_decode(get_option('causes_donation_response_email')), 'causes_donation_response_email', $args );
				?>
				<span class="message notice notice-warning"><strong>This is the body of the email that will be sent to the donor. Do not change the text within the braces []. You can use the following email tags in this email body field:</strong><br>[DONATOR_NAME] Donor Name<br>[CAUSE_TITLE] Campaign Title</span>
			</td>
		</tr>		
			<?php			
			break;			
			}
			?>
			<tr>
				<td>
					<input type="submit" value="Update" class="button">
			
				</td>
			</tr>
			
		
			
	</thead>
	</table>
	</td></tr></tbody>
</table>
</form>
</div>
</div>	