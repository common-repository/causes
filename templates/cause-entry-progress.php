<?php
/**
 * Cause entry Progress template part
 * @version     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Return if disabled via the Customizer
if ( ! get_theme_mod( 'causes_entry_progress', true ) ) {
	return;
}

?>
<?php
global $wpdb;
$table_donations = $wpdb->prefix . 'causes_donations';
$donators = $wpdb->get_results( "SELECT SUM(donation) as total_donation FROM ".$table_donations." WHERE  c_id='".$id."' and is_complete=1" );

$goal = get_post_meta($id,'causes_goal',true); 

if($goal!=0):
$status=$donators[0]->total_donation/$goal*100;	
	else:
$status=0;
endif;
?>
	<div class="donation-status padding-20">
    <div class="status-col">
    <strong><?php echo round($status,2); ?><span><?php echo esc_html__('%','algo'); ?></span></strong>
    <b><?php echo esc_html__('Donation','algo'); ?></b>
    </div>
    <div class="status-col">
    <strong><?php echo algo_causes_currency_position($goal);  ?></span></strong>
    <b><?php echo esc_html__('Needed','algo');?></b>
    </div>
    </div>                              
    
    
                                
