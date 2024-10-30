<?php
/**
 * Header donate button 
 * @package:    		Causes
 * @subpackage: 		Templates/Header-Donate-button
 * @author:     		Damodar Prasad
 * @version:    		1.0.0
 */ 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/// check if enabled from settings	
if(get_option('header_donate_now')=='no'){		
	return;
}		
?>
<div class="extra-nav">		
	<div class="extra-col">
		<?php	algo_causes_header_donate_button();	?>
									
	</div>                     
</div>	