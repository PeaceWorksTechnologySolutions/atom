<?php decorate_with('layout_1col.php') ?>
<?php if (!$contact_for_support) { 
        use_javascript('/plugins/sfEcommercePlugin/js/pending.js'); 
      } 
?>

<?php slot('title') ?>
  <?php if ($contact_for_support) { ?>
    <h1>Payment could not be confirmed</h1>
  <?php } else { ?>
    <h1>Confirming Payment</h1>
  <?php } ?>
<?php end_slot() ?>

<?php slot('content') ?>

  <?php if ($contact_for_support) { ?>
      <p>We have not been able to confirm your payment.</p>
      <p>Please <?php echo link_to(__('contact us'), array($resource, 'module' => 'staticpage', 'slug' => 'contact')) ?> for support.</p>
      <p>For reference, your Order number is <?php echo $resource->getId() ?></p>
  <?php } else { ?>
      <p>We are waiting for PayPal to confirm your payment.</p>
      <p>Please be patient and stand by...</p>
  <?php } ?>


<?php end_slot() ?>
