<?php
/**
 * cause entry title template part
 * @version     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<?php 
		global $wpdb;
		$table_donations = $wpdb->prefix . 'causes_donations';
		
		$donators = $wpdb->get_results( "SELECT  SUM(donation) as total_donation FROM ".$table_donations." WHERE  c_id='".$id."' and is_complete=1" );	
				
		$goal = get_post_meta($id,'causes_goal',true); 

		if($goal!=0):
		$status=$donators[0]->total_donation/$goal*100;	
			else:
				$status=0;
		endif;
?>

<div class="progress thin-bar">
    <div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $status; ?>%"></div>
</div>

<div class="title-bx" ><h4 class="causes-title">
	
		<a href="<?php echo get_permalink($id); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>

</h4></div>
