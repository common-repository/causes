<?php
/**
 * Add custom metaboxes to the causes
 * @package:	     Causes
 * @subpackage:		 includes/causes-metaboxes
 * @author:		     Damodar Prasad
 * @version:	     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Causes_Metabox' ) ) :

/*
** Causes custom meta box class
*/
	class Causes_Metabox{			
       
		/*
		 *
		 * Class constructor funtion
		*/
		public function __construct(){
			
			 // Add metabox
			add_action( 'add_meta_boxes', array( $this, 'algo_causes_post_meta' ) );
			
			// Save metabox
            add_action( 'save_post', array( $this, 'algo_causes_save_meta_data' ) );
			
			/// custom post style & script
			add_action( 'admin_enqueue_scripts', array( $this, 'algo_causes_meta_scripts' ) );
			
			$this->types = '';
			
		
		/*
			** Meta boxes elements
		*/
		$prefix = 'causes_';
		
		 $this->metaboxes = array(
                    'post_type' => array( 'cause' ),
                    'settings'  => array(					
                        'algo_goal'    => array(
                            'title'         => __( 'Goal', 'algo' ),
                            'description'   => __( 'Define Goal.', 'algo' ),
                            'id'            => $prefix .'goal',
                            'type'          => 'text',
                        ),
						
						'algo_start_date'    => array(
                          'title'         => __( 'Start Date', 'algo' ),
                          'description'   => __( 'Define Start date.', 'algo' ),
                          'id'            => $prefix .'start_date',
                          'type'          => 'text',
                        ),
						'algo_end_date'    => array(
                          'title'         => __( 'End Date', 'algo' ),
                          'description'   => __( 'Define End date.', 'algo' ),
                          'id'            => $prefix .'end_date',
                          'type'          => 'text',
                        )						
                    )
                );
		 }
		 
		 
		 /**
         * Enqueue scripts and styles needed for the metaboxes
         */
		 
        public function algo_causes_meta_scripts() {
			
			
           
		   //metabox assets
		   $assets_dir = plugin_dir_url( __DIR__ ).'assets/';
		   
			wp_enqueue_script('jquery-ui-datepicker', array( 'jquery' ) );
			
			wp_enqueue_script(
                'cause',
                $assets_dir .'js/causes-admin.js',
                true
            );
			
        }
 
	/*
	** 	add cutom post meta 	
	*/
		public function algo_causes_post_meta( $post_type ) {				
					
					$types  = array(  'cause' );
					$types  = apply_filters( 'causes_metaboxes_post_types', $types );
					$types  = array_combine( $types, $types );
					if ( in_array( $post_type, $types ) ) {
						$obj = get_post_type_object( $post_type );
						add_meta_box(
							'causes-metabox',
							$obj->labels->singular_name . ' '. __( 'Settings', 'algo' ),
							array( $this, 'algo_causes_metabox_display' ),
							$post_type,
							'normal',
							'high'
						);
					}			
					$this->types = $types;
				}
		
		
		/***
			** display metaboxe function
			** causes custom meta box
		*/
	    public function algo_causes_metabox_display($post){
		
				 $post_id    = $post->ID;
		 
		 // Add an nonce field so we can check for it later.
            wp_nonce_field( 'causes_metabox', 'causes_metabox_nonce' );

				
				if ( empty( $this->metaboxes ) ) {
                echo '<p>Hey your settings are empty, something is going on please contact your webmaster</p>';
                return;
            }

            ?>           
                <div id="algo-mb-tab" class="wp-tab-panel">
                    <table class="form-table">
					<tr  id="causes_tr">
                        <?php
                        // Loop through sections and store meta output
                        foreach ( $this->metaboxes['settings'] as $setting ) {
                            // Vars
                            $meta_id        = $setting['id'];
                            $title          = $setting['title'];
                            $type           = isset ( $setting['type'] ) ? $setting['type'] : 'text';
                            $default        = isset ( $setting['default'] ) ? $setting['default'] : '';
                            $description    = isset ( $setting['description'] ) ? $setting['description'] : '';
							
							if($meta_id=='causes_end_date' || $meta_id =='causes_start_date'):
							$end_date 			= get_post_meta( $post_id, $meta_id, true );
							$meta_value = 0 == $end_date ? '' : date_i18n( 'F d, Y', strtotime( $end_date ) );
							else:
							$meta_value     = get_post_meta( $post_id, $meta_id, true );
							 $meta_value     = $meta_value ? $meta_value : $default;
							
							endif;
							
                                                       

                               
                                // Text Field
                                if ( 'text' == $type ) { ?>							

                                    <td>
									<label for="<?php echo $meta_id; ?>"><strong><?php echo $title; ?>:</strong></label>
									<input name="<?php echo $meta_id; ?>" id="<?php echo $meta_id; ?>" type="text" value="<?php echo $meta_value; ?>">
									</td>

                                <?php }

                                
								
                                // Link field
                                elseif ( 'link' == $type ) { ?>

                                    <td><label for="<?php echo $meta_id; ?>"><strong><?php echo $title; ?>:</strong></label>
									
									<input name="<?php echo $meta_id; ?>" id="<?php echo $meta_id; ?>" type="text" value="<?php echo esc_url( $meta_value ); ?>">
									
									</td>

                                <?php }
								
								// Link field
                                elseif ( 'links' == $type ) { ?>

                                    <td><label for="<?php echo $meta_id; ?>"><strong><?php echo $title; ?>:</strong></label>
									<a href="<?php echo esc_url( home_url() );?>/wp-admin/admin.php?page=algo-panel#general-twitter-username" target="_blank"><?php echo _e('Twitter Inputs','boraj');?></a>
									</td>

                                <?php }

                                // Code Field
                                elseif ( 'code' == $type ) { ?>
                                    <td><label for="<?php echo $meta_id; ?>"><strong><?php echo $title; ?>:</strong></label>
                                        <textarea rows="1" cols="1" name="<?php echo $meta_id; ?>" type="text" class="algo-mb-textarea-code"><?php echo $meta_value; ?></textarea>
                                    </td>									
                                <?php }
								
                               // Checkbox
                                elseif ( 'checkbox' == $type ) {
                                    $meta_value = ( 'on' == $meta_value ) ? false : true; ?>
                                    <td>
										<label for="<?php echo $meta_id; ?>"><strong><?php echo $title; ?>:</strong></label>
										<input name="<?php echo $meta_id; ?>" type="checkbox" <?php checked( $meta_value, true, true ); ?>>
									</td>
                                <?php }								
                                // Select
                                elseif ( 'select' == $type ) {
                                    $options = isset ( $setting['options'] ) ? $setting['options'] : '';
                                    if ( ! empty( $options ) ) { ?>
                                        <td>
											<label for="<?php echo $meta_id; ?>"><strong><?php echo $title; ?>:</strong></label>
											<select id="<?php echo $meta_id; ?>" name="<?php echo $meta_id; ?>">
											<?php foreach ( $options as $option_value => $option_name ) { ?>
                                            <option value="<?php echo $option_value; ?>" <?php selected( $meta_value, $option_value, true ); ?>><?php echo $option_name; ?></option>
											<?php } ?>
											</select>
										</td>
                                    <?php }
                                }


                                // Media
                                elseif ( 'media' == $type ) {
                                    // Validate data if array - old Redux cleanup
                                    if ( is_array( $meta_value ) ) {
                                        if ( ! empty( $meta_value['url'] ) ) {
                                            $meta_value = $meta_value['url'];
                                        } else {
                                            $meta_value = '';
                                        }
                                    } ?>
                                    <td>
										<label for="<?php echo $meta_id; ?>"><strong><?php echo $title; ?>:</strong></label>
										<div class="uploader">
										<input type="text" name="<?php echo $meta_id; ?>" value="<?php echo $meta_value; ?>">
										<input class="algo-mb-uploader button-secondary" name="<?php echo $meta_id; ?>" type="button" value="<?php echo esc_html__( 'Upload', 'boraj' ); ?>" />
										</div>
									</td>
                                <?php }

                                // Editor
                                elseif ( 'editor' == $type ) {
                                    $teeny          = isset( $setting['teeny'] ) ? $setting['teeny'] : false;
                                    $rows           = isset( $setting['rows'] ) ? $setting['rows'] : '10';
                                    $media_buttons  = isset( $setting['media_buttons'] ) ? $setting['media_buttons'] : true; ?>
                                    <td>
										<label for="<?php echo $meta_id; ?>"><strong><?php echo $title; ?>:</strong></label>
										<?php wp_editor( $meta_value, $meta_id, array(
                                        'textarea_name' => $meta_id,
                                        'teeny'         => $teeny,
                                        'textarea_rows' => $rows,
                                        'media_buttons' => $media_buttons,
										) ); ?>
									
									</td>
                                <?php } ?>
                           

                        <?php } ?>
						 </tr>
                    </table>
                </div>
            

            

            <div class="clear"></div>
				
		<?php			
			
		}	
	
		/**
         * Save metabox data
         */
		 
        public function algo_causes_save_meta_data( $post_id ) {

            /*
             * We need to verify this came from our screen and with proper authorization,
             * because the save_post action can be triggered at other times.
             */

            // Check if our nonce is set.
            if ( ! isset( $_POST['causes_metabox_nonce'] ) ) {
                return;
            }

            // Verify that the nonce is valid.
            if ( ! wp_verify_nonce( $_POST['causes_metabox_nonce'], 'causes_metabox' ) ) {
                return;
            }

				
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            // Check the user's permissions.
            if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

                if ( ! current_user_can( 'edit_page', $post_id ) ) {
                    return;
                }

            } else {

                if ( ! current_user_can( 'edit_post', $post_id ) ) {
                    return;
                }
            }		
			

            /* OK, it's safe for us to save the data now. Now we can loop through fields */

            // Set settings array
            $tabs       = $this->metaboxes;
            $settings   = array();
           
                foreach ( $tabs['settings'] as $setting ) {
                    $settings[] = $setting;
                }
            
			

            // Loop through settings and validate
            foreach ( $settings as $setting ) {

			
                // Vars
                $value  = '';
                $id     = $setting['id'];
                $type   = isset ( $setting['type'] ) ? $setting['type'] : 'text';

                // Make sure field exists and if so validate the data
                if ( isset( $_POST[$id] ) ) { 

                    // Validate text
                    if ( 'text' == $type ) {
							if($id=='causes_end_date' || $id=='causes_start_date'):								
								$value =  date_i18n( 'Y-m-d 00:00:00', strtotime( $_POST[$id] ) );
							else:
							$value = sanitize_text_field( $_POST[$id] );
							endif;
                        
						
					
                    }

                    // Links
                    elseif ( 'link' == $type ) {
                        $value = esc_url( $_POST[$id] );
                    }

                    // Validate select
                    elseif ( 'select' == $type ) {
                        if ( 'default' == $_POST[$id] ) {
                            $value = '';
                        } else {
                            $value = $_POST[$id];
                        }
                    }

                    
                    // All else
                    else {
                        $value = $_POST[$id];
                    }
					
					 // Update meta if value exists
                    if ( $value) {
                        update_post_meta( $post_id, $id, $value );
                    }

                   
                    
                }

            }

        }

	} // end Causes_Metabox class

endif; /// end chekc if;

$post_metaboxes = new Causes_Metabox();
?>