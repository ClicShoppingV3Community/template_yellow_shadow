<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT

 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;

  $CLICSHOPPING_Template = Registry::get('Template');
  $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

// ----------------------------------------------------------------//
//                      file not found                             //
// ----------------------------------------------------------------//

  if ($CLICSHOPPING_ProductsCommon->getProductsCount() < 1 || (\is_null($CLICSHOPPING_ProductsCommon->getID())) || $CLICSHOPPING_ProductsCommon->getID() === false ) {
  header('HTTP/1.0 404 Not Found');
?>
 <section class="product" id="product">
  <div class="contentContainer">
    <div class="contentText">
      <div class="separator"></div>
      <div class="separator"></div>
      <div class="alert alert-warning text-center" role="alert">
         <h3><?php echo CLICSHOPPING::getDef('text_product_not_found'); ?></h3>
      </div>
      <div class="separator"></div>
      <div class="control-group">
        <div>
          <div class="buttonSet">
            <span class="float-end"><label for="buttonContinue"><?php echo HTML::button(CLICSHOPPING::getDef('button_continue'), null, CLICSHOPPING::link(),'success'); ?></label></span>
          </div>
        </div>
      </div>
    </div>
  </div>
 </section>
<?php
  }
// ----------------------------------------------------------------
// ---- Affiche la fiche produit selon les autorisations   ----
// ------------------------------------------------------------

    if ($CLICSHOPPING_ProductsCommon->getProductsGroupView() == 1 ||  $CLICSHOPPING_ProductsCommon->getProductsView() == 1) {
      require_once($CLICSHOPPING_Template->getTemplateFiles('breadcrumb'));
      $CLICSHOPPING_ProductsCommon->countUpdateProductsView();
?>
<section class="product" id="product">
  <div class="contentContainer">
    <div class="contentText">
      <div class="productsInfoContent">
        <?php echo $CLICSHOPPING_Template->getBlocks('modules_products_info'); ?>
        <div class="separator"></div>
      </div>
    </div>
  </div>
</section>
<?php
    }
