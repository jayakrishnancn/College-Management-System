<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Teacher Class
 * 
 * This class enables you to controll and manage Teacher accounts. It includes functions 
 * to manage users, add or delete user permissions etc.
 * 
 * @category Controller
 * @author	 jayakrishnancn
 * @link	https://github.com/jayakrishnancn/College-Management-System
 */
 class Teacher extends MY_Controller {
  
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	function __construct() 
	{
		parent::__construct(); 
 
		// verify if user has permission to this teacher controller (area).
		$this->common_functions->verify_permission('teacher'); 
	}

	// --------------------------------------------------------------------
	
	/**
	 * Render Hod View
	 * 
	 * For loading a common teacher bootstrap view .This is to improve consistency and reduce 
	 * redudent code. Use	$this->_render_teacher_view() insted of $this->load->view() 
	 * 
	 * @param	string	$page 		relative path of php view file to render
	 * @param	array	 $data 		To supply data to view 
	 * @param	boolean $default_directory 		If true relative path will start from view/teacher. 
	 *														 If false	relative path will start from view
	 *														 
	 * @return void
	 */
	private function _render_teacher_view($page, $data = array(), $default_directory = true) 
	{
		$this->_render_all_teacher_view($page, $data, $default_directory, 'teaching');
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
		$this->_render_teacher_view('home');
	}
	
	// --------------------------------------------------------------------
 	
 	public function add_teaching_plan()
 	{
 		$this->_render_teacher_view('home');
 	}
}
