<?php
if (!defined('ABSPATH'))
    exit;
    
class CF7_2Checkout_Process
{
	public function __construct(){
        add_action('wpcf7_init', array($this, 'wpcf7_add_form_tag_2checkout'));
        add_action('wpcf7_admin_init', array($this, 'wpcf7_add_tag_generator_2checkout'));
        add_action('wp_enqueue_scripts', array($this, 'cf7_2checkout_script_style'));
        add_filter('wpcf7_validate_twocheckout', array($this, 'cf7_2checkout_validation'), 10, 2);
        add_filter('wpcf7_mail_components', array($this, 'cf7_2checkout_add_transaction_to_email'), 100);
	}
	public function cf7_2checkout_script_style(){
		wp_register_script("cf7-2checkout","https://www.2checkout.com/checkout/api/2co.min.js",array("jquery"));
	}
	public function custom_css(){
		?>
		<style type="text/css">
			.wpcf7-form .card_expiry_fields {
			    display: flex;
			}
			.wpcf7-form .card_expiry_fields .month {
			    width: 49%;
			    float: left;
			}
			.wpcf7-form .card_expiry_fields .year {
			    float: right;
			    width: 49%;
			}
			.wpcf7-form .card_expiry_fields span {
			    width: 2%;
			    text-align: center;
			}
		</style>
		<?php
	}
	public function wpcf7_add_tag_generator_2checkout(){
		$tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add('twocheckout', __('2Checkout', 'cf7_2checkout'), array($this, 'wpcf7_tag_generator_2checkout'));
	}
	public function wpcf7_tag_generator_2checkout($contact_form, $args = ''){
		$args = wp_parse_args($args, array());
        $type = $args['id'];

        $description = __("Generate a form-tag for 2Checkout payment.", 'cf7_2checkout');
        ?>
        <div class="control-box">
            <fieldset>
                <legend><?php echo $description; ?></legend>

                <table class="form-table">
                    <tbody>                            
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-price'); ?>"><?php echo __('Price', 'cf7_2checkout'); ?></label></th>
                            <td>
                                <input type="text" name="values" class="oneline" id="<?php echo esc_attr($args['content'] . '-price'); ?>" /> 
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-currency'); ?>"><?php echo __('Currency', 'cf7_2checkout'); ?></label></th>
                            <td>
                                <input type="text" name="currency" value="USD" class="option" id="<?php echo esc_attr($args['content'] . '-currency'); ?>" />                                 
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset> 
        </div>
        <div class="insert-box">
            <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'cf7_2checkout')); ?>" />
            </div>
        </div>
        <?php
	}
	public function wpcf7_add_form_tag_2checkout(){
        wpcf7_add_form_tag('twocheckout', array($this, 'wpcf7_2checkout_form_tag_handler'), array('name-attr' => true));
	}
	public function wpcf7_2checkout_form_tag_handler($tag){
		wp_enqueue_script("cf7-2checkout");
		echo $this->custom_css();
		ob_start();

        if($this->check_keys()){
        
        $mode = get_option('cf7_2checkout_mode'); 
        $pubkey=''; $sellerid=''; $environment='sandbox';
		if($mode=='test'){
			$pubkey=get_option('cf7_test_2checkout_publickey');
			$sellerid=get_option('cf7_test_2checkout_sellerid');
			$environment='sandbox';
		}
		else{
			$pubkey=get_option('cf7_live_2checkout_publickey');
			$sellerid=get_option('cf7_live_2checkout_sellerid');
			$environment='production';
		}	
		
        $price = 0;
        $currency='USD';
        if (isset($tag['values']['0']) && $tag['values']['0']) {
            $price = $tag['values']['0'];
            $currency = (isset($tag['name']) && !empty($tag['name'])) ? explode(':', $tag['name'])[1] : '';
        }
        include CF72CHECKOUT_PLUGIN_PATH . 'includes/template/cf7-2checkout-form.php';
        $html = ob_get_contents();
         }
        else{
            $html=__('2checkout API credentials are missing','cf7_2checkout');
        }
        ob_get_clean();
        return $html;
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
	public function cf7_2checkout_validation($result, $tag){
		$mode=get_option('cf7_2checkout_mode');
		$privkey=''; $pubkey=''; $sellerid='';
		$post_id=$_POST['_wpcf7'];
		$price = $_POST['2chcekout_price'];
		$currency = $_POST['2chcekout_currency'];
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
		if(!empty($privkey) && !empty($pubkey) && !empty($sellerid)){
			Twocheckout::privateKey($privkey); //Private Key
			Twocheckout::sellerId($sellerid); // 2Checkout Account Number
			if($mode=='test'){
				Twocheckout::sandbox(true); // Set to false for production accounts.
			}else{
				Twocheckout::sandbox(false); // Set to false for production accounts.
			}
			
			$addr=get_post_meta($post_id,'cf7_2checkout_address_fields',true);
			$name=(isset($addr['2checkout_name']) && !empty($addr['2checkout_name'])) ? $addr['2checkout_name'] : '';
			$address=(isset($addr['2checkout_address']) && !empty($addr['2checkout_address'])) ? $addr['2checkout_address'] : '';
			$email=(isset($addr['2checkout_email']) && !empty($addr['2checkout_email'])) ? $addr['2checkout_email'] : '';
			$city=(isset($addr['2checkout_city']) && !empty($addr['2checkout_city'])) ? $addr['2checkout_city'] : '';
			$state=(isset($addr['2checkout_state']) && !empty($addr['2checkout_state'])) ? $addr['2checkout_state'] : '';
			$zipcode=(isset($addr['2checkout_zipcode']) && !empty($addr['2checkout_zipcode'])) ? $addr['2checkout_zipcode'] : '';
			$country=(isset($addr['2checkout_country']) && !empty($addr['2checkout_country'])) ? $addr['2checkout_country'] : '';
			if(!empty($name))
				$name= isset($_POST[$name]) ? $_POST[$name] : '';
			else
				$name='';

			if(!empty($address))
				$address= isset($_POST[$address]) ? $_POST[$address] : '';
			else
				$address='';

			if(!empty($email))
				$email= isset($_POST[$email]) ? $_POST[$email] : '';
			else
				$email='';

			if(!empty($city))
				$city= isset($_POST[$city]) ? $_POST[$city] : '';
			else
				$city='';

			if(!empty($state))
				$state= isset($_POST[$state]) ? $_POST[$state] : '';
			else
				$state='';

			if(!empty($zipcode))
				$zipcode= isset($_POST[$zipcode]) ? $_POST[$zipcode] : '';
			else
				$zipcode='';

			if(!empty($country))
				$country= isset($_POST[$country]) ? $_POST[$country] : '';
			else
				$country='';
				
		    try {
		        $charge = Twocheckout_Charge::auth(array(
		            "merchantOrderId" => $_POST['twocheckout_ccno'],
		            "token"      => $_POST['token'],
		            "currency"   => $currency,
		            "total"      => $price,
		           "billingAddr" => array(
		                "name" => $name,
		                "addrLine1" => $address,
		                "city" => $city,
		                "state" => $state,
		                "zipCode" => $zipcode,
		                "country" => $country,
		                "email" => $email,
		              //  "phoneNumber" => '555-555-5555'
		            ) 
		        ));
		        
		        if ($charge['response']['responseCode'] == 'APPROVED') {
		           $_POST['cf7_2checkout_transaction_id']=$charge['response']['transactionId'];
		           $_POST['cf7_2checkout_order_number']=$charge['response']['orderNumber'];
		        }
		    } catch (Twocheckout_Error $e) {
		    	$message='';
		        $message=$e->getMessage();
		        $result->invalidate($tag, $message);
		    }
		}
		return $result;
	}
	public function cf7_2checkout_add_transaction_to_email($wpcf7_data){
		if (isset($_POST['cf7_2checkout_transaction_id']))
                $wpcf7_data['body'] = str_replace('[cf7_2checkout_transaction_id]', $_POST['cf7_2checkout_transaction_id'], $wpcf7_data['body']);

        if (isset($_POST['cf7_2checkout_order_number']))
                $wpcf7_data['body'] = str_replace('[cf7_2checkout_order_number]', $_POST['cf7_2checkout_order_number'], $wpcf7_data['body']);

        return $wpcf7_data;
	}
}