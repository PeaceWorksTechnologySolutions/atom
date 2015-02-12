<?php use_stylesheet('/plugins/sfEcommercePlugin/css/ecommerce.css'); ?>

<h1><?php echo __('User %1%', array('%1%' => render_title($resource))) ?></h1>

<?php echo get_component('user', 'aclMenu') ?>

<section id="content">

  <section id="userDetails">

    <?php echo link_to_if(QubitAcl::check($resource, 'update'), '<h2>'.__('Ecommerce settings').'</h2>', array($resource, 'module' => 'user', 'action' => 'edit')) ?>

    <?php echo render_show(__('User name'), $resource->username.($sf_user->user === $resource ? ' ('.__('you').')' : '')) ?>

    <?php $repo = $resource->userEcommerceSettingss[0]->repository; 
          echo render_show(__('Repository'), $repo ? $repo : "None (user has access to ecommerce management)") ?>

    <?php echo render_show(__('Vacation Enabled'), $resource->userEcommerceSettingss[0]->vacationEnabled ? "Yes" : "No") ?>

    <div class="field vacation_message">
      <h3>Vacation Message</h3>
      <div class="message"><?php echo '<pre>' . $resource->userEcommerceSettingss[0]->vacationMessage . '</pre>' ?>
      </div>
    </div>


  </section>

</section>

<section class="actions">

  <ul>

    <?php if (QubitAcl::check($resource, 'update') || $is_own_record): ?>
      <li><?php echo link_to (__('Edit'), array($resource, 'module' => 'sfEcommercePlugin', 'action' => str_replace('index', 'edit', $sf_context->getActionName())), array('class' => 'c-btn')) ?></li>
    <?php endif; ?>
  </ul>

</section>
