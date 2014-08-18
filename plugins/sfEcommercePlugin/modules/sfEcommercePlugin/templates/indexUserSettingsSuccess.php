<h1><?php echo __('User %1%', array('%1%' => render_title($resource))) ?></h1>

<?php echo get_component('user', 'aclMenu') ?>

<section id="content">

  <section id="userDetails">

    <?php echo link_to_if(QubitAcl::check($resource, 'update'), '<h2>'.__('Ecommerce settings').'</h2>', array($resource, 'module' => 'user', 'action' => 'edit')) ?>

    <?php echo render_show(__('User name'), $resource->username.($sf_user->user === $resource ? ' ('.__('you').')' : '')) ?>

    <?php echo render_show(__('Repository'), $resource->userEcommerceSettingss[0]->repository) ?>


  </section>

</section>

<section class="actions">

  <ul>

    <?php if (QubitAcl::check($resource, 'update')): ?>
      <li><?php echo link_to (__('Edit'), array($resource, 'module' => 'sfEcommercePlugin', 'action' => str_replace('index', 'edit', $sf_context->getActionName())), array('class' => 'c-btn')) ?></li>
    <?php endif; ?>
  </ul>

</section>
