<div class="col-md-<?php echo  $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?>">
  <div class="separator"></div>
  <div class="card-deck-wrapper">
    <div class="card-deck">
      <div class="card shadow">
        <div class="card-block">
          <div class="separator"></div>
          <div class="card-img-top ModulesFrontPageBoostrapCategoriesImages">
<?php
  if (MODULE_FRONT_PAGE_CATEGORIES_IMAGES_DISPLAY == 'True') {
?>
              <div class="text-md-center">
                <h3><?php echo $link_categories_image; ?></h3>
              </div>
<?php
  }
  if (MODULE_FRONT_PAGE_CATEGORIES_IMAGES_DISPLAY_TITLE == 'True') {
?>
              <div class="text-md-center moduleFrontPageCategoriesText">
                <h3><span itemprop="itemListElement"><?php echo $link_categories; ?></span></h3>
              </div>
<?php
  }
?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>