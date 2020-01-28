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
<div class="col-md-<?php echo $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?>">
  <div>
    <div class="col-md-2 float-md-left">
      <div class="text-md-center ModulesIndexCategoriesBoostrapLine1Image">
        <?php echo $products_image; ?>
      </div>
    </div>
    <div class="col-md-6 float-md-left">
      <div>
        <div class="text-md-center ModulesIndexCategoriesBoostrapLine1Title"><h3><?php echo $products_name; ?></h3></div>
      </div>
<?php
  if (!empty($products_short_description)) {
?>
      <div>
        <div class="ModulesIndexCategoriesBoostrapLine1ShortDescription"><?php echo $products_short_description; ?></div>
      </div>
<?php
  }
?>
      <div class="text-md-center">
<?php
  if (!empty($products_stock)) {
?>
        <div class="col-md-4 ModulesIndexCategoriesBoostrapLine1StockImage"> <?php echo $products_stock; ?></div>
<?php
  }
  if (!empty($products_flash_discount)) {
?>
        <div class="col-md-4 ModulesIndexCategoriesBoostrapLine1FlashDiscount EndDateFlashDiscount"><?php echo $products_flash_discount; ?></div>
<?php
  }
?>
      </div>
    </div>
    <div class="col-md-4 float-md-right">
      <div class="text-md-center">
        <div class="ModulesIndexCategoriesBoostrapLine1TextPrice"><?php echo CLICSHOPPING::getDef('text_price') . ' ' . $product_price . $ticker; ?></div>
      </div>
      <div>
        <div class="ModulesIndexCategoriesBoostrapLine1ProductsQuantityUnit"><?php echo $products_quantity_unit; ?></div>
      </div>
<?php
  if (!empty($min_order_quantity_products_display)) {
?>
      <div>
        <div class="ModulesIndexCategoriesBoostrapLine1QuantityMinOrder"><?php echo $min_order_quantity_products_display; ?></div>
      </div>
<?php
  }
?>
      <?php echo $form; ?>
        <div class="text-md-center">
          <div class="ModulesIndexCategoriesBoostrapLine1QuantityMinOrder"><?php echo $input_quantity; ?></div>
          <div style="padding-top:5px;">
            <span class="ModulesIndexCategoriesBoostrapLine1ViewDetails"><label for="ModulesIndexCategoriesBoostrapLine1ViewDetails"><?php echo $button_small_view_details; ?></label>&nbsp;</span>
            <span class="ModulesIndexCategoriesBoostrapLine1SubmitButton"><label for="ModulesIndexCategoriesBoostrapLine1SubmitButton"><?php echo $submit_button; ?></label></span>
          </div>
        </div>
      <?php echo $endform; ?>
    </div>
  </div>
  <div class="hr"></div>
</div>
