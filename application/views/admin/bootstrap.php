<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?=isset($data['title'])?$data['title']:"Admin"?></title>
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="<?=base_url()?>assets/vendor/bootstrap.min.css">
		<link rel="stylesheet" href="<?=base_url()?>assets/css/style.css">
	</head>
	<body>
		<?php 
			$this->view('admin/nav',$data);
		?>
		<div class="container">
			<div class="row">
				<div class="col-sm-3">
					<?php 
						$this->view('admin/sidenav',$data);
					?>	
				</div>
				<div class="col-sm-9 main-contents">
					<?php 
						$this->view($page,$data);
					?>
				</div>
			</div>
		</div>

	<script src="<?=base_url()?>assets/vendor/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<?php if(isset($_GET['msg'])){ ?>
	<script>
		var snackbar = document.getElementById("snackbar");
		setTimeout(function(){ snackbar.className = snackbar.className.replace("show", ""); }, 3000);
	</script>
		<?php } ?>
	<script type="text/javascript">
		var elems = document.getElementsByClassName('confirmation');
		var confirmIt = function (e) {
			if (!confirm('Are you sure?')) e.preventDefault();
		};
		for (var i = 0, l = elems.length; i < l; i++) {
		elems[i].addEventListener('click', confirmIt, false);
		}
	</script>
	</body>
</html>