<?php decorate_with('layout_1col.php') ?>
<?php use_stylesheet('/plugins/sfEcommercePlugin/css/cart.css'); ?>

<?php slot('title') ?>
  <h1>Download Photos from Order <?php $resource->getId() ?></h1>
<?php end_slot() ?>

<?php slot('content') ?>

      <?php if (!$resource->allResourcesProcessed()) { ?>
          <p style="font-size: 15px; color: red"><b>IMPORTANT: some photos in your order have not yet been processed!</b></p>
          <p> The list below shows <b>only</b> the photos which have been processed so far.  You will receive further email when the remaining photos are processed.</p>
      <?php } ?>

      <fieldset id="downloadPhotos">

        <legend></legend>

        <?php if (count($resources) > 0) { ?>
        <div class="download_all"><?php echo link_to('Download All Images (as ZIP)', array('module' => 'sfEcommercePlugin', 'action' => 'download', 'id' => $resource->getId(), 'zip' => '1', 'hash' => $hash)) ?>
        </div>
        <?php } ?>

        <?php foreach ($resources as $item ) { ?>
        <div class="cart_item">
            <div class="cart_thumbnail">
                <?php echo image_tag($item->digitalObjects[0]->thumbnail->getFullPath()); ?>
            </div>
            <div class="cart_item_description">
                <div class="cart_title cart_truncate">
                    <?php echo render_title($item->title) ?>
                </div>
                <div class="cart_referenceCode">
                    <?php echo $item->referenceCode; ?>
                </div>
                <div class="cart_referenceCode">
                    <?php echo link_to('Download Image', array('module' => 'sfEcommercePlugin', 'action' => 'download', 'id' => $resource->getId(), 'photo' => $item->getId(), 'hash' => $hash)) ?>
                </div>
            </div>
        </div>
        <?php } ?>


      </fieldset>

  </form>

<?php end_slot() ?>
