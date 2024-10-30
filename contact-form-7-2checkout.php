<?php
/**
 * Plugin Name: Contact Form 7 2Checkout
 * Description: Accept payment using Contact Form 7 2Checkout
 * Author: Codeincept
 * Author URI: https://codeincept.com/
 * Version: 1.0.0
 * Text Domain: cf7_2checkout
 * Requires at least: 4.5
 * Tested up to: 4.9.7
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} //!defined('ABSPATH')
if (!class_exists('Contact_Form_Seven_TwoCheckout')) {
    class Contact_Form_Seven_TwoCheckout
    {
        public function __construct(){
        
            /**
             * check for contact form 7
             */
            add_action('init', array($this,'cf7_2checkout_plugin_dependencies'));
        }

        public function cf7_2checkout_plugin_dependencies() {
            if (!class_exists('WPCF7')) {
                add_action('admin_notices',  array($this, 'cf7_2checkout_admin_notices'));
            } else {
                define("CF72CHECKOUT_PLUGIN_PATH", plugin_dir_path(__FILE__));
                define("CF72CHECKOUT_PLUGIN_URL", plugin_dir_url(__FILE__));

                /**
                 * include 2checkout lib
                 */
                require_once( CF72CHECKOUT_PLUGIN_PATH . 'lib/Twocheckout.php' );

                /**
                 * contact form 7 2checkout class
                 */
                require_once( CF72CHECKOUT_PLUGIN_PATH . 'includes/class-cf7-2checkout.php' );
                new CF7_2Checkout_Process();
                
                /**
                 * contact form 7 2checkout settings
                 */
                require_once( CF72CHECKOUT_PLUGIN_PATH . 'includes/class-cf7-2checkout-settings.php' );
                new CF7_2Checkout_Settings();
            }
        }

        public function cf7_2checkout_admin_notices() {
            $class = 'notice notice-error';
            $message = __('Contact form 7 2Checkout Payment plugin requires Contatc form 7 to be installed and active.', 'cf7_2checkout');
            printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);
        }
    }
    $run= new Contact_Form_Seven_TwoCheckout();
}
