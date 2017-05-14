<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Class
 * 
 * This class enables you to controll and manage admin accounts. It includes functions 
 * to manage users, add or delete user permissions etc.
 * 
 * @category Controller
 * @author   jayakrishnancn
 * @link  https://github.com/jayakrishnancn/College-Management-System
 */
 class Admin extends CI_Controller {
 
	private $session_data = [];
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
		
		// load session and common function libraries
		$this->load->library(['session', 'common_functions']);

		$this->_min_username_length = $this->common_functions->_min_username_length;
		$this->_min_password_length = $this->common_functions->_min_password_length;

		// if session is not valid redirect to accounts/login page
		if (!$this->session->userdata('uid')) {
			redirect('accounts/login?msg=Login again');
			return;
		}

		// set session data to a private variable
		$this->session_data = $this->session->userdata();

		// load both accounts and public model
		$this->load->model(['accounts_model', 'public_model']);

		// verify ip from common function library
		// this function checks if ip_address from session is same as
		// current ip address 
		$this->common_functions->verify_ip(true);

		// temporary array store session data
		$session_data_to_verify = array(
				'ip_address' => $this->input->ip_address() ,
				'cookie_id' => $this->session_data['cookie_id'],
				'email' => $this->session_data['email'],
				'uid' => $this->session_data['uid']
			);

		// if user and cookie verified continue 
		$this->common_functions->verify_user_and_cookie($session_data_to_verify);

		// if admin is in user permission continue
		// else redirect.  
		$this->common_functions->verify_permission('admin');

		$this->load->model('admin_model');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Render Admin View
	 * 
	 * For loading a common admin bootstrap view .This is to improve consistency and reduce 
	 * redudent code. Use  $this->render_admin_view() insted of $this->load->view() 
	 * 
	 * @param  string  $page 		relative path of php view file to render
	 * @param  array   $data 		To supply data to view 
	 * @param  boolean $default_directory 	If true relative path will start from view/admin. 
	 *                             If false  relative path will start from view
	 *                             
	 * @return void
	 */
	private function render_admin_view($page, $data = array(), $default_directory = true) 
	{


		// store data, page etc to an array to pass to view so that it can render that page with 
		// data on view
		$data_to_pass['data'] = $data;

		// if $default_directory is true render from view/public directory  
		$data_to_pass['page'] = ($default_directory === true) ?  'admin/' . $page:$page;
   	
   	// session data
		$data_to_pass['data']['session_data'] = $this->session_data; 
   	// setup data
		$data_to_pass['setup'] = $this->public_model->setup_data();
		$data_to_pass['userpermission'] = $this->admin_model->get_usergroup($this->session_data['uid']);

		$this->load->view('admin/bootstrap', $data_to_pass);
	}


	// --------------------------------------------------------------------
	
	/**
	 *  Accounts Index 
	 *
	 * Default method for accounts controller
	 * calls login by default (might change in future)
	 * 
	 * @return void
	 */
	public function index() 
	{
		$this->render_admin_view('home');
	}


	// --------------------------------------------------------------------
	
	/**
	 *  Add User 
	 *  
	 *  Add user by admin
	 *  
	 * @return void
	 */
	public function adduser() 
	{
		if ($input = $this->input->post('username')) 
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[' . $this->_min_username_length. ']|is_unique[' . $this->tables['login'] . '.email]');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[' .  $this->_min_password_length . ']');
			$this->form_validation->set_rules('permissionid', 'permissions', 'trim|required|greater_than_equal_to[0]|numeric|is_natural');
			
			if ($this->form_validation->run() == FALSE) 
			{
				redirect('admin/adduser?msg=Check all fields'); 
				return;
			}

			$userdata = array(
				'username' => $input['username'],
				'password' => $input['password'],
				'permissionid' => $input['permissionid']
			);

			if ($this->admin_model->add_user($userdata)) 
			{
				redirect('admin/adduser?msg=User added');
				return;
			} 

			redirect('admin/adduser?msg=Couldn\'t add User. Try again');
			return;
		}

		$this->load->helper('form');
		$this->load->library('FormBuilder');

		$this->formbuilder->startform(['action' => 'admin/adduser', 'heading' => 'Add User']);
		$this->formbuilder->addlabel('Username');
		$this->formbuilder->addinput(['name' => 'username', 'placeholder' => "Username", 'autofocus' => true]);
		$this->formbuilder->addlabel('Password');
		$this->formbuilder->addinput(['name' => 'password', 'placeholder' => "Password", 'type' => 'password']);
		$this->formbuilder->addlabel('Permission');
		$this->formbuilder->startdropdown('permissionid');
		
		foreach ($this->admin_model->get_permissions() as $key => $value) {
			$this->formbuilder->dropdownoption($value['groupname'], $value['permissionid']);
		}
		$this->formbuilder->enddropdown();
		$this->formbuilder->setbutton('Add user');

		$this->render_admin_view('form_builder');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Add or delete user permission
	 * @return void
	 */
	public function adddeleteuserpermissions() 
	{
		if ($input = $this->input->post('permissionid')) 
		{
			$this->load->library('form_validation');
		
			$this->form_validation->set_rules('uid', 'user id', 'required');
			$this->form_validation->set_rules('permissionid', 'permissions', 'trim|required|greater_than_equal_to[0]|numeric|is_natural');
			$this->form_validation->set_rules('deletekey', 'deletekey', '');
		
			if ($this->form_validation->run() == FALSE) 
			{
				redirect('admin/adddeleteuserpermissions?msg=Check all fields');
				return;
			}

			$userdata = array(
				'uid' => $input['uid'],
				'permissionid' => $input['permissionid'],
				'deletekey' => $input['deletekey']
			); 

			if ($this->admin_model->add_user_permission($userdata)) 
			{
				$process=($input['deletekey']=='true')?"deleted":"added";
				redirect('admin/adddeleteuserpermissions?msg=User permission '.$process, 'refresh');
				return;
			}
			redirect('admin/adddeleteuserpermissions?msg=Couldn\'t add permission. Likely to be Already done before ', 'refresh');
			return;
		}

		$this->load->helper('form');
		$this->load->library('FormBuilder');

		$heading='Add User Permission';
		if($this->input->get('delete') == 'true')
		{
			$heading='Delete User Permission';
		}

		$this->formbuilder->startform(['action' => 'admin/adddeleteuserpermissions', 'heading' => $heading]);
		$this->formbuilder->addlabel('Username');
		$this->formbuilder->startdropdown('uid');
		
		foreach ($this->admin_model->getUser() as $key => $value) 
		{
			$this->formbuilder->dropdownoption($value['email'], $value['uid']);
		}
		$this->formbuilder->enddropdown();
		$this->formbuilder->addlabel('Permission');
		$this->formbuilder->startdropdown('permissionid');
		
		foreach ($this->admin_model->get_permissions() as $key => $value) 
		{
			$this->formbuilder->dropdownoption($value['groupname'], $value['permissionid']);
		}
		$this->formbuilder->enddropdown();
  
		if ($this->input->get('delete') == 'true') 
		{
			$this->formbuilder->addinput('deletekey', 'hidden', false,'true');
			$this->formbuilder->setbutton('Delete permission');
		} 
		else 
		{
		   $this->formbuilder->addinput('deletekey', 'hidden', false,'false');
			$this->formbuilder->setbutton('Add permission');
		}

		$this->render_admin_view('form_builder');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Manage Users
	 * @return void
	 */
	public function manageusers() 
	{ 
		$data['table'] = $this->admin_model->get_user_with_access();
		$this->render_admin_view('table', $data);
	}

	/**
	 * User History
	 * @return void
	 */
	public function history() 
	{ 
		$data['table'] =  $this->common_functions->history();
		$this->render_admin_view('public/history', $data,false);
	}

	public function resetpassword() 
	{
		if (($email = $this->input->get('email')) == false || ($this->input->get('email') == NULL)) {
			redirect('admin/manageusers?msg=Couldn\'t reset password.Empty Fields', 'refresh');
			
			return;
		}
		if ($this->admin_model->resetpassword($email)) {
			redirect('admin/manageusers?msg=Password Reset to email id ', 'refresh');
			
			return;
		}
		redirect('admin/manageusers?msg=Couldn\'t reset password', 'refresh');
	}
	public function deleteuser() {
		$this->load->helper(array(
			'form',
			'url'
		));
		$this->load->library('form_validation');
		if ($this->input->get('emailid')) {
			$this->form_validation->set_data($_GET);
			$this->form_validation->set_rules('emailid', 'emailid', 'required');
			if ($this->form_validation->run() == FALSE) {
				redirect('admin/manageusers?msg=username not valid', 'refresh');
				
				return;
			}
			$email = $this->input->get('emailid');
			if ($this->admin_model->deleteuser($email)) {
				redirect('admin/manageusers?msg=user  account ( ' . $email . ' ) deleted', 'refresh');
				
				return;
			}
			redirect('admin/manageusers?msg=Cant delete user.Try again', 'refresh');
			
			return;
		}
		redirect('admin/manageusers?msg=username required', 'refresh');
		
		return;
	}
	public function edituser() {
		$this->load->library(['FormBuilder', 'form_validation']);
		if ($email = $this->input->post('email')) {
			$oldemail = $this->input->post('oldemail');
			$this->form_validation->set_rules('oldemail', 'oldemail', 'required|min_length[4]');
			$this->form_validation->set_rules('email', 'email', 'required|min_length[4]');
			if ($this->form_validation->run() == FALSE) {
				redirect('admin/edituser?msg=username not valid', 'refresh');
				
				return;
			}
			if ($this->admin_model->updateuserdetails(['email' => $email], ['email' => $oldemail])) {
				redirect('admin/edituser?msg=user details updated', 'refresh');
				
				return;
			}
			redirect('admin/edituser?msg=can\'t  update user details', 'refresh');
			
			return;
		} elseif ($email = $this->input->get('email')) {
			$userdata = $this->admin_model->getUser(['email' => $email]);
			$this->formbuilder->startform(['action' => 'admin/edituser', 'heading' => 'Change User Details (' . $email . ') ']);
			$this->formbuilder->addlabel('E-mail id');
			$this->formbuilder->addinput('email', 'text', true, $userdata['email']);
			$this->formbuilder->addinput('oldemail', 'hidden', true, $userdata['email']);
			$this->formbuilder->setbutton('Change');
			$this->renderadmin('form_builder');
			
			return;
		}
		redirect('admin/manageusers?msg=user not found', 'refresh');
		
		return;
	}
}
