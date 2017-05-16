<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Hod Class
 * 
 * This class enables you to controll and manage Hod accounts. It includes functions 
 * to manage users, add or delete user permissions etc.
 * 
 * @category Controller
 * @author	 jayakrishnancn
 * @link	https://github.com/jayakrishnancn/College-Management-System
 */
 class Hod extends MY_Controller {
  
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	function __construct() 
	{
		parent::__construct(); 
 
		// verify if user has permission to this teacher controller (area).
		$this->common_functions->verify_permission('hod'); 
	}

	// --------------------------------------------------------------------
	
	/**
	 * Render Hod View
	 * 
	 * For loading a common teacher bootstrap view .This is to improve consistency and reduce 
	 * redudent code. Use	$this->_render_hod_view() insted of $this->load->view() 
	 * 
	 * @param	string	$page 		relative path of php view file to render
	 * @param	array	 $data 		To supply data to view 
	 * @param	boolean $default_directory 		If true relative path will start from view/teacher. 
	 *														 If false	relative path will start from view
	 *														 
	 * @return void
	 */
	private function _render_hod_view($page, $data = array(), $default_directory = 'teacher/hod') 
	{ 
		$this->_render_view($page, $data, $default_directory);
	}


	// --------------------------------------------------------------------
	
	/**
	 *	Teacher Index 
	 *
	 * Default method for Teacher controller
	 * 
	 * @return void
	 */
	public function index() 
	{
		$this->_render_hod_view('home');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add Subject
	 *
	 * add subject by hod
	 * @return void
	 */
	public function addsubject()
	{
		$this->_render_hod_view('home');
	}
	
}
