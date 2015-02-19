<?php decorate_with('layout_1col.php') ?>
<?php use_stylesheet('/plugins/sfEcommercePlugin/css/checkout.css'); ?>

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
        <?php echo render_field($form->address1, $resource) ?>
        <?php echo render_field($form->address2, $resource) ?>
        <?php echo render_field($form->city, $resource) ?>

        <?php echo $form->province
          ->label(__('Province/Region'))
          ->renderRow() ?>

        <?php echo render_field($form->postalCode, $resource) ?>
        <?php echo render_field($form->country, $resource) ?>
        <?php echo render_field($form->email, $resource) ?>
        <?php echo render_field($form->phone, $resource) ?>

        <div class="form-item">
            <label for="non_commercial"></label>
            <input name="non_commercial" type="checkbox" value="non_commercial"> I will only use these photos for non-commercial purposes.
        </div>

      </fieldset>

    </section>

    <section class="actions">
      <ul>
        <?php if (isset($sf_request->getAttribute('sf_route')->resource)): ?>
          <li><?php echo link_to(__('Cancel'), '@homepage', array('title' => __('Cancel'), 'class' => 'c-btn')) ?></li>
          <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
        <?php else: ?>
          <li><?php echo link_to(__('Cancel'), '@homepage', array('title' => __('Cancel'), 'class' => 'c-btn')) ?></li>
          <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Submit order') ?>"/></li>
        <?php endif; ?>
      </ul>
    </section>

  </form>

<?php end_slot() ?>
