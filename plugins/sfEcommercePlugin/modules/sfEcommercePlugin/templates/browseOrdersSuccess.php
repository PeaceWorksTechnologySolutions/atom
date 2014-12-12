<?php decorate_with('layout_1col') ?>
<?php use_stylesheet('/plugins/sfEcommercePlugin/css/ecommerce.css'); ?>
<?php use_helper('Date') ?>

<?php slot('title') ?>
  <h1 class="multiline">
    <?php echo __('Showing %1% results', array('%1%' => $pager->getNbResults())) ?>
    <span class="sub"><?php echo __('Orders') ?></span>
  </h1>
<?php end_slot() ?>

<?php slot('before-content') ?>


  <section class="header-options">
    <form method="GET" action="">
    <div class="row">
      <div class="span6">
        <?php echo get_component('search', 'inlineSearch', array(
          'label' => __('Search by order or name'))) ?>
      </div>
      <div class="span6 filter"><select name="filter" onchange="form.submit()">
        <?php foreach ($filter_options as $key) { ?>
          <?php if ($selected_filter == $key) { ?>
            <option value="<?php echo $key ?>" selected="selected"><?php echo $key ?></option>
          <?php } else { ?>
            <option value="<?php echo $key ?>"><?php echo $key ?></option>
          <?php } ?>
        <?php } ?>
      </select></div>
      <div class="span6">
        <?php echo get_partial('default/sortPicker',
          array(
            'options' => array(
              'lastUpdated' => __('Most recent'),
              ))) 
            ?>
      </div>
    </div>
    </form>
  </section>

<?php end_slot() ?>

<?php slot('content') ?>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>
          <?php echo __('Order') ?>
        </th>
        <th>
          <?php echo __('Name') ?>
        </th>
        <th>
          <?php echo __('Status') ?>
        </th>
        <th>
          <?php echo __('Reference Codes') ?>
        </th>
        <th>
          <?php echo __('Titles') ?>
        </th>
        <th>
          <?php echo __('Created') ?>
        </th>
        <th>
          <?php echo __('Updated') ?>
        </th>
      </tr>
    </thead><tbody>
      <?php 
      
        foreach ($pager->getResults() as $item): 
            $sale_repos = sfEcommercePlugin::sale_resources_by_repository($item);
            $resources = $sale_repos[$user_repo]['resources'];

      ?>
        <tr>
          <td>
            <?php echo link_to($item->getId(), array('module' => 'sfEcommercePlugin', 'action' => 'viewOrder', 'id' => $item->getId())) ?>
          </td>
          <td>
            <?php echo link_to($item->firstName . ' ' . $item->lastName, array('module' => 'sfEcommercePlugin', 'action' => 'viewOrder', 'id' => $item->getId())) ?>
          </td>
          <td>
              <?php echo $item->processingStatus ?>
          </td>
          <td>
              <?php foreach ($resources as $index => $item) { 
                  echo $item->referenceCode . "<br>";
              }
              ?>
          </td>
          <td>
              <?php foreach ($resources as $index => $item) { 
                  echo render_title($item->title) . "<br>";
              }
              ?>
          </td>
          <td>
            <?php echo format_date($item->createdAt, 'f') ?>
          </td>
          <td>
            <?php echo format_date($item->updatedAt, 'f') ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php end_slot() ?>

<?php slot('after-content') ?>

  <?php echo get_partial('default/pager', array('pager' => $pager)) ?>

<?php end_slot() ?>
