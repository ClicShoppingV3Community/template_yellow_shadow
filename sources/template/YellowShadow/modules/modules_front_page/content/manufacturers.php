<div class="col-md-<?php echo $bootstrap_column; ?> col-md-<?php echo $bootstrap_column; ?>">
  <div class="separator"></div>
  <div class="card-wrapper">
    <div class="card"
>
      <div class="card shadow">
        <div class="card-block">
          <div class="separator"></div>
          <div class="card-img-top ModulesFrontPageBoostrapManufacturerImages">
<?php
    if (MODULE_FRONT_PAGE_MANUFACTURERS_DISPLAY == 'True') {
?>
            <div class="row">
              <div class="col-md-12 text-center">
                <?php echo $image; ?>
              </div>
            </div>
<?php
    }
    if (MODULE_FRONT_PAGE_MANUFACTURERS_DISPLAY_TITLE == 'True') {
?>
            <div class="moduleFrontPageManufacturerText"><h3><?php echo $manufacturer; ?></h3></div>
<?php
    }
?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
