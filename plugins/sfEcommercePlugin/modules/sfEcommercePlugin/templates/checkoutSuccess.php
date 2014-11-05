<?php decorate_with('layout_1col.php') ?>
<?php use_stylesheet('/plugins/sfEcommercePlugin/css/checkout.css'); ?>
<?php use_javascript('/plugins/sfEcommercePlugin/js/subdivisions.js'); ?>

<?php slot('title') ?>
  <h1>Check out</h1>
<?php end_slot() ?>

<?php slot('content') ?>

  <?php echo $form->renderGlobalErrors() ?>

  <?php echo $form->renderFormTag(url_for(array('module' => 'sfEcommercePlugin', 'action' => 'checkout'))) ?>

    <?php echo $form->renderHiddenFields() ?>

    <section id="content">

      <fieldset id="checkoutInformation">

        <legend><?php echo __('Checkout Information') ?></legend>

        <?php echo render_field($form->firstName, $resource) ?>
        <?php echo render_field($form->lastName, $resource) ?>
        <?php echo render_field($form->country, $resource) ?>
        <?php echo render_field($form->address1, $resource) ?>
        <?php echo render_field($form->address2, $resource) ?>
        <?php echo render_field($form->city, $resource) ?>

        <?php echo $form->province
          ->label(__('Province/Region'))
          ->renderRow() ?>

        <?php echo render_field($form->postalCode, $resource) ?>
        <?php echo render_field($form->email, $resource) ?>

        <?php echo $form->email2
          ->label(__('Confirm Email'))
          ->renderRow() ?>

        <?php echo render_field($form->phone, $resource) ?>


        <div class="form-item">
            <?php echo $form->non_commercial->renderError() ?>
            <?php echo render_field($form->non_commercial, $resource, array('onlyInput' => true)) ?>
             I will only use these photos for non-commercial purposes.
        </div>

      <div class="form-item payment-info">Payment will take place via PayPal.  You do <b>not</b> have to have a PayPal account -- simply choose "Don't have a PayPal account".</div>
      <div class="form-item">
        <?php echo image_tag('/plugins/sfEcommercePlugin/images/mastercard.gif') ?>
        <?php echo image_tag('/plugins/sfEcommercePlugin/images/visa.gif') ?>
        <?php echo image_tag('/plugins/sfEcommercePlugin/images/paypal.gif') ?>
      </div>

      </fieldset>

    </section>

    <section class="actions">
      <ul>
        <?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
          <li><div class="ecommerce-button ecommerce-button-non-default"><?php echo link_to(__('Cancel'), '@homepage', array('title' => __('Cancel'))) ?></div></li>
          <li><div class="ecommerce-button"><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></div></li>
        <?php else: ?>
          <li><div class="ecommerce-button ecommerce-button-non-default"><?php echo link_to(__('Cancel'), '@homepage', array('title' => __('Cancel'))) ?></div></li>
          <li><div class="ecommerce-button"><input type="submit" value="<?php echo __('Submit order') ?>"/></div></li>
        <?php endif; ?>
      </ul>
    </section>

  </form>

<?php end_slot() ?>
