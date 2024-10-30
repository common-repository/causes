<?php
/* //Cuases Hooks
 * @package:    	Causes
 * @Version:		1.0.0
 * @Author: 		Damodar Prasad
 * @Author URI: 	http://algothemes.com
 * @copyright:	 	Copyright (c) 2017, Algo Themes
 * @license:	    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 
 *//// 
 
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

add_action('algo_causes_hook_container_before','algo_causes_start_wrpaer');

add_action('algo_causes_hook_container_after','algo_causes_end_wrpaer');

add_action('algo_causes_loop','algo_causes_loop');

/// for use this button in theme please call 'algo_causes_donate_button_header' on any action accroding to theme suitable. 
add_action( 'algo_hook_site_header_inner_nav', 'algo_causes_donate_button_header',11);





?>