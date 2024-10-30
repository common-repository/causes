<?php 
/*
 * @package:    	Causes
 * @Version:		1.0.0
 * @Author: 		Damodar Prasad
 * @Author URI: 	http://algothemes.com
 * @copyright:	 	Copyright (c) 2017, Algo Themes
 * @license:	    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 
 */
	
	//global objects	
	global $wpdb;
	
	//Get the id
	$id=get_the_ID();
	//Get the thumbnail
	//$thumbnail = algo_causes_get_post_thumbnail();
	
	add_filter( 'the_title', 'add_single_custom_post_titles');
	
	function add_single_custom_post_titles( $title ) {
		if( is_singular( 'cause' ) ):
		return single_post_title();
		else:
		return $title;
		endif;
	}
	
?>

	<div class="causes-detail  ">                                
    <div class="algo-causes-media thum-box">
		<?php
		
			
		//Query 
		$table_donations = $wpdb->prefix . 'causes_donations';
		
		$donators = $wpdb->get_results( "SELECT  SUM(donation) as total_donation FROM ".$table_donations." WHERE  c_id='".$id."' and is_complete=1" );	
		
		//Get goal from post meta
			$goal = get_post_meta($id,'causes_goal',true); 
					
			if($goal!=0):
				$status=$donators[0]->total_donation/$goal*100;	
			else:
				$status=0;
			endif;										
		?>									 
                                    
        <div class="overlay-black-dark padding-20">
			<div class="progress thin-bar">
				<div class="progress-bar bg-yellow progress-bar-striped active" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $status; ?>%"></div>
			</div><!-- .progress -->
			<div class="donation-status margin-b-30">
				<div class="status-col">
                <strong><?php echo round($status,2); ?><span><?php echo esc_html__('%','algo'); ?></span></strong>
					<b><?php echo esc_html__('Donation','algo'); ?></b>
				</div>
				<div class="status-col">
					<strong><?php echo algo_causes_currency_position($goal);  ?></strong>
				<b><?php echo esc_html__('Needed','algo'); ?></b>
				</div>
			</div><!-- .donation-status -->
		</div><!-- .overlay-black-dark -->
	</div><!-- .algo-causes-media -->
                                    
	<div class="algo-causes-info padding-b-30">                                    
		<div class="row">
        <div class="col-md-8 col-sm-8"><h2 class="title-bx padding-tb-10"><?php echo get_the_title(); ?></h2></div>
            <div class="col-md-4 col-sm-4 text-right">
            <div class="detail-btn">
				<a href="#" class="causes-button" data-cid="<?php echo $id; ?>" data-toggle="modal" data-target="#donate-now"><?php echo esc_html__('Donate Now','algo'); ?></a>
			</div>
            </div>
		</div>
                                        
        <div class="causes-single-content">
			<?php //the_content(); ?>     
		</div><!-- .causes-single-content -->                                    
	</div><!-- .causes-info -->                                    
				
								
	<?php // if(get_theme_mod('causes_comments')): ?> 
	<?php /*
		<div class="clear" id="comment-list">
			<div class="comments-area" id="comments">					
				<?php 			
					if ( comments_open() || get_comments_number() ) :
						//Comment template
						comments_template();			
					endif;										
				?>                                
			</div>                                
		</div><!--#comment-list-->	*/ ?>
		
	<?php //endif; ?>								
		<!-- Releted Post -->
		<?php   
				//do_action('algo_related_causes');
		?>  
</div><!-- .causes-detail -->							
						