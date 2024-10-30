<?php
/**
 * The template for displaying Causes Category archives
 * @Version:		1.0.0
 * @Author: 		Damodar Prasad
 * @package:    	Causes
 * @Author URI: 	http://algothemes.com
 * @copyright:	 	Copyright (c) 2017, Algo Themes
 * @license:	    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 
 */

get_header();


   do_action('algo_causes_hook_container_before');
   
   ?>
    <div id="cause-entries">
		<div class="row">
	<?php
   
   	$args = array ('posts_per_page' => 5,
					'post_type' 	=> 'cause',
					'meta_query'  	=> array(
										array(
										'key'       => 'causes_end_date',
										'value'     => date( 'Y-m-d' ),
										'compare'   => '>=',
										'type'      => 'datetime',
										)
									));
		$myposts = get_posts( $args );
		foreach( $myposts as $post ) :	setup_postdata($post);
		
		$thumbnail = algo_causes_get_post_thumbnail();
		
		//print_r($post);
		
		$classes	= array();
		$classes[]	= 'card-container';
		$classes[]	= 'col';
		$classes[]	= algo_causes_grid_class('4');
		//$classes[]	= 'col-'. $algo_count;
		?>
		 
		 <div id="#post-<?php the_ID(); ?>" <?php post_class( $classes ); ?> >
			<div class="causes-box element-box bg-white">
				<div class="thum-box">
				<?php echo $thumbnail; ?>
				</div>
				<?php include( PLUGIN_DIR_PATH.'templates/cause-entry-title.php' ); ?>
				<?php include( PLUGIN_DIR_PATH.'templates/cause-entry-progress.php' ); ?>
				<?php include( PLUGIN_DIR_PATH.'templates/cause-entry-excerpt.php' ); ?>
				
			</div>
			</div><!-- .card-container -->
<?php
		//Style Posts here
		 endforeach; 
		 ?>
		 </div>
 </div><!--causes entry-->
		 <?php
		   do_action('algo_causes_hook_container_after');?>  

<?php get_footer(); ?>