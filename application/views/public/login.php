<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="login">
	<h2>Login</h2>
	<?=form_open($action,["class"=>"login-content"])?>
		<div class="form-group">
		</div>
		<div class="form-group">
			<label for="exampleInputEmail1">Username</label>
			<input type="text" class="form-control" id="exampleInputEmail1" name="username" placeholder="Username" autofocus>
		</div>
		<div class="form-group">
			<label for="exampleInputPassword1">Password</label>
			<input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password">
		</div>
		<button type="submit" class="btn btn-success">Submit</button>
	</form>
	<div class="login-sub">
		New to usermanager ? <a href="<?=base_url()?>accounts/signup">Create an account </a>
	</div>
	<div class="links">
		<a href="#">Terms</a>
		<a href="#">contact</a>
	</div>
</div>