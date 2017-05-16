<div class="main-components">
<?php 
if(isset($heading)  && $heading!=NULL)
		{

		echo  '
			<h3 class="text-left">' . $heading . '</h3>';

		if(isset($desc) && $desc!=NULL)
		{ 
			echo  '<p>' . $desc . '</p>';
		}
 
		}
		echo '<hr/>';


		$labelflag=false;
		$attributes['class']='width80per';
	 

	 echo form_open($action,$attributes) ;
		
		
		if(isset($fields) && is_array($fields)){
		foreach ($fields as $key => $value) {

			
			if($value['type'] == 'label')
			{
				
				echo '<div class="form-group">';
				echo "<label " .  ( (isset($value['for']) && $value['for']!=NULL )?$value['for']: ' ') . ">" . $value['value'] ."</label>"; 
	 			$labelflag=true; 
			}
			else{
				if($labelflag!==true)
				{
					echo '<div class="form-group">';
				}
				switch($value['type'])
				{  
					case 'hidden' :
						echo " <input  type='" . $value['type'] ."' name='" .  $value['name'] . "' id='" .  $value['id'] . "'  value='" .  $value['value'] . "' " .  ( (isset($value['extra']) && strlen($value['extra'])>0 ) ?$value['extra']:"") . "/>";
						break;
					case 'text' :
					case 'password' :
						echo " <input class='form-control' type='" . $value['type'] ."' name='" .  $value['name'] . "' id='" .  $value['id'] . "' placeholder='" .  $value['placeholder'] . "' value='" .  $value['value'] . "' "  .  (isset($value['autofocus']) && ($value['autofocus']==true)?'autofocus':' ')  .  ( (isset($value['extra']) && strlen($value['extra'])>0 ) ?$value['extra']:"") . "/>";break;
					case 'select' : 
					case 'dropdown' : 
							echo "<select class='form-control'  name='" .  $value['name'] . "' "  .  (($value['autofocus']==true)?'autofocus':'')  .  $value['extra'] . ">";
								foreach ($value['option'] as $optionkey => $optionvalue) {
									echo "<option value='" . $optionvalue['value'] . "' " . ( ($optionvalue['selected']==true)?"selected":" ") . "> " . $optionvalue['displayvalue'] . " </option>" ;
								}
							echo "</select>";
					break; 
				}
				if($labelflag==true)
				{
					echo '</div>';
					$labelflag=false;
				}

			}
		}
	}// isset $fields


		?>
		<button class="btn btn-default btn-long <?php echo isset($button['class'])?$button['class']:"";?>"  ><?=(isset($button['value'])?$button['value']:"Submit")?></button>
	</form> 
</div>