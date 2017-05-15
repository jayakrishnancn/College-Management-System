<h3>History 
      <input type="text" class="form-control  " style="width: auto;float: right;" id="searchtable" placeholder="Enter keyword" autofocus>
</h3>
<hr>
<?php if (count($table) > 0): ?>
<table class="table table-bordered tabletosearch">
  <thead>
    <tr>
      <th> <?php echo implode('</th><th>', array_keys(current($table))); ?></th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($table as $row): array_map('htmlentities', $row); ?>
    <tr>
      <td> <?php echo implode('</td><td>', $row); ?> </td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>