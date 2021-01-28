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

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));

  if ($CLICSHOPPING_MessageStack->exists('checkout_success')) {
   echo $CLICSHOPPING_MessageStack->get('main');
  }
?>
<section class="checkout_success" id="checkout_success">
  <div class="contentContainer">
    <div class="contentText card shadow">
      <div class="separator"></div>
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_checkout_success'); ?>
      <div class="control-group">
        <div class="controls">
          <div class="buttonSet">
            <span class="float-end"><label for="buttonContinue"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(), 'success'); ?></label></span>
          </div>
        </div>
      </div>
      <div class="separator"></div>
    </div>
  </div>
</section>