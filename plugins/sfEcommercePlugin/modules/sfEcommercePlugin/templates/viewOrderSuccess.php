<?php decorate_with('layout_1col.php') ?>
<?php use_stylesheet('/plugins/sfEcommercePlugin/css/cart.css'); ?>

<?php slot('title') ?>
  <h1>Order <? echo $resource->getId() ?></h1>
<?php end_slot() ?>

<?php slot('content') ?>

    <form action="" method="post">

    <section id="content">

      <fieldset id="customerInformation">

        <legend><?php echo __('Customer Information') ?></legend>

        <div id="customerInformation">
          <div class="field"><?php echo $resource->firstName . ' ' . $resource->lastName ?></div>
          <div class="field space_before"><?php echo $resource->address1 ?></div>
          <?php if (!empty($resource->address2)) { ?>
          <div class="field"><?php echo $resource->address2 ?></div>
          <?php } ?>
          <div class="field"><?php echo $resource->city . ", " . $resource->province ?></div>
          <div class="field"><?php echo $resource->postalCode ?></div>
          <div class="field"><?php echo sfCultureInfo::getInstance()->getCountry($resource->country) ?></div>

          <div class="field space_before"><?php echo $resource->email ?></div>
          <div class="field"><?php echo $resource->phone ?></div>

          
        </div>

      </fieldset>

      <fieldset id="orderPhotos">

        <legend><?php echo __('Photos') ?></legend>

        <? foreach ($resources as $index => $item ) { ?>
        <div class="cart_item">
            <div class="cart_thumbnail">
                <?php echo link_to(image_tag($item->digitalObjects[0]->thumbnail->getFullPath()), array('module' => 'informationobject', 'slug' => $item->slug)); ?>
            </div>
            <div class="cart_item_description">
                <div class="cart_title cart_truncate">
                    <?php echo render_title($item->title) ?>
                </div>
                <div class="cart_referenceCode">
                    <?php echo link_to($item->referenceCode, array('module' => 'informationobject', 'slug' => $item->slug)); ?>
                </div>
                <?php if ($allResourcesProcessed) { ?>
                  <?php echo $saleResources[$index]->processingStatus ?>
                <?php } else { ?>
                <div class="confirm">
                  <select name="confirm_<?php echo $item->getId() ?>"><option value="accept">Accept</option><option value="reject">Reject</option></select>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

      </fieldset>

      <?php if (!$allResourcesProcessed) { ?>
      <fieldset id="note">
        <legend>Note (will be sent to customer)</legend>
          <textarea name="note"></textarea>
      </fieldset>
      <?php } ?>

    </section>

    <?php if (!$allResourcesProcessed) { ?>
    <div id="instructions">
      <p>When you click Process order, any rejected items will be automatically refunded.</p>
      <p>The customer will be sent an email which will list rejected items (if any), and will provide them
      with a link to download accepted items (if any).</p>
      <p>Your Note (above) will be included in the email as "Note from the Archives".</p>
    </div>
    <?php } ?>

    <section class="actions">
      <ul>
        <? if ($resource->processingStatus == 'paid' and !$allResourcesProcessed) { ?>
        <li><?php echo link_to(__('Cancel changes'), '@homepage', array('title' => __('Cancel changes'), 'class' => 'c-btn')) ?></li>
        <li><input class="c-btn c-btn-submit" type="submit" name="process" value="<?php echo __('Process order') ?>"/></li>
        <? } elseif (   $resource->processingStatus == 'cancelled' 
                     || $resource->processingStatus == 'refunded'
                     || $resource->processingStatus == 'processed' ) { ?>
          <li><input class="c-btn c-btn-submit" type="submit" name="anonymize" onclick="return confirm('Are you sure you want to remove personal information?')" value="<?php echo __('Remove personal information') ?>"/></li>
          <li><?php echo link_to(__('Return to Orders'), array('module' => 'sfEcommercePlugin', 'action' => 'browseOrders'), array('title' => __('Return to Orders'), 'class' => 'c-btn')) ?></li>
        <? } ?>
      </ul>
    </section>

  </form>

<?php end_slot() ?>
