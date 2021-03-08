<div class="col-md-<?php echo  $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?>">
  <div class="separator"></div>
  <div class="card-wrapper">
    <div class="card"
>
      <div class="card shadow">
        <div class="card-block">
          <div class="separator"></div>
          <div class="card-img-top ModulesFrontPageBoostrapCategoriesImages">
<?php
  if (MODULE_FRONT_PAGE_CATEGORIES_IMAGES_DISPLAY == 'True') {
?>
              <div class="text-center">
                <h3><?php echo $link_categories_image; ?></h3>
              </div>
<?php
  }
  if (MODULE_FRONT_PAGE_CATEGORIES_IMAGES_DISPLAY_TITLE == 'True') {
?>
              <div class="text-center moduleFrontPageCategoriesText">
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