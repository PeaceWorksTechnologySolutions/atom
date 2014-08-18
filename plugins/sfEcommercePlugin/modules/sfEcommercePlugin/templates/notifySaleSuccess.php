<?php decorate_with('layout_1col.php') ?>

<?php slot('title') ?>
  <h1>Notify</h1>
<?php end_slot() ?>

<?php slot('content') ?>

    <? foreach ($repos as $repoid => $repo) { ?>
      <? echo $repo['repository']->identifier; ?><br>
      <? foreach ($repo['resources'] as $resource) { ?>
        <? echo $resource->title ?><br>
      <? } ?>
    <? } ?>

<?php end_slot() ?>
