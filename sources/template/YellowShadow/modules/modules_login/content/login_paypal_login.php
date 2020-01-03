<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\CLICSHOPPING;

?>

<div class="col-md-<?php echo $content_width; ?>">
  <div class="contentText">
    <h2><?php echo CLICSHOPPING::getDef('module_content_paypal_login_template_title'); ?></h2>

<?php
  if (CLICSHOPPING_APP_PAYPAL_LOGIN_STATUS == '0') {
    echo '    <p class="alert alert-danger" role="alert">' . CLICSHOPPING::getDef('module_content_paypal_login_template_sandbox') . '</p>';
  }
?>
      <p><?php echo CLICSHOPPING::getDef('module_content_paypal_login_template_content'); ?></p>
      <div id="PayPalLoginButton" class="text-md-right"></div>
  </div>
</div>

  <script src="https://www.paypalobjects.com/js/external/api.js"></script>
  <script>
  paypal.use( ["login"], function(login) {
    login.render ({

<?php
  if (CLICSHOPPING_APP_PAYPAL_LOGIN_STATUS == '0') {
    echo '    "authend": "sandbox",';
  }

  if ( CLICSHOPPING_APP_PAYPAL_LOGIN_THEME == 'Neutral' ) {
    echo '    "theme": "Neutral",';
  }
?>
    "locale": "<?php echo CLICSHOPPING::getDef('module_content_paypal_login_language_locale'); ?>",
    "appid": "<?php echo (CLICSHOPPING_APP_PAYPAL_LOGIN_STATUS == '1') ? CLICSHOPPING_APP_PAYPAL_LOGIN_LIVE_CLIENT_ID : CLICSHOPPING_APP_PAYPAL_LOGIN_SANDBOX_CLIENT_ID; ?>",
    "scopes": "<?php echo implode(' ', $use_scopes); ?>",
    "containerid": "PayPalLoginButton",
    "returnurl": "<?php echo str_replace('&amp;', '&', CLICSHOPPING::link(null, 'Account&Login&action=paypal_login', false, false)); ?>"
    });
  });
  </script>
