<?php decorate_with('layout_1col.php') ?>
<?php use_stylesheet('/plugins/sfEcommercePlugin/css/ecommerce.css'); ?>

<?php slot('title') ?>
  <h1>Ecommerce Settings for <? echo $user ?></h1>
<?php end_slot() ?>

<?php slot('content') ?>

  <?php echo $form->renderGlobalErrors() ?>

  <?php echo $form->renderFormTag(url_for(array('module' => 'sfEcommercePlugin', 'action' => 'editUserSettings', 'slug' => $user->slug))) ?>

    <?php echo $form->renderHiddenFields() ?>

    <section id="content">

      <fieldset id="userEcommerceSettings">

        <?php echo render_field($form->repository, $resource) ?>

      </fieldset>

    </section>

    <section class="actions">
      <ul>
        <li><?php echo link_to(__('Cancel'), array($resource, 'module' => 'sfEcommercePlugin', 'action' => 'indexUserSettings'), array('class' => 'c-btn')) ?></li>
        <li><input class="c-btn c-btn-submit" type="submit" value="<?php echo __('Save') ?>"/></li>
      </ul>
    </section>

  </form>

<?php end_slot() ?>
