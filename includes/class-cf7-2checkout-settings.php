<?php
if (!defined('ABSPATH'))
    exit;

class CF7_2Checkout_Settings{
	public function __construct(){
		add_action('admin_menu', array($this, 'cf7_2checkout_settings'));
        add_filter( 'wpcf7_editor_panels', array($this, 'cf7_2checkout_panel_tab'),10,1);
        add_action('save_post_wpcf7_contact_form', array($this, 'save_contact_form_seven_checkout'));
        if(!$this->check_keys()){
            add_action('admin_notices',array($this,'api_keys_missing_errors'));
        }
	}
    public function api_keys_missing_errors(){
        $class = 'notice notice-error';
        $message = __('Please add 2Checkout keys on ', 'cf7_2checkout');
        $link = '<a href="'.admin_url().'admin.php?page=cf7-2checkout-settings">'.__('2Checkout setting page', 'cf7_2checkout').'</a>';
        printf('<div class="%1$s"><p>%2$s %3$s</p></div>', $class, $message, $link);
    }
    public function cf7_2checkout_panel_tab($panels){
         $panels['twocheckout-panel'] = array( 
            'title' => __( '2Checkout Fields', 'cf7_2checkout' ),
            'callback' => array($this, 'cf7_2checkout_panel_tab_callback')
        );
        return $panels;
    }
    public function save_contact_form_seven_checkout($post_id){
        $address['2checkout_name']=isset($_POST['2checkout_name']) ? sanitize_text_field($_POST['2checkout_name']) : '';
        $address['2checkout_address']=isset($_POST['2checkout_address']) ? sanitize_text_field($_POST['2checkout_address']) : '';
        $address['2checkout_email']=isset($_POST['2checkout_email']) ? sanitize_text_field($_POST['2checkout_email']) : '';
        $address['2checkout_city']=isset($_POST['2checkout_city']) ? sanitize_text_field($_POST['2checkout_city']) : '';
        $address['2checkout_state']=isset($_POST['2checkout_state']) ? sanitize_text_field($_POST['2checkout_state']) : '';
        $address['2checkout_zipcode']=isset($_POST['2checkout_zipcode']) ? sanitize_text_field($_POST['2checkout_zipcode']) : '';
        $address['2checkout_country']=isset($_POST['2checkout_country']) ? sanitize_text_field($_POST['2checkout_country']) : '';

        update_post_meta($post_id,'cf7_2checkout_address_fields',$address);
       
    }
    public function cf7_2checkout_panel_tab_callback(){
        global $post;
        $cf7=WPCF7_ContactForm::get_instance($_GET['post']);
        $tags=$cf7->collect_mail_tags();
        $post_id = isset($_GET['post']) ? $_GET['post'] : '';
        echo '<h3>'.__("Customer Billing Information (Form Fields)","cf7_2checkout").'</h3>';
        echo '<p style="color:red">'.__("2Checkout payment gateway requires following information to process payment,
         otherwise payment will not processed","cf7_2checkout").'</p>';
        if(!empty($tags)){
            echo '<table>';
            $address=get_post_meta($post_id,'cf7_2checkout_address_fields',true);
            //  name
            echo '<tr><th><label for="2checkout_name">'.__("Name Field","cf7_2checkout").'</label></th>';
            echo '<td><select name="2checkout_name" id="2checkout_name" class="widefat">';
            echo '<option value="">'.__("Select field name for name","cf7_2checkout").'</option>';
            foreach ($tags as $key => $tag) {
                $selected='';
                if(isset($address['2checkout_name']) && $address['2checkout_name']==$tag)
                    $selected='selected';

                echo '<option value="'.esc_attr($tag).'" '.$selected.'>'.esc_html($tag).'</option>';
            }
            echo '</select></td></tr>';
            //address
            echo '<tr><th><label for="2checkout_address">'.__("Address Field","cf7_2checkout").'</label></th>';
            echo '<td><select name="2checkout_address" id="2checkout_address" class="widefat">';
            echo '<option value="">'.__("Select field name for address","cf7_2checkout").'</option>';
            foreach ($tags as $key => $tag) {
                 $selected='';
                if(isset($address['2checkout_address']) && $address['2checkout_address']==$tag)
                    $selected='selected';

                echo '<option value="'.esc_attr($tag).'" '.$selected.'>'.esc_html($tag).'</option>';
            }
            echo '</select></td></tr>';
            // email field
            echo '<tr><th><label for="2checkout_email">'.__("Email Field","cf7_2checkout").'</label></th>';
            echo '<td><select name="2checkout_email" id="2checkout_email" class="widefat">';
            echo '<option value="">'.__("Select field name for email","cf7_2checkout").'</option>';
            foreach ($tags as $key => $tag) {
                 $selected='';
                if(isset($address['2checkout_email']) && $address['2checkout_email']==$tag)
                    $selected='selected';

                echo '<option value="'.esc_attr($tag).'" '.$selected.'>'.esc_html($tag).'</option>';
            }
            echo '</select></td></tr>';
            // city field
            echo '<tr><th><label for="2checkout_city">'.__("City Field","cf7_2checkout").'</label></th>';
            echo '<td><select name="2checkout_city" id="2checkout_city" class="widefat">';
            echo '<option value="">'.__("Select field name for city","cf7_2checkout").'</option>';
            foreach ($tags as $key => $tag) {
                 $selected='';
                if(isset($address['2checkout_city']) && $address['2checkout_city']==$tag)
                    $selected='selected';

                echo '<option value="'.esc_attr($tag).'" '.$selected.'>'.esc_html($tag).'</option>';
            }
            echo '</select></td></tr>';
            // state field
            echo '<tr><th><label for="2checkout_state">'.__("State Field","cf7_2checkout").'</label></th>';
            echo '<td><select name="2checkout_state" id="2checkout_state" class="widefat">';
            echo '<option value="">'.__("Select field name for state","cf7_2checkout").'</option>';
            foreach ($tags as $key => $tag) {
                 $selected='';
                if(isset($address['2checkout_state']) && $address['2checkout_state']==$tag)
                    $selected='selected';

                echo '<option value="'.esc_attr($tag).'" '.$selected.'>'.esc_html($tag).'</option>';
            }
            echo '</select></td></tr>';
            // zipcode field
            echo '<tr><th><label for="2checkout_zipcode">'.__("Zipcode Field","cf7_2checkout").'</label></th>';
            echo '<td><select name="2checkout_zipcode" id="2checkout_zipcode" class="widefat">';
            echo '<option value="">'.__("Select field name for zipcode","cf7_2checkout").'</option>';
            foreach ($tags as $key => $tag) {
                 $selected='';
                if(isset($address['2checkout_zipcode']) && $address['2checkout_zipcode']==$tag)
                    $selected='selected';

                echo '<option value="'.esc_attr($tag).'" '.$selected.'>'.esc_html($tag).'</option>';
            }
            echo '</select></td></tr>';
            // country field
            echo '<tr><th><label for="2checkout_country">'.__("Country Field","cf7_2checkout").'</label></th>';
            echo '<td><select name="2checkout_country" id="2checkout_country" class="widefat">';
            echo '<option value="">'.__("Select field name for country","cf7_2checkout").'</option>';
            foreach ($tags as $key => $tag) {
                 $selected='';
                if(isset($address['2checkout_country']) && $address['2checkout_country']==$tag)
                    $selected='selected';

                echo '<option value="'.esc_attr($tag).'" '.$selected.'>'.esc_html($tag).'</option>';
            }
            echo '</select></td></tr>';
            echo '</table>';
           
        }
        else{
            echo __('It seems your form does not contain tags, please add tags before!', 'cf7_2checkout');
        }
    }
	public function cf7_2checkout_settings() {
        add_submenu_page('wpcf7', __('2Checkout Settings','cf7_2checkout'), __('2Checkout Settings','cf7_2checkout'), 'manage_options', 'cf7-2checkout-settings', array($this, 'twocheckout_settings_html'));

        //call register settings function
        add_action('admin_init', array($this, 'twocheckout_register_settings'));
    }
    public function twocheckout_register_settings() {
        register_setting('cf7-2checkout-settings-group', 'cf7_2checkout_mode');
        register_setting('cf7-2checkout-settings-group', 'cf7_test_2checkout_sellerid');
        register_setting('cf7-2checkout-settings-group', 'cf7_test_2checkout_privatekey');
        register_setting('cf7-2checkout-settings-group', 'cf7_test_2checkout_publickey');
        register_setting('cf7-2checkout-settings-group', 'cf7_live_2checkout_sellerid');
        register_setting('cf7-2checkout-settings-group', 'cf7_live_2checkout_privatekey');
        register_setting('cf7-2checkout-settings-group', 'cf7_live_2checkout_publickey');
    }
    public function twocheckout_settings_html() {
        ?>            
        <div class="wrap">
            <h1><?php _e("CF7 2Checkout Settings","cf7_2checkout"); ?></h1>
            <p><?php echo __('Get 2Checkout API keys from your 2Checkout account.', 'cf7_2checkout'); ?></p>
            
            <form method="post" action="options.php">
                <?php settings_fields('cf7-2checkout-settings-group'); ?>
                <?php do_settings_sections('cf7-2checkout-settings-group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th><?php _e('Mode','cf7_2checkout'); ?></th>
                        <td>
                        	<?php $mode=get_option('cf7_2checkout_mode'); ?>
                            <input type="radio" <?php if ($mode == 'live'): ?>checked="checked"<?php endif; ?> value="live" id="cf7_2checkout_mode_live" name="cf7_2checkout_mode">
                            <label for="cf7_2checkout_mode_live" class="inline"><?php _e('Live', 'cf7_2checkout') ?></label>
                            &nbsp;&nbsp;&nbsp; <input type="radio" <?php if ($mode == 'test' || $mode == ''): ?>checked="checked"<?php endif; ?> value="test" id="cf7_2checkout_mode_test" name="cf7_2checkout_mode">
                            <label for="cf7_2checkout_mode_test" class="inline"><?php _e('Test', 'cf7_2checkout') ?></label>                                
                        </td>
                    </tr>

                    <tr>
                        <th colspan="2"><?php _e('Test Account', 'cf7_2checkout') ?> <hr></th>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Test Seller ID', 'cf7_2checkout') ?>
                        </th>
                        <td>
                            <input style="width: 60%;" type="text" value="<?php echo esc_attr(get_option('cf7_test_2checkout_sellerid')); ?>" name="cf7_test_2checkout_sellerid">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Test Private Key', 'cf7_2checkout') ?>
                        </th>
                        <td>
                            <input style="width: 60%;" type="text" value="<?php echo esc_attr(get_option('cf7_test_2checkout_privatekey')); ?>" name="cf7_test_2checkout_privatekey">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Test Publishable Key', 'cf7_2checkout') ?>
                        </th>
                        <td>
                            <input style="width: 60%;" type="text" value="<?php echo esc_attr(get_option('cf7_test_2checkout_publickey')); ?>" name="cf7_test_2checkout_publickey">
                        </td>
                    </tr>

                    <tr>
                        <th colspan="2"><?php _e('Live Account', 'cf7_2checkout') ?> <hr></th>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Live Seller ID', 'cf7_2checkout') ?>
                        </th>
                        <td>
                            <input style="width: 60%;" type="text" value="<?php echo esc_attr(get_option('cf7_live_2checkout_sellerid')); ?>" name="cf7_live_2checkout_sellerid">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Live Private Key', 'cf7_2checkout') ?>
                        </th>
                        <td>
                            <input style="width: 60%;" type="text" value="<?php echo esc_attr(get_option('cf7_live_2checkout_privatekey')); ?>" name="cf7_live_2checkout_privatekey">
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php _e('Live Publishable Key', 'cf7_2checkout') ?>
                        </th>
                        <td>
                            <input style="width: 60%;" type="text" value="<?php echo esc_attr(get_option('cf7_live_2checkout_publickey')); ?>" name="cf7_live_2checkout_publickey">
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>

        <?php
        
    }
    public function check_keys(){
        $mode = get_option('cf7_2checkout_mode'); 
        $privkey=''; $pubkey=''; $sellerid='';
        if($mode=='test'){
            $privkey=get_option('cf7_test_2checkout_privatekey');
            $pubkey=get_option('cf7_test_2checkout_publickey');
            $sellerid=get_option('cf7_test_2checkout_sellerid');
        }
        else{
            $privkey=get_option('cf7_live_2checkout_privatekey');
            $pubkey=get_option('cf7_live_2checkout_publickey');
            $sellerid=get_option('cf7_live_2checkout_sellerid');
        }   
        if(!empty($privkey) && !empty($pubkey) && !empty($sellerid) ){
            return true;
        }
        else{
            return false;
        }
    }
    
}