<div class="cart">
<?php if (0 < count($resource->digitalObjects) ) { ?>
    <?php 
        $price = sfEcommercePlugin::resource_price($resource);
        if ($may_disseminate && isset($price)) { ?>
        <div class="purchase ecommerce-button"><?php echo link_to(__('Purchase this image'), array($resource, 'module' => 'sfEcommercePlugin', 'action' => 'addCart')) ?></div>
        <div class="price">Price: <?php echo money_format("%.2n", $price) ?></div>
        <div class="megapixels">Megapixels: <?php echo round($megapixels, 1) ?></div>
        <div class="resolution">Resolution: <?php echo $resolution['width'] . 'x' . $resolution['height'] ?></div>
    <?php } else { ?>
        <p>This photo has restrictions and may not be ordered online. Please contact the archives for permission to order the photo.</p>
    <?php } ?>

    <?php 
    //$var = print_r(sfConfig::get("feature_jasonarc_price"), true);
    //sfContext::getInstance()->getLogger()->warning('feature_jasonarc: ' . $var);
    //$var = print_r($resource->repository, true);
    //sfContext::getInstance()->getLogger()->warning($var);

    ?>
<?php } ?>
</div>
