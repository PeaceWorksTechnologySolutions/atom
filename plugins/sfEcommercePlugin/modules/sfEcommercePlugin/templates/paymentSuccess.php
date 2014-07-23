<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
  <h1>Payment</h1>
<?php end_slot() ?>

<?php slot('content') ?>

    test.

    <p><?php echo sfConfig::get("ecommerce_paypal_form");  ?></p>

<?php end_slot() ?>
