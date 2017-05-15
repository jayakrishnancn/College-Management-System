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
 	/**
 	 * Session data in array so that redudent function call can be reduced
 	 * @var array
 	 */
	private $session_data = [];

	/**
	 * min username length load from common function library
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
		
		// load session and common function libraries
		$this->load->library(['session', 'common_functions']);

		$this->_min_username_length = $this->common_functions->_min_username_length;
		$this->_min_password_length = $this->common_functions->_min_password_length;

		// if session is not valid redirect to accounts/login page
		$this->common_functions->redirect_unknown_user();

		// set session data to a private variable
		$this->session_data = $this->session->userdata();

		// load both accounts and public model
		$this->load->model(['public_model']);

		// verify ip from common function library
		// this function checks if ip_address from session is same as
		// current ip address 
		$this->common_functions->verify_ip();

  
		// if user and cookie verified continue 
		$this->common_functions->verify_user_and_cookie();
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
		if ($input = $this->input->post()) 
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[' . $this->_min_username_length. ']');
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

			// return true if insertion successful  
			if ($this->admin_model->add_user($userdata)) 
			{
				redirect('admin/adduser?msg=User added');
				return;
			} 

			redirect('admin/adduser?msg=Couldn\'t add User. Try again');
			return;
		}
 
		$this->load->library('form_builder');

		$this->form_builder->startform(['action' => 'admin/adduser', 'heading' => 'Add User']);
		
		$this->form_builder->addlabel('Username');
		$this->form_builder->addinput(['name' => 'username', 'placeholder' => "Username", 'autofocus' => true]);

		$this->form_builder->addlabel('Password');
		$this->form_builder->addinput(['name' => 'password', 'placeholder' => "Password", 'type' => 'password']);

		$this->form_builder->addlabel('Permission');
		$this->form_builder->startdropdown('permissionid'); 
		foreach ($this->admin_model->get_permissions() as $key => $value) {
			$this->form_builder->dropdownoption($value['groupname'], $value['permissionid']);
		}
		$this->form_builder->enddropdown();

		$this->form_builder->setbutton('Add user');

		$this->render_admin_view('form_builder');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Add or delete user permission
	 * @return void
	 */
	public function adddeleteuserpermissions() 
	{
		// store post data to an array
		if ($input = $this->input->post()) 
		{
			$this->load->library('form_validation');
		
			$this->form_validation->set_rules('uid', 'user id', 'required');
			$this->form_validation->set_rules('permissionid', 'permissions', 'trim|required|greater_than_equal_to[0]|numeric|is_natural'); 
			
			if ($this->form_validation->run() == FALSE) 
			{
				redirect('admin/adddeleteuserpermissions?msg=Check all fields');
				return;
			}

			if(!isset($input['deletekey']))
				$input['deletekey']='';

			$userdata = array(
				'uid' => $input['uid'],
				'permissionid' => $input['permissionid']
			); 

			if(isset($input['deletekey']) && $input['deletekey'] =='true' )
			{
					if($this->admin_model->delete_user_permission($userdata))
					{
						redirect('admin/adddeleteuserpermissions?msg=User permission deleted');
						return;
					}
					else{
						
						redirect('admin/adddeleteuserpermissions?msg=can\'t delete User permission.Try again');
						return;
					}
			}
			else{
				if ($this->admin_model->add_user_permission($userdata)){

					redirect('admin/adddeleteuserpermissions?msg=User permission added');
					return;
				}
 				redirect('admin/adddeleteuserpermissions?msg=Cant\'t add permission. Likely to be Already done before ');
				return ;			
			} 
		}
 
		$this->load->library('form_builder');

		$heading='Add User Permission';
		if($this->input->get('delete') == 'true')
		{
			$heading='Delete User Permission';
		}

		$this->form_builder->startform(['action' => 'admin/adddeleteuserpermissions', 'heading' => $heading]);
		$this->form_builder->addlabel('Username');
		$this->form_builder->startdropdown('uid');
		
		foreach ($this->admin_model->get_user() as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['email'], $value['uid']);
		}
		$this->form_builder->enddropdown();
		$this->form_builder->addlabel('Permission');
		$this->form_builder->startdropdown('permissionid');
		
		foreach ($this->admin_model->get_permissions() as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['groupname'], $value['permissionid']);
		}
		$this->form_builder->enddropdown();
  
		if ($this->input->get('delete') == 'true') 
		{
			$this->form_builder->addinput('deletekey', 'hidden', false,'true');
			$this->form_builder->setbutton('Delete permission');
		} 
		else 
		{
		   $this->form_builder->addinput('deletekey', 'hidden', false,'false');
			$this->form_builder->setbutton('Add permission');
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

	// --------------------------------------------------------------------
	
	/**
	 * User History
	 * 
	 * @return void
	 */
	public function history() 
	{ 
		$data['table'] =  $this->common_functions->history();
		$this->render_admin_view('public/history', $data,false);
	}

	// --------------------------------------------------------------------
	
	/**
	 * reset password by admin
	 *
	 * accepts email (unique key of login table)
	 * @return void
	 */
	public function resetpassword() 
	{
		if (($email = $this->input->get('email')) == false || ($this->input->get('email') == NULL)) 
		{
			redirect('admin/manageusers?msg=Couldn\'t reset password.Empty Fields');
			
			return;
		}

		if ($this->admin_model->resetpassword($email)) 
		{
			redirect('admin/manageusers?msg=Password Reset to email id ');
			
			return;
		}
		redirect('admin/manageusers?msg=Couldn\'t reset password');
	}
 
	// --------------------------------------------------------------------
	
	/**
	 * deleteuser by admin
	 * 
	 * @return void
	 */
	public function deleteuser() 
	{	
		$this->load->library('form_validation');

		if ($email=$this->input->get('email')) 
		{

			$this->form_validation->set_data($_GET);
			$this->form_validation->set_rules('email', 'Email-id', 'required|min_length[3]');

			if ($this->form_validation->run() == FALSE) 
			{
				redirect('admin/manageusers?msg=username not valid'); 
				return;
			}
 
			if ($this->admin_model->deleteuser($email)) 
			{
				redirect('admin/manageusers?msg=user  account ( ' . $email . ' ) deleted'); 
				return;
			}

			redirect('admin/manageusers?msg=Cant delete user.Try again'); 
			return;
		}

		redirect('admin/manageusers?msg=username required'); 
		return;
	}

	// --------------------------------------------------------------------
	/**
	 * Edit user details 
	 * 
	 * Edit details of user by admin
	 * inputs oldemail of user (unique key in login table) and new inputs
	 * @return void
	 */
	public function edituser() 
	{

		$this->load->library(['form_builder', 'form_validation']);

		if ($email = $this->input->post('email')) 
		{
			$oldemail = $this->input->post('oldemail');
			$this->form_validation->set_rules('oldemail', 'oldemail', 'required|min_length[4]');
			$this->form_validation->set_rules('email', 'email', 'required|min_length[4]');

			if ($this->form_validation->run() == FALSE) 
			{
				redirect('admin/manageusers?msg=username not valid'); 
				return;
			}

			// parameter are 'values' and 'where' inquery 
			if ($this->admin_model->updateuserdetails(['email' => $email], ['email' => $oldemail])) {
				redirect('admin/manageusers?msg=user details updated');
				
				return;
			}
			redirect('admin/manageusers?msg=can\'t  update user details');
			
			return;
		}
		 elseif ($email = $this->input->get('email')) 
		 {
		 	// if get then show the form to fill the changes
			$userdata = $this->admin_model->get_user(['email' => $email]);

			$this->form_builder->startform(['action' => 'admin/edituser', 'heading' => 'Change User Details (' . $email . ') ']);

			$this->form_builder->addlabel('E-mail id');
			$this->form_builder->addinput('email', 'text', true, $userdata['email']);

			$this->form_builder->addinput('oldemail', 'hidden', true, $userdata['email']);
			$this->form_builder->setbutton('Change Details');
			$this->render_admin_view('form_builder');
			
			return;
		}
		redirect('admin/manageusers?msg=user not found');
		
		return;
	}
	
}
