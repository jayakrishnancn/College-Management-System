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

		$this->load->model('teacher_model');
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
		if($input = $this->input->post())
		{

			$this->load->library('form_validation');

			$this->form_validation->set_rules('subject_name', 'Subject Name', 'trim|required|min_length[3]');
			$this->form_validation->set_rules('course_name', 'Course Name', 'trim|required|min_length[3]');
			// run the form validation
			if ($this->form_validation->run() == FALSE) 
			{
				redirect($this->current_url.'?msg=Check all fields'); 
				return;
			}

			if(!($my_dept = $this->teacher_model->get_dept_by_uid($this->session_data['uid'])))
			{
				redirect($this->current_url."?msg=subject not added .No permission to do this action.");
				return false;
			}

			if($this->teacher_model->add_subject($input['subject_name'], $input['course_name'], $my_dept['id']))
			{
				redirect($this->current_url."?msg=subject added.");
				return;
			}
			redirect($this->current_url."?msg=subject not added. Try again.");
			return;
		}
		$this->load->library('form_builder');
		$this->form_builder->start_form(['action' =>$this->current_url,'heading' =>'Add Subject']);
		$this->form_builder->addlabel("Add Subject");
		$this->form_builder->addinput("subject_name");
		
		$this->form_builder->addlabel("Course");
		$this->form_builder->startdropdown("course_name");
		foreach ($this->public_model->course() as $key => $value) {
			$this->form_builder->dropdownoption($value['course_name']);
		}
		$this->form_builder->enddropdown();
		
		$this->form_builder->setbutton("Add Subject");
		$this->_render_hod_view('public/form_builder',false,false);
	}

	
	// --------------------------------------------------------------------

	/**
	 * View Subject
	 *
	 * View subject by hod
	 * @return void
	 */
	public function view_subject()
	{  
		$data['table'] = $this->teacher_model->get_subject();	 
		$data['table_title'] = 'Subjects';
		$this->_render_hod_view('public/table',$data,false);
	}
	
}
