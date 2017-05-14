<?php
 
class common_functions {
	private $CI;
	public $_min_username_length=4;	
	public $_min_password_length=6;	

	function __construct() {
		$this->CI = & get_instance();
		$this->CI->load->library('session');
		$this->CI->load->model('public_model');
	}
	/**
	 * Verify Permission
	 * @param  string $value  groupname in permission table
	 * @param  boolean $return  if true returns bool if not redirect 
	 * 
	 * @return bool
	 */
	public function verify_permission($value, $return = false) {

		$dbdata = $this->CI->public_model->userGroups($this->CI->session->userdata('uid'));

		if (is_array($dbdata) && in_array($value, $dbdata)) {
			
			return true;
		}
		if ($return) {
			
			return false;
		}
		redirect('user?msg=you dont have the permission to view this page');
		die;
	}
	/**
	 *  Verify Ip address
	 *  To verify ip address stored in  session is same as current ip address
	 * @param  boolean $redirect_invalid	if true redirect to accounts/logout. else return bool 
	 * 
	 * @return bool if valid ip return true else return false 
	 */
	public function verify_ip($redirect_invalid=false) 
	{
		// check for validity
		if($this->CI->input->ip_address() != $this->CI->session->userdata('ip_address'))
		{   
			// redirect if redirect_invalid is set to true
			if($redirect_invalid)
			{
				redirect('accounts/logout?msg=Login Again.');
			}
			return false;
		}
		return true;
	}
	/**
	 * Verify user and cookie
	 * @param  array $data  must contains uid,email,ip_address,cookieid
	 * 
	 * @return bool 	if verifed cookie return true 
	 */
	public function verify_user_and_cookie($data)
	{

		if($this->CI->public_model->verify_user(['uid'=>$data['uid'], 'email' => $data['email'] ]))
		{
			if($this->CI->public_model->verify_cookie($data))
			{
				return true;
			}
		}

			redirect('accounts/logout?msg=can\'t verify user! Login Again.');
			return false;
	}

	public function history() {
		$this->CI->load->model('accounts_model');
		
		return $this->CI->accounts_model->get_history($this->CI->session->userdata('uid'));
	}
}
