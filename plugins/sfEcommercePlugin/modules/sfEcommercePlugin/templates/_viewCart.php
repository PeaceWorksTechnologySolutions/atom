<?php use_stylesheet('/plugins/sfEcommercePlugin/css/cart.css'); ?>

<section id="cart">

  <?php if (count($resources) > 0) { ?>
  <h3 class="title"><?php echo __('Cart') ?> </h3>
  <div class="cart_action"><?php echo link_to('Edit', array('module' => 'sfEcommercePlugin', 'action' => 'editCart')) ?></div>
  <div class="cart_action cart_checkout"><?php echo link_to('Check out', array('module' => 'sfEcommercePlugin', 'action' => 'checkout')) ?></div>
  <div class="cart">
    <? foreach ($resources as $resource ) { ?>
    <div class="cart_item">
        <div class="cart_thumbnail">
            <?php //echo link_to(image_tag($resource->digitalObjects[0]->thumbnail->getFullPath()), array('module' => 'informationobject', 'slug' => $resource->slug)); ?>
            <?php echo image_tag($resource->digitalObjects[0]->thumbnail->getFullPath()); ?>
        </div>
        <div class="cart_item_description">
            <div class="cart_title cart_truncate">
                <?php echo render_title($resource->title) ?>
            </div>
            <div class="cart_referenceCode">
                <?php echo $resource->referenceCode ?>
            </div>
        </div>
    </div>
    <?php } ?>
  </div>
  <?php } ?>

</section>
