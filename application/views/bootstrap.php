<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?=isset($data['title'])?$data['title']:" "?></title>
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="<?=base_url()?>assets/vendor/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?=base_url()?>assets/css/style.css">
	</head>
	<body>
		<?php
			$this->view('nav',$data);
		?>
		<div class="container">
			<div class="row">
				<div class="col-sm-3">
					<?php
						$this->view('sidenav',$data);
					?>
				</div>
				<div class="col-sm-9 main-contents">
					<?php 
						if(isset($page))
						{
							if(is_array($page))
							{
			 					foreach ($page as $key => $value) 
			 					{
									$this->view($value,$data);
			 					}
							}
							else
							{
									$this->view($page,$data);
							}
						}
					?>
				</div>
			</div>
		</div>
		<script src="<?=base_url()?>assets/vendor/jquery.min.js"></script>
		<script src="<?=base_url()?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="<?=base_url()?>assets/js/custom.js"></script>
	</body>
</html>