<div class="container-fluid">
	<div class="row">
<div class="col-sm-6 col-sm-offset-3">	
	<?=form_open($action,["class"=>"contentbox"])?>
		<div class="form-group">
			<h2 class=" text-left">Add User</h2>
		</div>
		<div class="form-group">
			<label for="exampleInputEmail1">Username</label>
			<select name="uid" class="form-control">
			<?php
				foreach ($username as $key=>$value) {
			 echo '	<option value="' . $value['uid'] . '">' . $value['email'] . '</option>';
			 
				}
			 ?>
			</select> 
		</div>  
		<div class="form-group">
			<label for="exampleInputPassword1">Permission</label>
			<select name="permissionid" class="form-control">
			<?php
				foreach ($permission as $key=>$value) {
			 echo '	<option value="' . $value['permissionid'] . '">' . $value['groupname'] . '</option>';
			 
				}
			 ?>
			</select>

		</div>
		<button type="submit" class="btn btn-success">Submit</button>
	</form>

</div>
</div>

</div>