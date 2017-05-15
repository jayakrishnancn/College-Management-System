<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Staff_advisor Class
 * 
 * This class enables you to controll and manage Staff_advisor .it inherits teacher 
 * super class called my_controller
 * 
 * @category Controller
 * @author	 jayakrishnancn
 * @link	https://github.com/jayakrishnancn/College-Management-System
 */
 class Staff_advisor extends MY_Controller {
  
	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	function __construct() 
	{
		parent::__construct(); 
 
		// verify if user has permission to this teacher controller (area).
		$this->common_functions->verify_permission('staff_advisor'); 
	}

	// --------------------------------------------------------------------
	
	/**
	 * Render staff_advisor View
	 * 
	 * For loading a common staff_advisor bootstrap view .This is to improve consistency and reduce 
	 * redudent code. Use	$this->_render_staff_advisor_view() insted of $this->load->view() 
	 * 
	 * @param	string	$page 		relative path of php view file to render
	 * @param	array	 $data 		To supply data to view 
	 * @param	boolean $default_directory 		If true relative path will start from view/teacher. 
	 *														 If false	relative path will start from view
	 *														 
	 * @return void
	 */
	private function _render_staff_advisor_view($page, $data = array(), $default_directory = true) 
	{
		$this->_render_all_teacher_view($page, $data, $default_directory, 'staff_advisor');
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
		$this->_render_staff_advisor_view('home');
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add Teaching Plan
	 *
	 * add Teaching Plan by teaching staff
	 * 
	 * @return void
	 */
	public function add_teaching_plan()
	{
		$this->_render_staff_advisor_view('home');
	}
	
}
