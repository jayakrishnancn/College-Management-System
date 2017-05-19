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
 class Admin extends MY_Controller { 

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
		 
 
 		// verify if user has permission to this admin controller (area).
		$this->common_functions->verify_permission('admin');
 
 		

		$this->load->model('admin_model');


		$this->_min_username_length = $this->common_functions->_min_username_length;
		$this->_min_password_length = $this->common_functions->_min_password_length;
 
	}

	// --------------------------------------------------------------------
	
	/**
	 * Render Admin View
	 * 
	 * For loading a common admin bootstrap view .This is to improve consistency and reduce 
	 * redudent code. Use  $this->_render_admin_view() insted of $this->load->view() 
	 * 
	 * @param  string  $page 		relative path of php view file to render
	 * @param  array   $data 		To supply data to view 
	 * @param  boolean $default_directory 		If true relative path will start from view/admin. 
	 *                             If false  relative path will start from view
	 *                             
	 * @return void
	 */
	private function _render_admin_view($page, $data = array(), $default_directory = true) 
	{


		// store data, page etc to an array to pass to view so that it can render that page with 
		// data on view
		$data_to_pass['data'] = $data;

		// if $default_directory is true render from view/public directory  
		$data_to_pass['page'] = ($default_directory === true) ?  'admin/' . $page:$page;
   	
   		// session data
		$data_to_pass['data']['session_data'] = $this->session_data; 
	
	   	// setup data from public model
		$this->load->model('public_model');
		
		// To display college name and other details 
		$data_to_pass['setup'] = $this->public_model->setup_data();

		// To display in navbar
		$data_to_pass['userpermission'] = $this->public_model->user_groups($this->session_data['uid']);
		// for toast notification message
		$data_to_pass['msg'] = $this->session->flashdata('msg');
		
		// filter all data  
		// load default admin/ bootstrap view  in application/view directory 
		$this->load->view('bootstrap', $data_to_pass);
	}


	// --------------------------------------------------------------------
	
	/**
	 *  Admin Index 
	 *
	 * Default method for admin controller
	 * 
	 * @return void
	 */
	public function index() 
	{
		$this->_render_admin_view('home');
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

			// run the form validation
			if ($this->form_validation->run() == FALSE) 
			{
				$this->session->set_flashdata('msg','Check all fields');
				redirect('admin/adduser'); 
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
				$this->session->set_flashdata('msg','User added');
				redirect('admin/adduser');
				return;
			} 
			$this->session->set_flashdata('msg','Couldn\'t add User. Try again');
			redirect('admin/adduser');
			return;
		}
 
		$this->load->library('form_builder');

		// form starts to register in form_builder library
		$this->form_builder->start_form(['action' => 'admin/adduser', 'heading' => 'Add User']);
		
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
		// form_builder ends
		
		$this->_render_admin_view('public/form_builder',NULL,false);
	}

	// --------------------------------------------------------------------
	 
	/**
	 * Add User Permission
	 *
	 * this will add user permissions.
	 * @return void
	 */
	public function adduserpermission() 
	{
		// store post data to an array
		if ($input = $this->input->post()) 
		{
			$this->load->library('form_validation');
		
			$this->form_validation->set_rules('uid', 'user id', 'trim|required');
			$this->form_validation->set_rules('permissionid', 'permissions', 'trim|required|greater_than_equal_to[0]|numeric|is_natural'); 
			
			if ($this->form_validation->run() == FALSE) 
			{
				$this->session->set_flashdata('msg','Check all fields');
				redirect('admin/adduserpermission');
				return;
			}
			if ($this->admin_model->add_user_permission($input))
			{ 
				$this->session->set_flashdata('msg','User permission added');
				redirect('admin/adduserpermission');
				return;
			}
				$this->session->set_flashdata('msg','Cant\'t add permission. Likely to be Already done before');
			redirect('admin/adduserpermission');
			return ;			 
		}
 
		$this->load->library('form_builder'); 

		$this->form_builder->start_form(['action' => 'admin/adduserpermission', 'heading' => 'Add User Permission']);
		$this->form_builder->addlabel('Username');

		// dropdown starts
		$this->form_builder->startdropdown('uid');
		
		foreach ($this->admin_model->get_user() as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['email'], $value['uid']);
		}
		$this->form_builder->enddropdown();
		// dropdown ends
		
		$this->form_builder->addlabel('Permission');
		
		// dropdown starts
		$this->form_builder->startdropdown('permissionid'); 
		foreach ($this->admin_model->get_permissions() as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['groupname'], $value['permissionid']);
		}
		$this->form_builder->enddropdown();
		// dropdown ends 
		
		$this->form_builder->setbutton('Add permission'); 

		$this->_render_admin_view('public/form_builder',NULL,false);
	}  

	// --------------------------------------------------------------------
	
	/**
	 * Delete user permission
	 * This will add user permissions, if it exists
	 * 
	 * @return void
	 */
	public function deleteuserpermission() 
	{
		// store post data to an array
		if ($input = $this->input->post()) 
		{
			$this->load->library('form_validation');
		
			$this->form_validation->set_rules('uid', 'user id', 'trim|required');
			$this->form_validation->set_rules('permissionid', 'permissions', 'trim|required|greater_than_equal_to[0]|numeric|is_natural'); 
			
			if ($this->form_validation->run() == FALSE) 
			{
				$this->session->set_flashdata('msg','Check all fields');
				redirect('admin/deleteuserpermission');
				return;
			}
 
			$userdata = array(
				'uid' => $input['uid'],
				'permissionid' => $input['permissionid']
			); 
 
			if($this->admin_model->delete_user_permission($userdata))
			{
				$this->session->set_flashdata('msg','User permission revoked');
				if($input['uid'] == $this->session_data['uid'])
				{
					redirect('accounts/logout');				
				}
				redirect('admin/deleteuserpermission');
				return;
			}
			else{
				$this->session->set_flashdata('msg','Can\'t revoke user permission. Try again');
				redirect('admin/deleteuserpermission');
				return;
			}  
		}
 
		$this->load->library('form_builder'); 

		$this->form_builder->start_form(['action' => 'admin/deleteuserpermission', 'heading' => 'Delete User Permission']);
		$this->form_builder->addlabel('Username');

		// dropdown starts
		$this->form_builder->startdropdown('uid');
		
		foreach ($this->admin_model->get_user() as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['email'], $value['uid']);
		}
		$this->form_builder->enddropdown();
		// dropdown ends
		
		$this->form_builder->addlabel('Permission');
		
		// dropdown starts
		$this->form_builder->startdropdown('permissionid'); 
		foreach ($this->admin_model->get_permissions() as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['groupname'], $value['permissionid']);
		}
		$this->form_builder->enddropdown();
		// dropdown ends 
		
		$this->form_builder->setbutton('Revoke permission'); 

		$this->_render_admin_view('public/form_builder',NULL,false);
	}

	// -------------------------------------------------------------------- 
	
	/**
	 * Manage Users
	 * @return void
	 */
	public function manageusers() 
	{ 
		$data['table'] = $this->admin_model->get_user_with_access();
		$data['table_title'] = 'Users ';
		if(is_array($data['table']))
		{
 			foreach ($data['table'] as $key => &$value) 
	 		{
	 			$value['action'] = '<a class="btn btn-default btn-sm " href="'  .base_url('admin/resetpassword')  .  '?email=' . urlencode($value['email']) . '">Reset password</a>
						<a class="btn btn-default btn-sm " href="'  .base_url('admin/edituser')  .  '?email=' . urlencode($value['email']) . '">Edit </a>
						<a class="btn btn-default btn-sm confirmation"  href="'  .base_url('admin/deleteuser')  .  '?email=' . urlencode($value['email']) . '">Delete </a>';
	 		}
 			unset($value);
 		}
		$this->_render_admin_view('public/table', $data,false);
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
		$this->_render_admin_view('public/history', $data,false);
	}
 

	// 
	// --------------------------------------------------------------------
	
	/**
	 * reset password by admin
	 *
	 * accepts email (unique key of login table) as get 
	 * accepts username and new password as post method and reset user password
	 *  to new password. 
	 * @return void
	 */
	public function resetpassword() 
	{
		if($email=$this->input->get('email'))
		{

			$this->load->library('form_builder'); 

			$this->form_builder->start_form(['action' => 'admin/resetpassword', 'heading' => 'Change Password']);
			
			$this->form_builder->addlabel('change password of user '.$email);
			$this->form_builder->addinput('username','hidden',false,$email);
			$this->form_builder->addlabel('password (same as email id by default)');
			$this->form_builder->addinput('password','password',true,$email);
			$this->form_builder->setbutton('Reset Password',' confirmation '); 

			$this->_render_admin_view('public/form_builder',FALSE,FALSE);

			return;
		} 
		elseif($this->input->post('password'))
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[' . $this->_min_username_length. ']');
			$this->form_validation->set_rules('password', 'Password', 'required|min_length[' .  $this->_min_password_length . ']');

			// run the form validation
			if ($this->form_validation->run() == FALSE) 
			{
				$this->session->set_flashdata('msg','Check all fields');
				redirect('admin/resetpassword'); 
				return;
			}

			$input=$this->input->post();
			
			// Also delete history to automatically logout from other deveices
			if ($this->admin_model->reset_password($input['username'],$input['password'])) 
			{
				$this->session->set_flashdata('msg','Password has chenged.');
				redirect('admin/manageusers');
				return;
			}
			else
			{
				$this->session->set_flashdata('msg','Couldn\'t change password');
				redirect('admin/manageusers');
				return;
			}
		}
		$this->session->set_flashdata('msg','Missing fields. Try again');
		redirect('admin/manageusers');
	}

 
	// --------------------------------------------------------------------
	
	/**
	 * deleteuser by admin
	 *
	 * Inputs email (unique key in login table) to delete user.
	 * It deletes record of user from every table 
	 * @return void
	 */
	public function deleteuser() 
	{	
		$this->load->library('form_validation');

		if ($this->input->post('email') && $this->input->post('type_yes')) 
		{

			$this->form_validation->set_rules('email', 'Email-id', 'required|min_length[3]');

			if ($this->form_validation->run() == FALSE) 
			{

				$this->session->set_flashdata('msg','Username not valid');
				redirect('admin/manageusers'); 
				return;
			}

			$email=$this->input->post('email');
			
			if(strtolower($this->input->post('type_yes') )!= 'yes')
			{

				$this->session->set_flashdata('msg','Confirm Delete');
				redirect('admin/deleteuser?email=' .  $email .  ''); 
				return;
			}
 
			if ($this->admin_model->delete_user($email)) 
			{
				$this->session->set_flashdata('msg','User account deleted');
				redirect('admin/manageusers'); 
				return;
			}

			$this->session->set_flashdata('msg','Cant delete user. Try again');
			redirect('admin/manageusers'); 
			return;
		}
		else if($email = $this->input->get('email'))
		{

			$this->load->library('form_builder'); 
			$this->form_builder->start_form(['action' => $this->current_url, 'heading' =>'Confirm Delete User']);
			$this->form_builder->addlabel('Type "Yes" to delete User  ');
			$this->form_builder->addinput('type_yes');
			$this->form_builder->setbutton('Confirm Delete');
			$this->form_builder->addinput('email','hidden',false,$email);
			$this->_render_admin_view('public/form_builder',false,false);
			return;
		}

		$this->session->set_flashdata('msg','Username required');
		redirect('admin/manageusers'); 
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
				$this->session->set_flashdata('msg','Username not valid');
				redirect('admin/manageusers'); 
				return;
			}

			// parameter are 'values' and 'where' inquery 
			if ($this->admin_model->update_user_details(['email' => $email], ['email' => $oldemail])) 
			{

				$this->session->set_flashdata('msg','User details updated');
				redirect('admin/manageusers');
				return;
			}
			$this->session->set_flashdata('msg','cant update user details');
			redirect('admin/manageusers');
			return;
		}
		 elseif ($email = $this->input->get('email')) 
		 {
		 	// if get then show the form to fill the changes
			$userdata = $this->admin_model->get_user(['email' => $email]);

			$this->form_builder->start_form(['action' => 'admin/edituser', 'heading' => 'Change User Details (' . $email . ') ']);

			$this->form_builder->addlabel('E-mail id');
			$this->form_builder->addinput('email', 'text', true, $userdata['email']);

			$this->form_builder->addinput('oldemail', 'hidden', true, $userdata['email']);
			$this->form_builder->setbutton('Change Details');
			$this->_render_admin_view('public/form_builder',NULL,false);
			
			return;
		}
		$this->session->set_flashdata('msg','User not found');
		redirect('admin/manageusers');
		return;
	}
	
}
