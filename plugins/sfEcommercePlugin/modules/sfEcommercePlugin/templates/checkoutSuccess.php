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

            <?php echo $form->terms->renderError() ?>
            <div class="ecommerce-terms">
                <p>I agree to use this copy only for non-commercial purposes as outlined <a href="/order" target="_blank">here</a>. I am aware that any other use may require permission from the copyright owner, and that it is my responsibility to obtain this permission.</p>

               <p>If I want to use this copy for any other use, I will contact the archives that supplied the photograph for more information. </p>
             </div>

            <?php echo render_field($form->terms, $resource, array('onlyInput' => true)) ?>
              Yes, I accept these terms

        </div>

        <div class="form-item may_contact">
            <?php echo $form->mayContact->renderError() ?>
            <?php echo render_field($form->mayContact, $resource, array('onlyInput' => true)) ?>
             I would like to receive updates about this website.
        </div>

      <div class="form-item order-info">After placing your order, it will be processed by the Archive(s) involved.  Then you will receive an email allowing you to download the photos.</div>

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
