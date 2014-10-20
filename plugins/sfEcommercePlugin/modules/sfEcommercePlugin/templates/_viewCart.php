<div id="ecommerce-cart-menu">
  <a class="top-item top-dropdown" data-target="#" data-toggle="dropdown">Cart (<?php echo count($resources) ?>)</a>
  
    <div class="top-dropdown-container">
      <div class="top-dropdown-arrow">
        <div class="arrow"></div>
      </div>
 
      <?php if (count($resources) == 0) { ?>
        <div class="top-dropdown-header">
          Your cart is empty.
        </div>

      <?php } else { ?>
        <div class="top-dropdown-header">
          Cart Contents  <div class="cart-subtotal">Subtotal $<?php echo $subtotal ?></div>
        </div>

        <div class="top-dropdown-body">

          <div class="cart_action ecommerce-button"><?php echo link_to('Edit', array('module' => 'sfEcommercePlugin', 'action' => 'editCart')) ?></div>
          <div class="cart_action cart_checkout ecommerce-button"><?php echo link_to('Check out', array('module' => 'sfEcommercePlugin', 'action' => 'checkout')) ?></div>

          <div class="cart">
            <? foreach ($resources as $resource ) { ?>
            <div class="cart_item">
                <div class="cart_thumbnail">
                    <?php echo link_to(image_tag($resource->digitalObjects[0]->thumbnail->getFullPath()), array('module' => 'informationobject', 'slug' => $resource->slug)); ?>
                </div>
                <div class="cart_item_description">
                    <div class="cart_title cart_truncate">
                        <?php echo link_to(render_title($resource->title), array('module' => 'informationobject', 'slug' => $resource->slug));  ?>
                    </div>
                    <div class="cart_referenceCode">
                        <?php echo $resource->referenceCode ?>
                    </div>
                </div>
            </div>
            <?php } ?>
          </div>


        </div>

        <div class="top-dropdown-bottom"></div>

      <?php } ?>
    </div>

  
</div>

