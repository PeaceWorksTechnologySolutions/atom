<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
  <h1>Payment</h1>
<?php end_slot() ?>

<?php slot('content') ?>

    <form name="paypalform" action="<?php echo sfConfig::get("ecommerce_paypal_url") ?>" 
          accept-charset="UTF=8" method="post">
      <?php $i = 1;
            foreach ($sale_items as $repo => $info) { ?>
              <input type="hidden" name="item_name_<?php echo $i?>" value="Photo(s) from <?php echo htmlspecialchars($info['name']) ?>"/>
              <input type="hidden" name="amount_<?php echo $i?>" value="<?php echo $info['amount'] ?>"/>
              <input type="hidden" name="quantity_<?php echo $i?>" value="<?php echo $info['quantity'] ?>"/>
      <?php 
              $i += 1;
            } 
      ?>
      <input type="hidden" name="tax_cart" value="<?php echo $taxAmount ?>"/>
      <input type="hidden" name="charset" value="utf-8">
      <input type="hidden" name="cmd" value="_cart"/>
      <input type="hidden" name="upload" value="1"/>
      <input type="hidden" name="business" value="<?php echo sfConfig::get("ecommerce_paypal_email") ?>"/>

      <input type="hidden" name="currency_code" value="CAD"/>
      <input type="hidden" name="lc" value="CA"/>
      <input type="hidden" name="no_shipping" value="1"/>
      <input type="hidden" name="invoice" value="<?php echo sfConfig::get("ecommerce_paypal_invoice_prefix"); echo $resource->getId() ?>"/>


      <input type="hidden" name="no_note" value="1"/>
      <input type="hidden" name="notify_url" value="<?php echo $ipn_url ?>"/>
      <input type="hidden" name="return" value="<?php echo $pending_url ?>"/>
      <input type="hidden" name="cancel_return" value="<?php echo $cancel_url ?>"/>

      <input type="hidden" name="address_override" value="1"/>

      <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($resource['firstName']) ?>"/>
      <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($resource['lastName']) ?>"/>
      <input type="hidden" name="country" value="<?php echo $resource['country'] ?>"/>
      <input type="hidden" name="address1" value="<?php echo htmlspecialchars($resource['address1']) ?>"/>
      <input type="hidden" name="address2" value="<?php echo htmlspecialchars($resource['address2']) ?>"/>
      <input type="hidden" name="city" value="<?php echo htmlspecialchars($resource['city']) ?>"/>
      <input type="hidden" name="state" value="<?php echo htmlspecialchars($resource['province']) ?>"/>
      <input type="hidden" name="zip" value="<?php echo htmlspecialchars($resource['postalCode']) ?>"/>
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($resource['email']) ?>"/>

      <?php if (!empty($phone)) { ?>
        <input type="hidden" name="night_phone_a" value="<? echo substr($phone, 0, 3) ?>"/>
        <input type="hidden" name="night_phone_b" value="<? echo substr($phone, 3, 3) ?>"/>
        <input type="hidden" name="night_phone_c" value="<? echo substr($phone, 6, 4) ?>"/>
      <?php } ?>

      Click the PayPal button to continue:
      <input style="vertical-align: middle" type="image" src="http://www.paypal.com/en_US/i/btn/x-click-but01.gif" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!"/>
    </form>

      <script type="text/javascript" language="javascript"> 
        jQuery(document).ready(function() {
            jQuery('#wrapper').children().hide();
            jQuery('#wrapper').find('h1').show().text('You are being taken to PayPal.  Please wait...');
            jQuery('form[name="paypalform"]').submit();
        });
      </script>


<?php end_slot() ?>
