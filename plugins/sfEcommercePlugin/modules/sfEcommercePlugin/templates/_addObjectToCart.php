<div class="cart">
<?php if (0 < count($resource->digitalObjects) ) { ?>
    <?php 
        $price = sfEcommercePlugin::resource_price($resource);
        if ($may_disseminate && isset($price)) { ?>
        <p>Price: <?php echo money_format("%.2n", $price) ?></p>
        <p><?php echo link_to(__('Purchase this image'), array($resource, 'module' => 'sfEcommercePlugin', 'action' => 'addCart')) ?></p>
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
