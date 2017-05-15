<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

 	/**
 	 * Session data in array so that redudent function call can be reduced
 	 * @var array
 	 */
	protected $session_data = [];

	public function __construct()
	{ 
		parent::__construct();
		// load session and common function libraries
		$this->load->library('common_functions');

		// verify session user ip and cookie redirect if any of this is invalid else continue
		$this->common_functions->verify_suci();

		// set session data to a private variable
		$this->session_data = $this->common_functions->session_data; 

		log_message("info","MY_Controller initiated");
	}

	// -------------------------------------------------------------------- 

	/**
	 * Render Teacher View
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
	protected function _render_all_teacher_view($page, $data = array(), $default_directory = true, $sub_directory = NULL) 
	{

		if($sub_directory == NULL)
		{
			$sub_directory = '';
		}
		else{
			$sub_directory =$sub_directory . "/";
		}
		// store data, page etc to an array to pass to view so that it can render that page with 
		// data on view
		$data_to_pass['data'] = $data;

		// if $default_directory is true render from view/public directory	
		$data_to_pass['page'] = ($default_directory === true) ?	'teacher/' . $sub_directory . $page:$page;
	 	
	 		// session data
		$data_to_pass['data']['session_data'] = $this->session_data; 
	
		 	// setup data from public model
		$this->load->model('public_model');
		
		// To display college name and other details 
		$data_to_pass['setup'] = $this->public_model->setup_data();

		// To display in navbar
		$data_to_pass['userpermission'] = $this->public_model->user_groups($this->session_data['uid']);
		// filter all data 
		// $data_to_pass = $this->security->xss_clean($data_to_pass); 
		// load default teacher bootstrap view	in application/view directory 
		$this->load->view('teacher/bootstrap', $data_to_pass);
	}
	public function history()
	{ 
		$data['table'] =  $this->common_functions->history();
		$this->_render_all_teacher_view('history', $data);
	}
}
