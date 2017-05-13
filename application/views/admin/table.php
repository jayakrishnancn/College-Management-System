<h3>Users</h3>
<hr>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>user id</th>
			<th>email</th>
			<th>access</th>
			<th class="text-center">Action</th>
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
				<td> 
					<a class="btn btn-default btn-sm confirmation" href="'  .base_url('admin/resetpassword')  .  '?email=' . $value['email'] . '">Reset password</a>
					<a class="btn btn-default btn-sm " href="'  .base_url('admin/edituser')  .  '?email=' . $value['email'] . '">Edit </a>
					<a class="btn btn-default btn-sm confirmation"  href="'  .base_url('admin/deleteuser')  .  '?emailid=' . $value['email'] . '">Delete </a>
				</td>
		</tr>
			';
		}?>
	</tbody>
</table>