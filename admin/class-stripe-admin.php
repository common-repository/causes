<?php
/**
 * Add stripe payment to the causes
 * @package:	     Causes
 * @subpackage:		 admin/stripe
 * @author:		     Damodar Prasad
 * @version:	     1.0.1
 */
 
 
class AlgoCausesStripe_Admin {
	
    /**
     * Instance of this class.
     *
     * @var      object
     */
    protected static $instance = null;


	private function __construct(){
		
		add_action('admin_init', array(&$this, 'register_settings'));
		
		
	}
    /**
     * Return an instance of this class.
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register Admin page settings
     *
     */
    public function register_settings($value = '') {
		
        register_setting('CausesStripePayments-settings-group', 'causes_settings', array(&$this, 'settings_sanitize_field_callback'));

        add_settings_section('CausesStripePayments-global-section', 'Stripe Settings', null, 'causes_stripe_payment');
        add_settings_section('CausesStripePayments-credentials-section', 'Credentials', null,'causes_stripe_payment');

       
        add_settings_field('stripe_is_active', 'Acitve Mode', array(&$this, 'settings_field_callback'), 'causes_stripe_payment', 'CausesStripePayments-credentials-section', array('field' => 'stripe_is_active', 'desc' => 'Check this checkbox to activate stripe payment method.'));
		
		 add_settings_field('stripe_is_live', 'Live Mode', array(&$this, 'settings_field_callback'), 'causes_stripe_payment', 'CausesStripePayments-credentials-section', array('field' => 'stripe_is_live', 'desc' => 'Check this to run the transaction in live mode. When unchecked it will run in test mode.'));
		 
		 
        add_settings_field('stripe_api_publishable_key', 'Stripe Publishable Key', array(&$this, 'settings_field_callback'), 'causes_stripe_payment', 'CausesStripePayments-credentials-section', array('field' => 'stripe_api_publishable_key', 'desc' => ''));
        add_settings_field('stripe_api_secret_key', 'Stripe Secret Key', array(&$this, 'settings_field_callback'), 'causes_stripe_payment', 'CausesStripePayments-credentials-section', array('field' => 'stripe_api_secret_key', 'desc' => ''));
       
	   
    }

    /**
     * Settings HTML
     *
     */
    public function settings_field_callback($args) {
        
        extract($args);

        $field_value = esc_attr(!empty(get_option($field)) ? get_option($field) : '');

        if (empty($size))
            $size = 40;

        switch ($field) {
           
            case 'use_new_button_method':
            case 'stripe_is_live':
            case 'stripe_is_active':
                echo "<input type='hidden' name='causes_settings[{$field}]' value='0' /><input type='checkbox' name='causes_settings[{$field}]' value='1' " . ($field_value ? 'checked=checked' : '') . " /><p class=\"description\">{$desc}</p>";
                break;
           
            default:
               
                // case 'api_username':
                // case 'api_password':
                // case 'api_signature':
                echo "<input type='text' name='causes_settings[{$field}]' value='{$field_value}' size='{$size}' /> <p class=\"description\">{$desc}</p>";
                break;
        }
    }

    /**
     * Validates the admin data
     *
     * @since    1.0.0
     */
    public function settings_sanitize_field_callback($input) {
        $output = get_option('CausesStripePayments-settings');

        if (empty($input['stripe_is_live']))
            $output['stripe_is_live'] = 0;
        else
            $output['stripe_is_live'] = 1;

        if (empty($input['stripe_api_secret_key']) || empty($input['stripe_api_publishable_key'])) {
            add_settings_error('causes_settings', 'invalid-credentials', 'You must fill all API credentials for plugin to work correctly.');
        }

        if (!empty($input['causes_checkout_url']))
            $output['causes_checkout_url'] = $input['causes_checkout_url'];
        else
            add_settings_error('causes_settings', 'invalid-checkout_url', 'Please specify a checkout page.');

        if (!empty($input['button_text']))
            $output['button_text'] = $input['button_text'];
        else
            add_settings_error('causes_settings', 'invalid-button-text', 'Button text should not be empty.');

        if (empty($input['use_new_button_method']))
            $output['use_new_button_method'] = 0;
        else
            $output['use_new_button_method'] = 1;        
        
       

        if (!empty($input['stripe_api_publishable_key']))
            $output['stripe_api_publishable_key'] = $input['stripe_api_publishable_key'];

        if (!empty($input['stripe_api_secret_key']))
            $output['stripe_api_secret_key'] = $input['stripe_api_secret_key'];
        

        return $output;
    }


}
