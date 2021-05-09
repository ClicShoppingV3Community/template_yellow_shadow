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
  <div class="card-wrapper">
    <div class="card"
>
      <div class="card card-footer">
          <div>
            <?php echo $ticker; ?>
            <div class="col-md-3 float-start">
              <div class="ModulesFrontPageBoostrapColumn6Image"> <?php echo $products_image; ?></div>
            </div>
            <div class="col-md-9 float-end">
              <div class="ModulesFrontPageBoostrapColumn6Title"><h3><?php echo $products_name; ?></h3></div>
              <div class="separator"></div>
              <div class="separator"></div>
<?php
  if (!empty($products_short_description)) {
?>
              <div class="ModulesFrontPageBoostrapColumn6ShortDescription"><h3><?php echo $products_short_description; ?></h3></div>
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
              <ul>
<?php
  if (!empty($min_order_quantity_products_display)) {
?>
                <div class="ModulesFrontPageBoostrapColumn6QuantityMinOrder"><?php echo  $min_order_quantity_products_display; ?></div>
<?php
  }
?>
                <div class="ModulesFrontPageBoostrapColumn6TextPrice" ><?php echo CLICSHOPPING::getDef('text_price') . ' ' . $product_price; ?></div>
                <li class="float-end">
                  <?php echo  $form; ?>
                  <div class="ModulesFrontPageBoostrapColumn6QuantityMinOrder"><?php echo $input_quantity; ?></div>
                  <span class="ModulesFrontPageBoostrapColumn6ViewDetails"><label for="ModulesFrontPageBoostrapColumn6ViewDetails"><?php echo $button_small_view_details; ?></label>&nbsp;</span>
                  <span class="ModulesFrontPageBoostrapColumn6SubmitButton"><label for="ModulesFrontPageBoostrapColumn6SubmitButton"><?php echo $submit_button; ?></label></span>
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