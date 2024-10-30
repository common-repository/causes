<?php
/* @Version:		1.0.0
 * @Author: 		Damodar Prasad
 * @package:    	Causes
 * @Author URI: 	http://algothemes.com
 * @copyright:	 	Copyright (c) 2017, Algo Themes
 * @license:	    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 
/// Paypal Sandbox Details
*/

define('API_USERNAME', esc_attr( get_option('causes_paypal_sandbox_api_username') ));

define('API_PASSWORD', esc_attr( get_option('causes_paypal_sandbox_api_password') ));

define('API_SIGNATURE', esc_attr( get_option('causes_paypal_sandbox_api_signature') ));

define('API_ENDPOINT', 'https://api-3t.sandbox.paypal.com/nvp');

define('SUBJECT','');

define('USE_PROXY',FALSE);

define('PROXY_HOST', '127.0.0.1');

define('PROXY_PORT', '808');

define('PAYPAL_URL', 'https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=');

define('VERSION', '92.0');

define('ACK_SUCCESS', 'SUCCESS');

define('ACK_SUCCESS_WITH_WARNING', 'SUCCESSWITHWARNING');