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

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;

  if (defined('MODULE_LOGIN_MODE_B2B_B2C_STATUS')) {
    echo '<div style="padding-top:3rem;"></div>';
  }
?>
<div class="col-md-<?php echo $content_width . ' ' . MODULE_LOGIN_MODE_GUEST_ACCOUNT_POSITION; ?>">
  <div class="card shadow">
    <div class="card-header">
      <div class="loginModeGuestAccount"><?php echo CLICSHOPPING::getDef('heading_title_guest_account'); ?></div>
    </div>
    <div class="card-block">
      <div class="separator"></div>
      <div class="card-text">
        <div><?php echo CLICSHOPPING::getDef('text_intro_guest_customer'); ?></div>
        <div class="text-rmd-ight">
          <div class="control-group">
            <div class="separator"></div>
            <div class="controls">
              <div class="buttonSet text-md-right"><?php echo HTML::button(CLICSHOPPING::getDef('button_text_guest_customer'), null, CLICSHOPPING::link(null, 'Account&CreateGuestAccount'), 'info'); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
