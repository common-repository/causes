<?php
/*
 * Causes donation list in admin panel
 * @package    		Causes 
 * @subpackage 		Causes/donation
 * @author     		Damodar Prasad
 * @version   		1.0.0
*/
?>
<div class="wrap">
<div class="donations_header"> <h2><?php echo esc_html__('Donations','algo'); ?></h2> </div>

<div class="donations_wrap">

<table class="wp-list-table widefat  striped posts">
	<thead>
		<tr>	
			<th class="manage-column column-title" id="donor" scope="col"><span><?php echo esc_html__('Donator','algo'); ?></span></th>
			<th class="manage-column column-shortcode" id="total-donation" scope="col"><span><?php echo esc_html__('Amount','algo'); ?></span></th>
			<th class="manage-column column-author" id="title" scope="col"><span><?php echo esc_html__('Cause(s)','algo'); ?></span></th>
			<th class="manage-column column-author" id="date" scope="col"><span><?php echo esc_html__('Date/Time','algo'); ?></span></th>
			<th class="manage-column column-date" id="status" scope="col"><span><?php echo esc_html__('Status','algo'); ?></span></th>
		</tr>
	</thead>
	<?php	
		global $wpdb,$post;	

		

		
        $table_donations = $wpdb->prefix . 'causes_donations';		
		$donators = $wpdb->get_results( "SELECT * FROM ".$table_donations." cd INNER JOIN ".$wpdb->prefix."causes_donators cdr on cd.donator=cdr.id  WHERE  cd.is_complete=1" );		
		foreach($donators as $donator):					
		?>
			<tr>				
				<td><?php if(isset($donator->first_name)) { echo $donator->first_name." ".$donator->last_name; } ?></td>
				<td><?php echo algo_causes_currency_position($donator->donation);  ?></td>
				<td>
				<?php							
					$causesPost = get_post( $donator->c_id );
					echo $causesPost->post_title;				
				?>
				</td>							
				<td><?php echo date_i18n( 'F d, Y H:i:s', strtotime( $donator->time ) ); ?></td>
				<td><?php echo esc_html__('Complete','algo'); ?></td>
			</tr>
		<?php
		endforeach;					
    ?>
</table>
</div>
</div>