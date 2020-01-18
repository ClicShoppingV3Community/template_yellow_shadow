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
  <div class="card-deck-wrapper" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/Product">
    <div class="card-deck">
      <div class="card card-footer">
          <div>
            <?php echo $ticker; ?>
            <div class="col-md-3 float-md-left">
              <div class="ModulesFrontPageBoostrapColumn6Image"> <?php echo $products_image; ?></div>
            </div>
            <div class="col-md-9 float-md-right">
              <div class="ModulesFrontPageBoostrapColumn6Title"><span itemprop="name"><h3><?php echo $products_name; ?></h3></span></div>
              <div class="separator"></div>
              <div class="separator"></div>
<?php
  if (!empty($products_short_description)) {
?>
              <div class="ModulesFrontPageBoostrapColumn6ShortDescription"><h3><span itemprop="description"><?php echo $products_short_description; ?></span></h3></div>
<?php
  }
?>
              <div>
<?php
  if (!empty($products_stock)) {
?>
                <div class="ModulesFrontPageBoostrapColumn6StockImage"><?php echo $products_stock; ?></div>
<?php
  }
  if (!empty($products_flash_discount)) {
?>
                <div class="EndDateFlashDiscount"> <?php echo $products_flash_discount; ?></div>
<?php
  }
?>
              </div>
              <div class="hr"></div>
              <ul class="list-inline">
<?php
  if (!empty($min_order_quantity_products_display)) {
?>
                <div class="ModulesFrontPageBoostrapColumn6QuantityMinOrder"><?php echo  $min_order_quantity_products_display; ?></div>
<?php
  }
?>
                <div class="ModulesFrontPageBoostrapColumn6TextPrice" itemprop="offers" itemscope itemtype="https://schema.org/Offer"><?php echo CLICSHOPPING::getDef('text_price') . ' ' . $product_price; ?></div>
                <li class="float-md-right">
                  <?php echo  $form; ?>
                  <div class="ModulesFrontPageBoostrapColumn6QuantityMinOrder"><?php echo $input_quantity; ?></div>
                  <span class="ModulesFrontPageBoostrapColumn6ViewDetails"><?php echo $button_small_view_details; ?></span>
                  <span class="ModulesFrontPageBoostrapColumn6SubmitButton"><?php echo $submit_button; ?></span>
                  <?php echo $endform; ?>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    <div class="separator"></div>
  </div>
</div>