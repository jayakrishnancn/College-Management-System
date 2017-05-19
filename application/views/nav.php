<?php
defined('BASEPATH') OR exit('No direct script access allowed');

  if(isset($msg)){
    echo '<div id="snackbar" class="show">'  . $msg .  ' </div>';
  }

?><nav class="navbar navbar-inverse navbar-static-top"  >
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><?=$setup['collegeshortname']?></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
      <li><a href="<?=base_url('admin/manageusers')?>">Manage Users</a></li> 
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Panels<span class="caret"></span></a>
          <ul class="dropdown-menu left">
             <?php 
           foreach ($userpermission as $key => $value) {
            ?>
            <li><a href="<?=base_url($value)?>" style="text-transform: capitalize;"><?=$value?> Panel</a></li>
            <?php 
            }
            ?>
          </ul>
        </li> 
       
      </ul>
      
      <ul class="nav navbar-nav navbar-right">
        <li class="active"><a href="#"><?=$session_data['email']?></a></li> 
        <li><a href="<?=base_url()?>accounts/logout">Logout</a></li> 
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>