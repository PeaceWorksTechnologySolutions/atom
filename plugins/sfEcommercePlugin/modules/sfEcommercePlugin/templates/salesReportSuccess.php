<?php decorate_with('layout_1col') ?>
<?php use_stylesheet('/plugins/sfEcommercePlugin/css/ecommerce.css'); ?>
<?php use_stylesheet('/plugins/sfEcommercePlugin/css/print.css', '', array('media' => 'print')); ?>
<?php use_javascript('/vendor/yui/calendar/calendar-min'); ?>
<?php use_javascript('/js/date.js'); ?>
<?php use_helper('Date') ?>

<?php slot('title') ?>
  <h1>
    Sales Report 
    <?php if (!empty($start_date)) echo " from " . $start_date ?>
    <?php if (!empty($end_date)) echo " to " . $end_date ?>
  </h1>
<?php end_slot() ?>

<?php slot('before-content') ?>


  <section class="header-options">
    <form method="GET" action="">
    <div class="row">
      <div class="span6 form-item form-item-date">
        <label>Start date</label><input id="start_date" class="date-widget" type="text" icon="/images/calendar.png" name="start_date" value="<?php echo $start_date ?>">
      </div>
      <div class="span6 form-item form-item-date">
        <label>End date</label><input id="end_date" class="date-widget" type="text" icon="/images/calendar.png" name="end_date" value="<?php echo $end_date ?>">
      </div>
      <div class="span6">
        <label>&nbsp;</label>
        <input type="submit" value="Update">
      </div>
    </div>
    </form>
  </section>

<?php end_slot() ?>

<?php slot('content') ?>
  <table class="sales_stats">
    <tr><td class="label">Total orders</td><td></td><td class="amount"><?php echo $stats['total_orders'] ?></td></tr>
    <tr><td class="label">Photos sold</td><td></td><td class="amount"><?php echo $stats['photos_sold'] ?></td></tr>
    <tr><td class="label">Gross sales</td><td></td><td class="amount"><?php echo "$" . $stats['gross_sales'] ?></td></tr>
  <!-- FIXME: photo sales -->
  <!-- FIXME: tax1 amount -->
  <!-- FIXME: tax2 amount -->
    <tr><td></td><td class="label">Fees</td><td class="amount"><?php echo "$" . $stats['fees'] ?></td></tr>
    <tr><td class="label">Net Sales</td><td></td><td class="amount"><?php echo "$" . $stats['net_total'] ?></td></tr>
  </table>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>
          <?php echo __('ID') ?>
        </th>
        <th>
          <?php echo __('Sale ID') ?>
        </th>
        <th>
          <?php echo __('Type') ?>
        </th>
        <th>
          <?php echo __('Amount') ?>
        </th>
        <th>
          <?php echo __('Customer') ?>
        </th>
        <th>
          <?php echo __('Created') ?>
        </th>
      </tr>
    </thead><tbody>
      <?php foreach ($pager->getResults() as $item): ?>
        <tr>
          <td>
              <?php echo $item->getId() ?>
          </td>
          <td>
            <?php echo link_to($item->sale->getId(), array('module' => 'sfEcommercePlugin', 'action' => 'viewOrder', 'id' => $item->sale->getId())) ?>
          </td>
          <td>
              <?php echo $item->type ?>
          </td>
          <td>
              <?php echo $item->amount ?>
          </td>
          <td>
            <?php 
                  $sale = $item->sale;
                  echo link_to($sale->firstName . ' ' . $sale->lastName, array('module' => 'sfEcommercePlugin', 'action' => 'viewOrder', 'id' => $sale->getId())) ?>
          </td>
          <td>
            <?php echo format_date($item->createdAt, 'f') ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php end_slot() ?>

<?php slot('after-content') ?>

  <?php echo get_partial('default/pager', array('pager' => $pager)) ?>

<?php end_slot() ?>
