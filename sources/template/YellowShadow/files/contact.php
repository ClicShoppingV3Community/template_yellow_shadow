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

  if ( $CLICSHOPPING_MessageStack->exists('main') ) {
    echo $CLICSHOPPING_MessageStack->get('main');
  }

  require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
?>
<section class="contact" id="contact">
  <div class="contentContainer">
    <div class="contentText card shadow">
      <?php echo $CLICSHOPPING_Template->getBlocks('modules_contact_us'); ?>
      <div class="separator"></div>
    </div>
  </div>
</section>
