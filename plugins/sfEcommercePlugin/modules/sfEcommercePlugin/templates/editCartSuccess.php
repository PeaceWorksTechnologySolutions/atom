<?php decorate_with('layout_2col') ?>
<?php use_stylesheet('/plugins/sfEcommercePlugin/css/cart.css'); ?>

<?php slot('sidebar') ?>
  <section>
    <h3><?php echo __('Browse by') ?></h3>
    <ul>
      <?php $browseMenu = QubitMenu::getById(QubitMenu::BROWSE_ID) ?>
      <?php if ($browseMenu->hasChildren()): ?>
        <?php foreach ($browseMenu->getChildren() as $item): ?>
          <li>
            <a href="<?php echo url_for($item->getPath(array('getUrl' => true, 'resolveAlias' => true))) ?>">
              <?php echo $item->getLabel(array('cultureFallback' => true)) ?>
            </a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </section>
<?php end_slot() ?>

<?php slot('title') ?>
  <h1>Cart</h1>
<?php end_slot() ?>

<?php slot('content') ?>
  <section id="cart_full">
  <?php if (count($resources) > 0) { ?>
  <div class="cart">
    <? foreach ($resources as $resource ) { ?>
    <div class="cart_item">
        <div class="cart_thumbnail">
            <?php echo link_to(image_tag($resource->digitalObjects[0]->thumbnail->getFullPath()), array('module' => 'informationobject', 'slug' => $resource->slug)); ?>
        </div>
        <div class="cart_full_item_description">
            <div class="cart_title">
                <?php echo render_title($resource->title) ?>
            </div>
            <div class="cart_referenceCode">
                <?php echo $resource->referenceCode ?>
            </div>

            <?php $price = sfEcommercePlugin::resource_price($resource);  ?>
            <div class="cart_price">Price: <?php echo money_format("%.2n", $price) ?></div>

            <div class="cart_item_remove">
            <? echo link_to('Remove', array('module' => 'sfEcommercePlugin', 'action' => 'removeCart', 'slug' => $resource->slug)) ?>
            </div>
        </div>
    </div>
    <?php } ?>
  </div>
  <?php } else { ?>
    <p>Your cart is empty.</p>
  <?php } ?>
  </section>
<?php end_slot() ?>
