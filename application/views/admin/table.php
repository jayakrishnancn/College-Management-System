<h3>Users 
      <input type="text" class="form-control  " style="width: auto;float: right;" id="searchtable" placeholder="Enter keyword" autofocus>
</h3>

<hr>

<table class="table table-bordered tabletosearch">
	<thead>
		<tr>
			<th>user id</th>
			<th>email</th>
			<th>access</th>
			<th class="text-center noindex">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($table as $key => $value) {
		echo '
		<tr>
				<td>' . $value['uid'] . '</td>
				<td>' . $value['email'] . '</td>
				<td>' . $value['access'] . '</td>
				<td  class="noindex"> 
					<a class="btn btn-default btn-sm confirmation" href="'  .base_url('admin/resetpassword')  .  '?email=' . $value['email'] . '">Reset password</a>
					<a class="btn btn-default btn-sm " href="'  .base_url('admin/edituser')  .  '?email=' . $value['email'] . '">Edit </a>
					<a class="btn btn-default btn-sm confirmation"  href="'  .base_url('admin/deleteuser')  .  '?emailid=' . $value['email'] . '">Delete </a>
				</td>
		</tr>
			';
		}?>
	</tbody>
</table>
