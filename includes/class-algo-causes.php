<?php

/**
 * algo cauese class
 *
 * @package:	 	Cuases
 * @author:	 		Damodar Prasad
 * @version:	 	1.0.0
 * @copyright 		Copyright (c) 2017, Algo Themes
 * @license     	http://opensource.org/licenses/gpl-2.0.php GNU Public License
 
 */
 
 
global $causeObj;

 if ( ! class_exists( 'AlgoCauses' ) ) :
 
  
/** 
 * Main causes class
 * @class AlgoCauses
 * @version 1.0.0
 */
 
 
 final Class AlgoCauses{
	 
	 public $version = '1.0.0';
	   
	
	//Causes Constructor
	public function __construct(){		
		
		//custom post init	
		add_action( 'init', array($this,'algo_causes_init'),0 );		
		
		// enqueue scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'algo_causes_scripts' ) );	
		
		add_action('admin_enqueue_scripts',  array( $this, 'algo_causes_admin_style' ));
			
		//custom post add column		
		add_filter(	'manage_edit-cause_columns', array($this,'algo_causes_add_new_columns'));
		
		add_action(	'manage_cause_posts_custom_column', array($this,'algo_causes_manage_columns'), 10, 2);
		
		//custom post add image size
		add_filter( 'causes_image_sizes', array($this,'algo_causes_add_image_sizes') , 10 );
       
	   //custom post post per page		
		add_action( 'pre_get_posts',array($this,'algo_causes_posts_per_page'));
		
		//custom post currency function
		add_action( 'causes_currency',array($this,'algo_causes_currency'));
		
		add_action( 'causes_currency_code',array($this,'algo_causes_currency_code'));
		
		//custom post donation thankyou page
        add_shortcode( 'donation-thankyou', array($this,'algo_causes_donation_thankyou'));			
		
		//single post template 
		add_filter('the_content', array($this ,'algo_causes_template' ));
		// title filter
		add_filter( 'the_title', array($this ,'remove_single_custom_post_titles'), 10, 2 );
		
		
		add_filter( 'cause_template_locations' , array( $this, 'add_template_locations' ) );
		
			
	}
	
	/// remove default title from causes custom post single page
	function remove_single_custom_post_titles( $title ) {
		if( is_singular( 'cause' ) ):
		return '';
		else:
		return $title;
		endif;
	}
	
	public function add_template_locations( $template_locations ){ 
 
				echo $template_locations[] = PLUGIN_DIR_PATH . 'templates';
    
				return $template_locations;
	}
	
 /** 
  * Custom post init function
  */
 function algo_causes_init() {
	 
	$causes_search   	   = true;
    $causes_search  	   = ! $causes_search ? true : false;
	
	$labels = array(
			'name'              => esc_html__( 'Causes', 'algo' ),
            'singular_name'     => esc_html__( 'Add New Campaign', 'algo' ),
            'add_new'           => esc_html__( 'Add New Campaign', 'algo' ),
            'add_new_item'      => esc_html__( 'Add New Campaign', 'algo' ),
            'edit_item'         => esc_html__( 'Edit Campaign', 'algo' ),
            'new_item'          => esc_html__( 'Add New Campaign', 'algo' ),
            'view_item'         => esc_html__( 'View Campaign', 'algo' ),
            'search_items'      => esc_html__( 'Search Campaigns', 'algo' ),
            'not_found'         => esc_html__( 'No Campaigns Found', 'algo' ),
            'not_found_in_trash'=> esc_html__( 'No Campaign Found In Trash', 'algo' )
		);

	$args = array(
		'labels'             => $labels,
        'description'        => esc_html__( 'Description.', 'algo' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'causes' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title','thumbnail', 'editor','comments'),
		'taxonomies'		 => array('tags'),
		'menu_icon'			=>'dashicons-money',
		'exclude_from_search'   => $causes_search,		
	);

	register_post_type( 'cause', $args ); //Register post type	
	
	/** 
	 * Custom post tags
	 */
	// Define causes tag labels
        $labels = array(
            'name'                          => esc_html__( 'Tags', 'algo' ),
            'singular_name'                 => esc_html__( 'Tag', 'algo' ),
            'menu_name'                     => esc_html__( 'Tags', 'algo' ),
            'search_items'                  => esc_html__( 'Search Causes Tags', 'algo' ),
            'popular_items'                 => esc_html__( 'Popular Causes Tags', 'algo' ),
            'all_items'                     => esc_html__( 'All Causes Tags', 'algo' ),
            'parent_item'                   => esc_html__( 'Parent Causes Tag', 'algo' ),
            'parent_item_colon'             => esc_html__( 'Parent Causes Tag:', 'algo' ),
            'edit_item'                     => esc_html__( 'Edit Causes Tag', 'algo' ),
            'update_item'                   => esc_html__( 'Update Causes Tag', 'algo' ),
            'add_new_item'                  => esc_html__( 'Add New Causes Tag', 'algo' ),
            'new_item_name'                 => esc_html__( 'Causes Tag Name', 'algo' ),
            'separate_items_with_commas'    => esc_html__( 'Separate Causes tags with commas', 'algo' ),
            'add_or_remove_items'           => esc_html__( 'Add or remove Causes tags', 'algo' ),
            'choose_from_most_used'         => esc_html__( 'Choose from the most used Causes tags', 'algo' ),
        );

        // Define Causes tag arguments
        $args = array(
            'labels'                => $labels,
            'public'                => true,
            'show_in_nav_menus'     => true,
            'show_ui'               => true,
            'show_tagcloud'         => true,
            'hierarchical'          => false,
            'rewrite'               => array(
                'slug'  => 'causes-tags',
            ),
            'query_var'             => true
        );

        // Register the Causes tag taxonomy
        register_taxonomy( 'cause_tags', array( 'cause' ), $args );
	
	/** 
	*	custom post category
	*/
	// Define causes category labels
    $labels = array(
        'name'                          => esc_html__( 'Categories','algo' ),
        'singular_name'                 => esc_html__( 'Categories','algo' ),
        'menu_name'                     => esc_html__( 'Categories','algo' ),
        'search_items'                  => esc_html__( 'Search','algo' ),
        'popular_items'                 => esc_html__( 'Popular', 'algo' ),
        'all_items'                     => esc_html__( 'All', 'algo' ),
        'parent_item'                   => esc_html__( 'Parent', 'algo' ),
        'parent_item_colon'             => esc_html__( 'Parent', 'algo' ),
		'edit_item'                     => esc_html__( 'Edit', 'algo' ),
        'update_item'                   => esc_html__( 'Update', 'algo' ),
        'add_new_item'                  => esc_html__( 'Add Cause Category', 'algo' ),
        'new_item_name'                 => esc_html__( 'New', 'algo' ),
        'separate_items_with_commas'    => esc_html__( 'Separate with commas', 'algo' ),
        'add_or_remove_items'           => esc_html__( 'Add or remove', 'algo' ),
        'choose_from_most_used'         => esc_html__( 'Choose from the most used', 'algo' ),
    );

    // Define causes category arguments
    $args = array(
        'labels'                => $labels,
        'public'                => true,
        'show_in_nav_menus'     => true,
        'show_ui'               => true,
        'show_tagcloud'         => true,
        'hierarchical'          => true,
        'rewrite'               => array(
        'slug'  				=> 'causes-category',
        ),
			'query_var'             => true
    );

    // Register the causes category taxonomy
       register_taxonomy( 'cause-category', array( 'cause' ), $args );
	   
	   flush_rewrite_rules();
	
	}
	
	/**
	 * Loads public facing scripts and stylesheets.
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function algo_causes_scripts() {
			
			
			/* Main styles */
			wp_register_style(
				'causes-style',
				PLUGIN_URL_PATH.'assets/css/causes.css',
				array()	,
				$this->version
			);
			wp_register_style(
				'bootstrap',
				PLUGIN_URL_PATH.'assets/css/bootstrap.min.css',
				array()				
			);
			wp_enqueue_style( 'bootstrap' );
			
			wp_enqueue_style( 'causes-style' );
			
			
			
			/* bootstrap js */
			wp_enqueue_script(
				'bootstrap-min',
				PLUGIN_URL_PATH.'assets/js/bootstrap.min.js',
				array(),
				'',
				true
			);
			wp_enqueue_script( 'causes-functions', PLUGIN_URL_PATH.'assets/js/cause-function.js' , array( 'jquery' ), '1.0', true );
			
			$wp_localize_script = array('ajaxurl'=> admin_url( 'admin-ajax.php' ),);
			
			wp_localize_script( 'causes-functions', 'causesLocalize',  $wp_localize_script);
		}
	/*
	 *** for admin use only
	*/
		
		function algo_causes_admin_style(){
			
			wp_register_style('jquery-ui', PLUGIN_URL_PATH.'assets/css/jquery-ui.css');
			wp_enqueue_style( 'jquery-ui' ); 
		}
		

	/**
		 * Throw error on object clone.
		 *
		 * @since   1.0.0
		 * @access  public
		 * @return  void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'huh?', 'algo' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since   1.0.0
		 * @access  public
		 * @return  void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'huh?', 'algo' ), '1.0.0' );
		}
		
	//function template 

	/** 
	 *	call single template
	 */ 
	function algo_causes_template($single) {
		global $post;   
		
		
		
		// Checks for single template by post type	
		if (get_post_type() == "cause" && is_single()){  
		
			//add_filter( 'the_title', 'suppress_if_blurb', 10, 2 )
		
			if(file_exists(PLUGIN_DIR_PATH.'templates/causes-single-content.php')):
				include PLUGIN_DIR_PATH. 'templates/causes-single-content.php';
			endif;
		}
		return $single;
	}	


/** 
 *	Add to custom post column function	
 */ 
function algo_causes_add_new_columns($causes_columns) {	
    $new_columns['cb'] 			= '<input type="checkbox" />';    
    $new_columns['title'] 		= esc_html__('Title','algo');   
	$new_columns['author'] 		= esc_html__('Author','algo');	
	$new_columns['cause-category'] = esc_html__('Category','algo');
	$new_columns['comments']	= esc_html__('Comments','algo'); 
	$new_columns['image'] 		= esc_html__('Featured Images','algo');
	$new_columns['causestart'] 	= esc_html__('Causes Start','algo');
	$new_columns['causeend'] 	= esc_html__('Causes End','algo');
    $new_columns['date'] 		= esc_html__('Date', 'algo'); 
	
    return $new_columns;
}
	

/** 
 *	Mange to custom post column function	
 */
function algo_causes_manage_columns($column_name, $id) {
    global $wpdb;
    switch ($column_name) { // switch start
		case 'goal':
		   echo get_post_meta($id, 'causes_goal', true);							
		break;
		case 'causestart':
			echo date_i18n( 'F d, Y', strtotime(get_post_meta($id, 'causes_start_date', true)));	
		break;	
		case 'causeend':
		   echo date_i18n( 'F d, Y', strtotime(get_post_meta($id, 'causes_end_date', true)));	
		break;
		case 'cause-category':
			if ( $category_list = get_the_term_list( $id, 'cause-category', '', ', ', '' ) ) {
				echo $category_list;
			} else {
			echo '&mdash;';
		}
		break;	
		case 'image':
			
			if ( has_post_thumbnail() ) {
					the_post_thumbnail( array(100,100)); 
			}
			
			else {
				echo '&mdash;';
			}
			break;	
				
    } // end switch
} 

/** 
 * Add custom post image sizes
 */
function algo_causes_add_image_sizes( $sizes ) {
		$obj            = get_post_type_object( 'cause' );	// get post type	
        $post_type_name = $obj->labels->singular_name;      // custom post singular name     		
        $new_sizes  = array(
            'cause_entry'   => array(
                'label'     => sprintf( esc_html__( '%s Grid', 'algo' ), $post_type_name ),
                'width'     => 'cause_entry_image_width',
                'height'    => 'cause_entry_image_height',
                'crop'      => 'cause_entry_image_crop',
            ),
            'cause_post'    => array(
                'label'     => sprintf( esc_html__( '%s Single', 'algo' ), $post_type_name ),
                'width'     => 'cause_post_image_width',
                'height'    => 'cause_post_image_height',
                'crop'      => 'cause_post_image_crop',
            ),
        );
        $sizes = array_merge( $sizes, $new_sizes );
        return $sizes;
    }

/**
 * Alters posts per page for the custom post taxonomies.    
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts
 */
function algo_causes_posts_per_page( $query ) {
    if ( algo_causes_is_tax() && $query->is_main_query() ) {
        $query->set( 'posts_per_page', get_theme_mod( 'causes_archive_posts_per_page', '12' ) );
        return;
    }
}

/**
 * custom post donation thankyou page    
 */	
function algo_causes_donation_thankyou( $atts ){
	extract(shortcode_atts(array(), $atts));    
	ob_start();
	// ThankYou page Content
	echo get_option('causes_donation_thankyou');
		
	$output = ob_get_contents();
	ob_end_clean();
	return  $output ;
}
	
/**
 * currency symbol for custom post
 */
function algo_causes_currency(){
	$currency_symbols = array(		
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
?>

	<select name="causes_settings[causes_currency_codes]" id="currency_codes">				
		<?php foreach($currency_symbols as $key=>$option)				
			{
			?>
				<option value="<?php echo $key; ?>" <?php if($key == get_option('causes_currency_codes') ) { echo 'selected'; } ?>><?php echo $key; ?></option>					
			<?php
			}				
		?>				
	</select>
<?php	  
}

	/**
	 * currency code for custom post
	 */
	function algo_causes_currency_code(){		
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
}
endif; /// 

$causeObj = new AlgoCauses; 