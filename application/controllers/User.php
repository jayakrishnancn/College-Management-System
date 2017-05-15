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

		redirect($this->common_functions->default_user_group().$msg);
	}
}
