<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Class
 * 
 * This class is to redirect the user to corresponding controller and check user verification.
 * Logic to redirect will be changed in future. 
 * 
 * @category	Controller
 * @author	  jayakrishnancn
 * @link		https://github.com/jayakrishnancn/College-Management-System
 */ 
class User extends CI_Controller { 

	/**
	 * Class constructor
	 *
	 * @return  void
	 */
	function __construct() 
	{ 
		parent::__construct();
		
		// load session and common function libraries
		$this->load->library('common_functions');

		// verify session user ip and cookie redirect if any of this is invalid else continue
		$this->common_functions->verify_suci(); 
	}

	// --------------------------------------------------------------------

	/**
	 * User/index
	 *
	 * default method for User controller
	 * 
	 * @return void
	 */
	public function index() 
	{
		$msg="";
		if($msg = $this->input->get('msg'))
		{
			$msg = "?msg=".$msg;	
		}
		
		$groupname = $this->common_functions->default_user_group();
		
		/*$default_controller = 'accounts/logout?msg= user has no permission to continue';

		switch($groupname = $this->common_functions->default_user_group())
		{
			case 'admin' : $default_controller = $groupname; break;
			case 'principal' : $default_controller = $groupname; break;
			case 'hod' :
			case 'teacher' :
			case 'staff_advisor' :
						$default_controller = 'teacher'; break;
			case 'student' : $default_controller = $groupname; break;
			case 'parent' : $default_controller = $groupname; break;
		}*/

		redirect($groupname.$msg);
	}
}
