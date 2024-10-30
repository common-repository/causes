<?php
/**
 
 * @Version:		1.0.0
 * @Author: 		Damodar Prasad
 * @package:    	Causes
 * @Author URI: 	http://algothemes.com
 * @copyright:	 	Copyright (c) 2017, Algo Themes
 * @license:	    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 
 */
 
 function algo_causes_start_wrpaer(){
	 
	  echo '<div id="primary" class="section-content container  causes-content">';
 }
 
 function algo_causes_end_wrpaer(){
	 
	 echo '</div>';
 }

// Causes tax 
if ( ! function_exists( 'algo_causes_is_tax' ) ) {
 function algo_causes_is_tax() {
        if ( is_tax( 'cause-category' ) || is_tax( 'cause_tag' ) ) {
            return true;
        } else {
            return false;
        }

		
    }
}

/// causes category page
function get_cause_archive($archive_template) { 
    global $post;
 
    if (is_tax('cause-category') || is_tax('cause_tags')) {
         $archive_template = PLUGIN_DIR_PATH .  'templates/archive-cause-category.php';
    }
    return $archive_template;
}

add_filter( 'archive_template', 'get_cause_archive' );

/**
 * Get header donate button
 */
 if( !function_exists( 'algo_causes_donate_button_header' ) ) {
	function algo_causes_donate_button_header(){
		$default_path = plugin_dir_path(__DIR__) . 'templates/';
		load_template($default_path. 'donate_button_header.php',false);
		
	}
}

/**
 * Returns correct thumbnail HTML for the custom post entries
 */
function algo_causes_get_entry_thumbnail() {
    // Define thumbnail args
    $args = array(
        'size'  => 'cause_entry',
        'class' => 'cause-entry-img',
        'alt'   => algo_get_esc_title(),
    );

    // Apply filters
    $args = apply_filters( 'algo_cause_get_entry_thumbnail_args', $args );

    // Return thumbanil
    return algo_causes_get_thumbnail( $args );
}

/**
 * Returns correct thumbnail HTML for the causes post
 */
function algo_causes_get_post_thumbnail( ) {

    // Define thumbnail args
    $args = array(
        'size'  => 'cause_post',
        'class' => 'cause-single-media-img',
        'alt'   => algo_cause_get_esc_title(),
    );

    // Apply filters
    $args = apply_filters( 'algo_causes_get_post_thumbnail_args', $args );

    // Return thumbanil
    return algo_causes_get_thumbnail( $args );

}

function algo_causes_get_thumbnail($args){
	global $post;
	$defaults = array(
        'attachment'    => get_post_thumbnail_id(),
        'size'          => 'full',
        'width'         => '',
        'height'        => '',
        'crop'          => 'center-center',
        'alt'           => '',
        'class'         => '',
        'return'        => 'html',
        'style'         => '',
		'type'          => '',
    );
      
	  
	  // Parse args
    $args = wp_parse_args( $args, $defaults );

    // Extract args
    extract( $args );

    // Return if there isn't any attachment
    if ( ! $attachment ) {
        return;
    }
	
	// Image must have an alt
    if ( empty( $alt ) ) {
        $alt = get_post_meta( $attachment, '_wp_attachment_image_alt', true );
    }
    if ( empty( $alt ) ) {
        $alt = trim( strip_tags( get_post_field( 'post_excerpt', $attachment ) ) );
    }
    if ( empty( $alt ) ) {
        $alt = trim( strip_tags( get_the_title( $attachment ) ) );
        $alt = str_replace( '_', ' ', $alt );
        $alt = str_replace( '-', ' ', $alt );
    }
	
	 // Prettify alt attribute
    if ( $alt ) {
        $alt = ucwords( $alt );
    }
     
    // If image width and height equal '9999' return full image
    if ( '9999' == $width && '9999' == $height ) {
        $size   = $size ? $size : 'full';
        $width  = $height = '';
    }
	
	
		$image_size = apply_filters( 'causes_archive_thumbnail_size', $size );

		if ( has_post_thumbnail() ) {
			
			return get_the_post_thumbnail( $post->ID, $image_size);
		}
	
	
}

/**
 * Return escaped post title
 * @return  string
 */
 
function algo_cause_get_esc_title() { 
    return esc_attr( the_title_attribute( 'echo=0' ) );
}


/**
 * Get causes Single Page template
 **/
if( !function_exists( 'algo_causes_loop' ) ) { 
    function algo_causes_loop() { 
		
		$default_path = plugin_dir_path(__DIR__) . 'templates/';
		load_template($default_path. 'causes-single-content.php',false);
		
       
    }
}

/**
 * Get causes Single Page template
 **/
if( !function_exists( 'algo_causes_related_posts' ) ) { 
    function algo_causes_related_posts() { 
		
		$default_path = plugin_dir_path(__DIR__) . 'templates/';
		load_template($default_path. 'causes-single-related.php',false);
		
       
    }
}




function algo_cause_get_id(){
	
	// If singular get_the_ID
    if ( is_singular() ) {
        return get_the_ID();
    }
	
}

/**
 * check if cart is blank
 */
function algo_causes_header_donate_button(){ 
	global $wpdb ,$donationObj ;
	$table_name = $wpdb->prefix . 'causes_donations';	
	$cartCount= $wpdb->get_var( "SELECT COUNT(*) FROM $table_name where is_complete=0 and session_id='".$donationObj->session->session_id."'" );
	
	$button_text = get_option('header_donate_now_text'); //Button text			
	$button_text     = $button_text ? $button_text : esc_html__( 'Donate Now', 'algo' );			
	$button_url = get_option('header_donate_now_url');			
	if ($button_url && false === strpos($button_url, '://')) {
		$button_url = 'http://' . $button_url; //Buttn Url
	}	
	
	$button_url     = $button_url ? $button_url : esc_html__( site_url().'/causes', 'algo' );
	
	if($cartCount > 0):?>
			<a href="<?php echo site_url(); ?>/donation-checkout" class="causes-button-secondry"><i class="fa"><?php do_action('causes_currency_code'); ?></i>&nbsp;<?php echo esc_html__($button_text,'algo'); ?></a>
			<?php else:?>
			<a href="<?php echo $button_url; ?>" class="causes-button-secondry"><i class="fa"><?php do_action('causes_currency_code'); ?></i>&nbsp;<?php echo esc_html__($button_text,'algo'); ?></a>
			<?php endif;
}

/**
 * Algo Causes get meta value
 */
function algo_causes_get_meta_values( $key = '', $type = '', $status = 'publish' ) {
	global $wpdb;
	if( empty( $key ) )
	   return;
	$goal = $wpdb->get_col( $wpdb->prepare( "
	    SELECT pm.meta_value FROM {$wpdb->postmeta} pm
		LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE pm.meta_key = '%s'
		AND p.post_status = '%s'
		AND p.post_type = '%s'
		", $key, $status, $type ) );
		return $goal;
}

/**
	 * get currency code for custom post
	 */
	function algo_causes_get_currency_code(){		
		$currency_array = array(	
			'AUD' => '&#36;',	
			'CAD' => '&#36;',
			'CHF' => '&#67;&#72;&#70;',
			'CNY' => '&#165;',
			'EUR' => '&#8364;',
			'GBP' => '&#163;',
			'JPY' => '&#165;',
			'MYR' => '&#82;&#77;',
			'SGD' => '&#36;',
			'USD' => '&#36;',
		);
		 echo '<span>'.$currency_array[get_option('causes_currency_codes','USD')].'</span>';
	}
	
/**
 *Display currency with correct position
 *
*/

function algo_causes_currency_position($currency){
	
	 $currencyPose = get_option('causes_currendy_code_position');
	 $currencyCode = get_option('causes_currency_codes');
	 
	 if($currencyPose=='before'){
		  algo_causes_get_currency_code(); echo $currency;
	 }
	 else{
		 echo $currency; 
		 algo_causes_get_currency_code(); 
	 } 
		 
}

/**
 * Returns the correct classname for any specific column grid
 */
 
function algo_causes_grid_class( $col = '4' ) {
			if($col==1)
				$cols=12;
			if($col==2)
				$cols=6;
			if($col==3)
				$cols=4;
			if($col==4)
				$cols=3;
			if($col==5)
				$cols=5;
			if($col==6)
				$cols=2;
	
    $class = ' col-lg-'.$cols.' col-md-'. $cols.' col-sm-'. $cols;
    $class = apply_filters( 'algo_causes_grid_class', $class );
    return $class;
}



//** All Causess Short code **//
	function algo_causes_grid_shortcode($attr){
		global $wpdb;		
		$defaults = array(
						'post_type'		 => 'cause',
						'posts_per_page' => 8,
						'orderby'		 =>'post_date',
						'orderby'		 =>'post_date',
						'columns' 	     => 4,
						'meta_query'  	 => array(
									array(
										'key'       => 'causes_end_date',
										'value'     => date( 'Y-m-d' ),
										'compare'   => '>=',
										'type'      => 'datetime',
										)
									)										
						);
						
		$args   = shortcode_atts( $defaults, $attr, 'algo' );				
		
		
		echo '<div class="row">';	
		
		$the_query = new WP_Query($args);
		
		while ( $the_query->have_posts() ) : $the_query->next_post();
		$id= $the_query->post->ID;
		
		$content = $the_query->post->post_content.'</br>';
		
		$excerpt_length = get_theme_mod( 'causes_entry_excerpt_length', '15' );
		
		$content = wp_trim_words($content,$excerpt_length );
		
		$title = $the_query->post->post_title.'</br>';
		$goal = get_post_meta($id, 'causes_goal', true);
		$start_date = get_post_meta($id, 'causes_start_date', true);
		$end_date = get_post_meta($id, 'causes_end_date', true);
		
		
		//Query 
		$table_donations = $wpdb->prefix . 'causes_donations';
		
		$donators = $wpdb->get_results( "SELECT  SUM(donation) as total_donation FROM ".$table_donations." WHERE  c_id='".$id."' and is_complete=1" );	
		
		$percent=$donators[0]->total_donation/$goal*100;
		
		$url = wp_get_attachment_url( get_post_thumbnail_id($id) );	        
		?>
		
			<div class="<?php echo algo_causes_grid_class($args['columns']);?> ">
                        	<div class="causes-box element-box bg-white clearfix">
                            
                            	<div class="thum-box  img-effect8">
                                    <a href="<?php echo get_permalink($id); ?>">
                                    	<img src="<?php echo $url; ?>" width="357" height="278" alt="">
                                    </a>
                                    
                                    
                                    
                             </div>						
                                
                                <div class="progress thin-bar">
                                	<div class="progress-bar progress-bar-warning progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $percent;  ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $percent;  ?>%"></div>
                                </div>
                                
                                <div class="title-bx">
                                   	<h4 class="causes-title">
										<a href="<?php echo get_permalink($id); ?>"><?php echo $title; ?></a>
									</h4>
                                </div>
                                
                                <div class="donation-status padding-20">
                                	<div class="status-col">
                                        <strong><?php echo round($percent,2);  ?><span>%</span></strong>
                                        <b>Donation</b>
                                    </div>
                                    <div class="status-col">
                                        <strong>
										
										<?php echo algo_causes_currency_position($goal);  ?></strong>
                                        <b>Needed</b>
                                    </div>
                                </div>
								
								 <div class="causes-entry-excerpt clearfix">
                                <?php echo $content; ?>
                            </div>
                                
                                
								<a href="#" class="causes-button" data-cid="<?php echo $id; ?>" data-toggle="modal" data-target="#donate-now"><?php echo esc_html__('Donate Now','algo'); ?></a>
                                
                            </div>						
							
                      </div>
		
       
        <?php		
		endwhile;	
			echo "</div>";
			
			wp_reset_postdata();	
		}
		
add_shortcode('causes','algo_causes_grid_shortcode');

/// add dashbord widget for causes

//add_action('wp_dashboard_setup', 'algo_causes_dashboard_widgets');
 
function algo_causes_dashboard_widgets() {
global $wp_meta_boxes;

wp_add_dashboard_widget('custom_help_widget', 'Causes Status', 'algo_causes_dashboard_statistic');
}

function algo_causes_dashboard_statistic() {
	
	
}		


?>