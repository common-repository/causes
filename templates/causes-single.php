<?php
/*
 * @package:    	Causes
 * @Version:		1.0.0
 * @Author: 		Damodar Prasad
 * @Author URI: 	http://algothemes.com
 * @copyright:	 	Copyright (c) 2017, Algo Themes
 * @license:	    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 
 */
 
get_header();
?>
<!-- contact area -->	
			
		<?php do_action('algo_causes_hook_container_before');
					
					
								
						// Start the loop.
						while ( have_posts() ) : the_post();
						//single page data action
					 
							do_action('algo_causes_loop');

						// If comments are open or we have at least one comment, load up the comment template.					
						endwhile;			
					
					
				do_action('algo_causes_hook_page_content_after');
				
				get_sidebar( 'algo-cause-sidebar' );
									
			  
        
		
        do_action('algo_causes_hook_container_after');?>  
	
<!-- contact area  END -->
<?php get_footer(); ?>