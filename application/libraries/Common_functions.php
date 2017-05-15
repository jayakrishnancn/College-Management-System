<?php
 /**
  * Common Functions  
  * 
  *  Used to call from different controllers 
  *    
  */
class common_functions {
	/**
	 * CI to store the super codeigniter object . 
	 * @var object
	 */
	private $CI;
	private $session_data = [];

	/**
	 * To ser minimum length of username and password
	 * @var integer
	 */
	public $_min_username_length=4;	
	public $_min_password_length=6;	

	/**
	 * Class constructor
	 *
	 * @return  void
	 */
	function __construct() 
	{
		$this->CI = & get_instance();
	
		$this->CI->load->library('session');
		$this->CI->load->model('public_model');
		$this->session_data=$this->CI->session->userdata();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Redirect unknown user
	 *
	 * 	redirect unknown user to logout page , by checking the session uid exist or not.
	 * @param  boolean $return  if TRUE returns bool if not redirect 
	 * @return bool
	 */
	public function redirect_unknown_user($redirect_invalid = TRUE)
	{
		if($this->CI->session->userdata('uid'))
		{
			return TRUE;
		}

		if ($redirect_invalid) 
		{
			redirect('accounts/logout?msg=Login again');
		}
		
			return FALSE;
	}
	// --------------------------------------------------------------------

	/**
	 * Verify Permission
	 * @param  string $value  groupname in permission table
	 * @param  boolean $return  if TRUE returns bool if not redirect 
	 * 
	 * @return bool
	 */
	public function verify_permission($value, $redirect_invalid = TRUE) 
	{

		$dbdata = $this->CI->public_model->user_groups($this->session_data['uid']);

		if (is_array($dbdata) && in_array($value, $dbdata)) 
		{ 
			return TRUE;
		}

		if ($redirect_invalid) 
		{
			redirect('user?msg=you dont have the permission to view this page');
		}
		
			return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 *  Verify Ip address
	 *  
	 *  To verify ip address stored in  session is same as current ip address
	 * @param  boolean $redirect_invalid		if TRUE redirect to accounts/logout. else return bool 
	 * 
	 * @return bool  
	 */
	public function verify_ip($redirect_invalid=TRUE) 
	{
		// check for validity
		if($this->CI->input->ip_address() != $this->session_data['ip_address'])
		{   
			// redirect if redirect_invalid is set to TRUE
			if($redirect_invalid)
			{
				redirect('accounts/logout?msg=Login Again.');
			}
			return FALSE;
		}
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Verify user and cookie
	 * @param  array $data  must contains uid,email,ip_address,cookieid
	 * 
	 * @return bool 	if verifed cookie return TRUE 
	 */
	public function verify_user_and_cookie($data=NULL,$redirect_invalid=TRUE)
	{
		if($data == NULL)
		{
			$data=$this->session_data;
		}

		if($this->CI->public_model->verify_user(['uid'=>$data['uid'], 'email' => $data['email'] ]))
		{
			if($this->CI->public_model->verify_cookie($data))
			{
				return TRUE;
			}
		}
		// redirect invalid user or cookie 
		if($redirect_invalid)
		{
			redirect('accounts/logout?msg=can\'t verify user! Login Again.');
		}
			return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * User login history
	 * Returns user login histroy from history table
	 * 
	 * @return array 
	 */
	public function history() 
	{
		$this->CI->load->model('accounts_model');
		return $this->CI->accounts_model->get_history($this->session_data['uid']);
	}

	// --------------------------------------------------------------------

	/**
	 * Default user group
	 *
	 * can also implemented by calling $this->CI->public_model->default_user_group(user id)
	 *  
	 * @return string  default high prio groupname from userpermission.permissionid
	 */
	public function default_user_group()
	{
		return $this->CI->public_model->user_groups($this->session_data['uid'])[0];
	}
}
