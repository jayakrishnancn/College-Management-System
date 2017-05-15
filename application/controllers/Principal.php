<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Principal Class
 * 
 * This class enables you to controll and manage principal login 
 * @category	Controller
 * @author	jayakrishnancn
 * @link		https://github.com/jayakrishnancn/College-Management-System
 */ 
class Principal extends CI_Controller { 

	/**
	 * Session data in array so that redudent function call can be reduced
	 * @var array
	 */	
	private $session_data = [];
	 
	function __construct() 
	{

		parent::__construct();
		
		// load session and common function libraries
		$this->load->library('common_functions');

		// verify session user ip and cookie redirect if any of this is invalid else continue
		$this->common_functions->verify_suci();
 
 		// verify if user has permission to this principal controller (area).
		$this->common_functions->verify_permission('principal');

		// set session data to a private variable
		$this->session_data = $this->common_functions->session_data;
 		    
	}

	// --------------------------------------------------------------------
	
	/**
	 * Render Principal View
	 * 
	 * For loading a common principal bootstrap view .This is to improve consistency and reduce 
	 * redudent code. Use  $this->render_principal_view() insted of $this->load->view() 
	 * 
	 * @param  string  $page 		relative path of php view file to render
	 * @param  array   $data 		To supply data to view 
	 * @param  boolean $default_directory 		If true relative path will start from 
	 *                 application/view/principal.otherwise from application/view/
	 *                             
	 * @return void
	 */
	private function render_principal_view($page, $data = array(), $default_directory = true) 
	{


		// store data, page etc to an array to pass to view so that it can render that page with 
		// data on view
		$data_to_pass['data'] = $data;

		// if $default_directory is true render from view/public directory  
		$data_to_pass['page'] = ($default_directory === true) ?  'principal/' . $page:$page;
   	
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
		// load default principal/ bootstrap view  in application/view directory 
		$this->load->view('principal/bootstrap', $data_to_pass);
	} 

	// --------------------------------------------------------------------
	
	/**
	 *  Principal Index 
	 *
	 * Default method for accounts controller 
	 * 
	 * @return void
	 */
	public function index() 
	{
		$this->render_principal_view('home');
	}
}
