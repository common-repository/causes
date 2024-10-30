<?php
/**
 * Cause entry excerpt template part
 
 * @version     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get excerpt length
$excerpt_length = get_theme_mod( 'causes_entry_excerpt_length', '15' );

// Return if excerpt length is set to 0
if ( '0' == $excerpt_length ) {
	return;
} ?>

<div class="cause-entry-excerpt clearfix">
	<?php 
	
	$content = wp_trim_words(get_the_content(),$excerpt_length)."<br>";
	
	echo $content ;
	
	?>
</div><!-- .cause-entry-excerpt -->

<a href="javascript:void(0);" data-cid="<?php echo get_the_ID(); ?>" class="causes-button" data-toggle="modal" data-target="#donate-now"><?php echo esc_html__('Donate now','algo'); ?></a>

