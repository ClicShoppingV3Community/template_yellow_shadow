<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT

 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
?>
<div class="clearfix"></div>
<div class="col-md-<?php echo $content_width; ?>">
  <div class="separator"></div>
  <div class="ModuleLoginInfoCustomers">
    <table width="100%"  border="0" cellspacing="0" cellpadding="5">
      <tr>
        <th width="20%"><strong><?php echo CLICSHOPPING::getDef('module_login_info_customers_heading_payment'); ?></th>
        <th width="20%"><strong><?php echo CLICSHOPPING::getDef('module_login_info_customers_heading_shipping'); ?></strong></th>
        <th width="20%"><strong><?php echo CLICSHOPPING::getDef('module_login_info_customers_heading_private'); ?></strong></th>
        <th width="20%"><strong><?php echo CLICSHOPPING::getDef('module_login_info_customers_heading_contact_us'); ?></strong></th>
      </tr>
      <tr valign="top">
        <td><?php echo CLICSHOPPING::getDef('module_login_info_customers_text_payment'); ?><p class="text-align:center"><?php echo HTML::image($CLICSHOPPING_Template->getDirectoryTemplateImages() . 'logos/payment/3_cb.png'); ?></p></td>
        <td><?php echo CLICSHOPPING::getDef('module_login_info_customers_text_shipping') . $free_amount; ?></td>
        <td><?php echo CLICSHOPPING::getDef('module_login_info_customers_text_private'); ?></td>
        <td><?php echo CLICSHOPPING::getDef('module_login_info_customers_heading_contact_us'); ?>
          <?php echo HTML::link(CLICSHOPPING::link(null,'Info&Contact'), CLICSHOPPING::getDef('module_login_info_customers_text_contact_us')) ;?><br />
          <?php echo STORE_OWNER; ?><br />
          <?php echo nl2br(STORE_NAME_ADDRESS); ?>
        </td>
      </tr>
    </table>
  </div>
</div>
