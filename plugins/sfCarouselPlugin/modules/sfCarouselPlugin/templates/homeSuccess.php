<?php decorate_with('layout_2col') ?>

<?php slot('before-title') ?>
  <div id="carousel_container">
      <div id="carousel_left_arrow">
        <img style="height: 100px" src="/plugins/sfCarouselPlugin/images/left_arrow.png">
      </div>
      <div id="carousel">
      <?php foreach ($results as $hit): ?>
        <?php $doc = $hit->getData() ?>
        <div class="carousel_item">
              <?php if (NULL !=  $doc['digitalObject']['thumbnailPath']): ?>
                <?php echo link_to(image_tag($doc['digitalObject']['thumbnailPath']), array('module' => 'informationobject', 'slug' => $doc['slug'])) ?>
              <?php else: ?>
                <?php echo link_to(image_tag('question-mark'), array('module' => 'informationobject', 'slug' => $doc['slug'])) ?>
              <?php endif; ?>
              <p class="description"><?php echo get_search_i18n($doc, 'title') ?></p>
        </div>
      <?php endforeach; ?>
      </div>
      <div id="carousel_right_arrow">
        <img style="height: 100px" src="/plugins/sfCarouselPlugin/images/right_arrow.png">
      </div>
      <div id="carousel_slides">
      </div>
    </div>
<?php end_slot() ?>

<?php slot('title') ?>
  <h1><?php echo render_title($resource->getTitle(array('cultureFallback' => true))) ?></h1>
<?php end_slot() ?>

<?php slot('sidebar') ?>

  <section>
    <h3><?php echo __('Browse by') ?></h3>
    <ul>
      <?php $browseMenu = QubitMenu::getById(QubitMenu::BROWSE_ID) ?>
      <?php if ($browseMenu->hasChildren()): ?>
        <?php foreach ($browseMenu->getChildren() as $item): ?>
          <li>
            <a href="<?php echo url_for($item->getPath(array('getUrl' => true, 'resolveAlias' => true))) ?>">
              <?php echo $item->getLabel(array('cultureFallback' => true)) ?>
            </a>
          </li>
        <?php endforeach; ?>
      <?php endif; ?>
    </ul>
  </section>

  <?php echo get_component('default', 'popular', array('limit' => 10, 'sf_cache_key' => $sf_user->getCulture())) ?>

<?php end_slot() ?>

<div class="page">
  <?php echo render_value($resource->getContent(array('cultureFallback' => true))) ?>
</div>

<?php if (SecurityCheck::hasPermission($sf_user, array('module' => 'staticpage', 'action' => 'update'))): ?>
  <?php slot('after-content') ?>
    <section class="actions">
      <ul>
        <li><?php echo link_to(__('Edit'), array($resource, 'module' => 'staticpage', 'action' => 'edit'), array('title' => __('Edit this page'), 'class' => 'c-btn')) ?></li>
      </ul>
    </section>
  <?php end_slot() ?>
<?php endif; ?>
