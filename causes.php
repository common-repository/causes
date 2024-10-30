<?php 
/**
 * plugin name:		 	 Causes
 * Plugin URI:		 	 http://algothemes.com/
 * Description: 	 	 A charity toolkit that help you to creat online causes and collect online donations.
 * Version:			 	 1.0.01
 * Author:	   		 	 algothemes
 * Author URI: 		 	 http://algothemes.com
 * Requires at least:	 4.4
 * Tested up to: 	  	 4.7
 *
 *
 * @package:    		 Causes
 * @author               Damodar Prasad
 * @copyright            Copyright (c) 2017, Algo Themes
 * @license             http://opensource.org/licenses/gpl-2.0.php GNU Public License
 
 This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 
 */ 
 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
		define('PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ));
		
		define('PLUGIN_URL_PATH', plugin_dir_url( __FILE__ ));
		
		define('PLUGIN_VERSION', '1.0.01');
		
		
		require_once( PLUGIN_DIR_PATH. 'includes/install.php' )	;	
		
		
		/*
		* Register hooks that are fired when the plugin is activated o.
		*
		*/
		register_activation_hook( __FILE__, 'algo_causes_install' );
		
		
		require_once( PLUGIN_DIR_PATH. 'includes/class-algo-causes.php' )	;		
		require_once( PLUGIN_DIR_PATH. 'includes/class-causes-donation.php' )	;		
		require_once( PLUGIN_DIR_PATH. 'includes/causes-functions.php' );
		require_once( PLUGIN_DIR_PATH. 'includes/causes-hooks.php' );
		
		
		if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX )) {

			require_once( plugin_dir_path(__FILE__) . 'admin/class-stripe-admin.php' );
			add_action('plugins_loaded', array('AlgoCausesStripe_Admin', 'get_instance'));
			
		}
		

		/// Custome meta box class
		
		if (is_admin() ){
			
			require_once( PLUGIN_DIR_PATH. 'includes/causes-metabox.php' );
		}		
	

	/**
	* Add custom links to the plugin actions.
	*
	* @since   1.0.0
	*/
	function algo_causes_plugin_settings_links( $links ) {
			$links[] = '<a href="' . admin_url( 'edit.php?post_type=cause&page=settings' ) . '">' . __( 'Settings', 'algo' ) . '</a>';
			return $links;
		}


	add_filter( 'plugin_action_links_'. plugin_basename(__FILE__),  'algo_causes_plugin_settings_links' );
?>