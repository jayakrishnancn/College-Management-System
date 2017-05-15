<?php 
	if(in_array('hod',$userpermission))
	{
?> 
	<ul class="list-group side-nav" >
		<li class="list-group-item heading">Hod </li>
		<a class="list-group-item" href="<?=base_url()?>hod/addsubject">Add Subject</a>  
	</ul>
<?php 
	}
?>
<?php 
	if(in_array('staff_advisor',$userpermission))
	{
?> 
	<ul class="list-group side-nav" >
		<li class="list-group-item heading">Staff Advisor</li>
		<a class="list-group-item" href="<?=base_url()?>staff_advisor/add_teaching_plan">Add Students</a>  
	</ul>
<?php 
	}
?>

<?php 
	if(in_array('teacher',$userpermission))
	{
?> 
	<ul class="list-group side-nav" >
		<li class="list-group-item heading">Teacher </li>
		<a class="list-group-item" href="<?=base_url()?>teacher/add_teaching_plan">Add Teaching Plan</a>  
	</ul>
<?php 
	}
?>
<ul class="list-group side-nav" >
	<li class="list-group-item heading">Settings </li>
	<a class="list-group-item" href="<?=base_url($this->router->fetch_class())?>/history">Login History</a>  
</ul> 