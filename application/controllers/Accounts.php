<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Accounts Class
 * 
 * This class enables you to controll and manage user accounts. It includes functions like 
 * login, signup, view login history, logout, change password etc. This module is independent
 * access level of users (admin,student etc.). 
 * 
 * @category	Controller
 * @author	  jayakrishnancn
 * @link		https://github.com/jayakrishnancn/College-Management-System
 */ 
class Accounts extends CI_Controller {

	/**
	 * To ser minimum length of username and password
	 * @var integer
	 */
	private $_min_username_length=4;	
	private $_min_password_length=6;	

	/**
	 * Class constructor
	 *
	 * @return  void
	 */
	function __construct() 
	{
		parent::__construct();
		
		// public model is a model used for general purpose
		// where as accounts_model is used specificaly for account related things 
 		$this->load->model('accounts_model');
 		log_message('info', 'Accounts class loaded');
	}

	// --------------------------------------------------------------------

	/**
	 * Render Public View
	 * 
	 * For loading a common public bootstrap view .This is to improve consistency and reduce 
	 * redudent code. Use  $this->_render_public_view() insted of $this->load->view() 
	 * 
	 * @param  string  $page 		relative path of php view file to render
	 * @param  array   $data 		To supply data to view 
	 * @param  boolean $default_directory 	If true relative path will start from view/public. 
	 *                             If false  relative path will start from view
	 *                             
	 * @return void
	 */
	private function _render_public_view($page, $data = array(), $default_directory = true) 
	{
		// store data, page etc to an array to pass to view so that it can render that page with 
		// data on view
		$data_to_pass['data'] = $data;

		// if $default_directory is true render from view/public directory  
		$data_to_pass['page'] = ($default_directory === true) ?  'public/' . $page:$page;
 
		$this->load->view('public/bootstrap', $data_to_pass);
	}

	// --------------------------------------------------------------------
	
	/**
	 *  Accounts / Index 
	 *
	 * Default method for accounts controller
	 * calls login by default (might change in future)
	 * 
	 * @return void
	 */
	public function index() 
	{
		$this->login();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Login page
	 *
	 * To render login login page and manage user login. Uses accounts_model for 
	 * verifying users. Uses session for checking if user already logged in. If user 
	 * already logged in redirect them to user controller.
	 * 	username : atleast 4 characters
	 * 	password  : atleast 6 characters 
	 * 	refer private $_min_username_length and $_min_password_length 
	 * 	
	 * @return void
	 */
	public function login() 
	{
		// load session library for checking user already logged in and to saving user login 
		// details if user verified successfuly. 
		$this->load->library('session');
		
		// check if user already logged in by checking session data exist
		if ($this->session->userdata('uid')) 
		{
			redirect('user');	
			return;
		}

		// if username and password are send through post method
		if ($this->input->post('username') && $this->input->post('password')) 
		{

			// validate the username and password
			$this->load->library('form_validation');
			
			// username and password : Required and check for min_length
			$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[' . $this->_min_username_length . ']'); 
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[' . $this->_min_password_length . ']'); 

			// if the requirement are not meet redirect to login page to re-enter the login details 
			if ($this->form_validation->run() == FALSE)
			{
				redirect('accounts/login?msg=Incorrect username or password');	
				return;
			}

			//store to temporary variable to pass to model
			$input = $this->input->post();
			
			$login_details = array('username'=>$input['username'], 'password'=>$input['password']);

			// if account_model .login return array it means user verified 
			// if not redirect to login page to re-enter details 
			// returned array is saved as  session data contains data in talbe 
			// 	login.uid, login.email (primarykey), cookie_id (random string) and current ipAddress.
			if ($user_login_details = $this->accounts_model->login($login_details)) 
			{ 
				// session library already loaded. so dont have to load again
				$this->session->set_userdata($user_login_details);
				redirect('user?msg=Login successful');
				return;
			} 
			else 
			{
				// login failed . redirect to try again
				redirect('accounts/login?msg=Invalid username or password ');	
				return;
			} 
		}

		// load form helper for form_open() in view
		$this->load->helper('form');
		
		$data_to_render_public['title'] = 'Login';
		$data_to_render_public['action'] = 'accounts/login';

		// Render view in public/login with title Login and form action accounts/login 
		$this->_render_public_view('login', $data_to_render_public);
	}

	// --------------------------------------------------------------------
	
	/**
	 * User Signup
	 *
	 * To create account for users 
	 *  Input username , password etc.
	 *  insert to login table using accounts_model.
	 *  
	 * @return void
	 */
	public function signup() 
	{
		// load session library for checking user already logged in and to saving user login 
		// details if user verified successfuly. 
		$this->load->library('session');
		
		// check if user already logged in by checking session data exist
		if ($this->session->userdata('uid')) 
		{
			redirect('user');	
			return;
		}

		// if username and password are send through post method
		if ($this->input->post('username') && $this->input->post('password')) 
		{

			// validate the username and password
			$this->load->library('form_validation');
			
			// username and password : Required and check for min_length
			$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[' . $this->_min_username_length . ']'); 
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[' . $this->_min_password_length . ']'); 

			// if the requirement are not meet redirect to signup page to re-enter the signup details 
			if ($this->form_validation->run() == FALSE)
			{
				redirect('accounts/signup?msg=Check all input fields and try again.');	
				return;
			}

			//store to temporary variable to pass to model
			$input = $this->input->post();

			$signup_details = array('username'=>$input['username'], 'password'=>$input['username']);

			// if account_model .signup return bool true if user created 
			// if not redirect to signup page to re-enter details. 
			if ($this->accounts_model->signup($signup_details)) 
			{
				redirect('accounts/login?msg=Account created login to continue');
				return;
			}  
			redirect('accounts/signup?msg=Some error occured. Try again');
			return; 
		}

		// load form helper for form_open() in view
		$this->load->helper('form');
		
		$data_to_render_public['title'] = 'Signup';
		$data_to_render_public['action'] = 'accounts/signup';

		$this->_render_public_view('signup', $data_to_render_public);
	}

	// --------------------------------------------------------------------

	/**
	 * Logout account
	 *
	 * This function distroy session and redirect to login page.
	 * 
	 * @return void
	 */
	public function logout() 
	{
		// load session library to  
		$this->load->library('session');
		$user_data = $this->session->all_userdata();

		// unset data if array key is not  session_id or ip_address  or user_agent
		foreach ($user_data as $key => $value) {
				$this->session->unset_userdata($key);
		}

		// distroy session 
		$this->session->sess_destroy();
		
		// if message passed through get('msg') it has to pass to login page
		$prev_msg = (($this->input->get('msg') == FALSE) ? FALSE : $this->input->get('msg'));
 
		// append ' logout successful ' message  to previous message and redirect to login page 
		$msg = ($prev_msg == FALSE) ? "msg=Logout successful" : "msg=" . $prev_msg . " ";
		redirect("accounts/login?" . $msg);
	}


	  
}
