<input id="twocheckout_token" name="token" type="hidden" value="">
<input id="2chcekout_price" name="2chcekout_price" type="hidden" value="<?php echo $price; ?>">
<input id="2chcekout_currency" name="2chcekout_currency" type="hidden" value="<?php echo $currency; ?>">
<div>
    <label>
        <span><?php _e('Card Number','cf7_2checkout'); ?></span>
    </label>
    <input id="ccNo" type="text" name="twocheckout_ccno" size="20" value="" autocomplete="off" required placeholder="<?php _e('Card Number','cf7_2checkout'); ?>" />
</div>
<div>
    <label>
        <span><?php _e('Expiration Date (mm/yyyy)','cf7_2checkout'); ?></span>
    </label>
    <div class="card_expiry_fields">
        <div class="month">
        <input type="text" size="2" id="expMonth" required placeholder="mm"/></div>
        <span> / </span>
        <div class="year"><input type="text" size="2" id="expYear" required  placeholder="yyyy"/></div>
    </div>
</div>
<div>
    <label>
        <span><?php _e('CVV','cf7_2checkout'); ?></span>
    </label>
    <input id="cvv" size="4" type="text" value="" autocomplete="off" required placeholder="CVV"/>
</div>
<div id="cf7_2checkout_erros">

</div>
<script>
    // Called when token created successfully.
    var successCallback = function(data) {
        var cf7Form = jQuery('.wpcf7-form');
        // Set the token as the value for the token input
        jQuery('#twocheckout_token').val(data.response.token.token);
        jQuery('.wpcf7-submit').removeAttr('disabled');
        cf7Form.submit();
    };
    // Called when token creation fails.
    var errorCallback = function(data) {
        if (data.errorCode === 200) {
            tokenRequest();
        } else {
            jQuery('div.wpcf7 .ajax-loader').toggleClass('is-active');
             jQuery('#cf7_2checkout_erros').html('<span role="alert" class="wpcf7-not-valid-tip">'+data.errorMsg+'</span>');
             jQuery('.wpcf7-submit').removeAttr('disabled');
        }
    };
    var tokenRequest = function() {
        // Setup token request arguments
        var args = {
            sellerId: '<?php echo $sellerid; ?>',
            publishableKey: '<?php echo $pubkey; ?>',
            ccNo: jQuery("#ccNo").val(),
            cvv: jQuery("#cvv").val(),
            expMonth: jQuery("#expMonth").val(),
            expYear: jQuery("#expYear").val()
        };
        // Make the token request
        TCO.requestToken(successCallback, errorCallback, args);
    };
    jQuery(function() {

        // Pull in the public encryption key for our environment
        TCO.loadPubKey('<?php echo $environment; ?>');
       

         jQuery('.wpcf7-submit').click(function (event) {
            // Call our token request function
            event.preventDefault();
            jQuery('.cf7s_container .messages').html("");
            jQuery(this).attr('disabled', 'disabled');

            tokenRequest();

            // Prevent form from submitting
            return false;
        });
    });
</script>