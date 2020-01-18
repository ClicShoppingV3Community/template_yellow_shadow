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
  <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/Product">
    <div class="col-md-2 float-md-left">
      <div class="ModulesFrontPageBoostrapLine1Image">
        <?php echo $products_image; ?>
      </div>
    </div>
    <div class="col-md-6 float-md-left">
      <div>
        <div class="ModulesFrontPageBoostrapLine1Title"><span itemprop="name"><h3><?php echo $products_name; ?></h3></span></div>
      </div>
<?php
  if (!empty($products_short_description)) {
?>
      <div>
        <div class="ModulesFrontPageBoostrapLine1ShortDescription"><span itemprop="description"><?php echo $products_short_description; ?></span></div>
      </div>
<?php
  }
?>
      <div class="text-md-center">
<?php
  if (!empty($products_stock)) {
?>
        <div class="col-md-4 ModulesFrontPageBoostrapLine1StockImage"><?php echo $products_stock; ?></div>
<?php
  }
  if (!empty($products_flash_discount)) {
?>
        <div class="col-md-4 ModulesProductsFavoritesBoostrapLine1FlashDiscount EndDateFlashDiscount"><?php echo $products_flash_discount; ?></div>
<?php
  }
?>
      </div>
    </div>
    <div class="col-md-4 float-md-right">
      <div class="text-md-center">
        <div class="ModulesFrontPageBoostrapLine1TextPrice" itemprop="offers" itemscope itemtype="https://schema.org/Offer"><?php echo CLICSHOPPING::getDef('text_price') . ' ' . $product_price . $ticker; ?></div>
      </div>
      <div>
        <div class="ModulesFrontPageBoostrapLine1ProductsQuantityUnit"><?php echo $products_quantity_unit; ?></div>
      </div>
<?php
  if (!empty($min_order_quantity_products_display)) {
?>
      <div>
        <div class="ModulesFrontPageBoostrapLine1QuantityMinOrder"><?php echo $min_order_quantity_products_display; ?></div>
      </div>
<?php
  }
?>
      <?php echo $form; ?>
        <div class="text-md-center">
          <div class="ModulesFrontPageBoostrapLine1QuantityMinOrder"><?php echo $input_quantity; ?></div>
          <div style="padding-top:5px;">
            <span class="ModulesFrontPageBoostrapLine1ViewDetails"><?php echo $button_small_view_details; ?></span>
            <span class="ModulesFrontPageBoostrapLine1SubmitButton"><?php echo $submit_button; ?></span>
          </div>
        </div>
      <?php echo $endform; ?>
    </div>
  </div>
  <div class="hr"></div>
</div>
