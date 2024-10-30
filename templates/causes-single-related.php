<?php
/**
 * Causes single related template part
 * @package     Causes
 * @subpackage  Causes/Template
 * @author      Algo Themes
 * @version     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*////
// Return if disabled
if ( ! get_theme_mod( 'causes_related', true ) ) {
	return;
}

// Vars
global $post;
$post_id	= $post->ID;
$post_count	= get_theme_mod( 'causes_related_count', '3' );

// Return if pass required
if ( post_password_required() ) {
	return;
}

// Disabled via meta setting - goodbye
if ( 'on' == get_post_meta( $post_id, 'algo_disable_related_items', true ) ) {
	return;
}*////

// Create an array of current category ID's
$cats		= wp_get_post_terms( $post_id, 'cause-category' );
$cats_ids	= array();  
foreach( $cats as $algo_related_cat ) {
	$cats_ids[] = $algo_related_cat->term_id; 
}
if ( ! empty( $cats_ids ) ) {
	$tax_query = array (
		array (
			'taxonomy'	=> 'cause-category',
			'field' 	=> 'id',
			'terms' 	=> $cats_ids,
			'operator'	=> 'IN',
		),
	);
} else {
	$tax_query = '';
}

// Related query arguments
$args = array(
	'post_type'			=> 'cause',
	'posts_per_page'	=> $post_count,
	'orderby'			=> 'rand',
	'post__not_in'		=> array( $post_id ),
	'no_found_rows'		=> true,
	'tax_query'			=> $tax_query,
);
$args = apply_filters( 'algo_related_cause_args', $args );
$algo_related_query = new wp_query( $args );

// If posts were found display related items
if ( $algo_related_query->have_posts() ) :

	// Wrap classes
	$wrap_classes = 'cause-posts-related clearfix';
	 ?>
	
	<div class="<?php echo $wrap_classes; ?>">
		<?php
		// Get heading text
		$heading = get_theme_mod( 'causes_related_title', esc_html__( 'Related Causes', 'boraj' ) );
		
		// Fallback
		$heading = $heading ? $heading : esc_html__( 'Related Causes', 'boraj' );
		
		// If Heading text isn't empty
		$value = apply_filters( 'causes_related', $heading );
		?><h2 class="related-causes posts-heading"><?php echo $value; ?></h2>
		
		<div class="algo-grid">
			<?php $algo_count = 0; //cutom post counting ?>
			<?php foreach( $algo_related_query->posts as $post ) : setup_postdata( $post ); ?>
				<?php $algo_count++; ?>
				
				<?php
				$default_path = plugin_dir_path(__DIR__) . 'templates/';
				$template = load_template( $default_path.'cause.php' );
				
					?>
				
				
				<?php if ( $algo_count == $post_count ) $algo_count = 0; ?>
			<?php endforeach; ?>
		</div><!-- .algo-grid -->
	</div><!--.<?php echo $wrap_classes; ?>-->

<?php endif; ?>
<?php wp_reset_postdata(); ?>