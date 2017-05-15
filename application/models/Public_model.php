<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Public_model extends CI_Model {

	private $tables = array(
			'login' => 'login',
			'userpermission' => 'userpermission',
			'permission' => 'permission',
			'setup' => 'setup',
			'history' => 'history'
		);


	function __construct() {
	
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Default User Group
	 *
	 *  returns default usergroup 
	 * @param  int  $uid 	user id to find user group of highest prio
	 * 
	 * @return string      user group with high prio (low value in userpermission.prio column)
	 */
	public function default_user_group($uid)
	{ 

		 return $this->user_groups($uid)[0];

	}

	// --------------------------------------------------------------------

	/**
	 * User Groups
	 *
	 * This returns either false if no data found or an array with permission.groupname 
	 * corresponds to a specific  user.
	 * 	example: 
	 * 		amin,principal
	 * @param  int $uid  	should supply user id
	 *   
	 * @return array 
	 */
	public function user_groups($uid=NULL)
	{ 
		if(!is_numeric($uid))
			return false;

		$query='select groupname from '.$this->tables['permission'].' WHERE  permissionid in 
						(SELECT permissionid FROM '.$this->tables['userpermission'].' WHERE uid ='.$uid.' ) order by prio  ';
		$result=$this->db->query($query);

		if($result->num_rows()>0)
		{ 
			return array_column($result->result_array(),'groupname');
		}

		return false;
	}

	// --------------------------------------------------------------------

	/**
	 * Verify User
	 * 
	 * Verify user by checking uid and email matchs. Returns true if a unique row
	 * was found correspondto email and uid. 
	 * 
	 * @param  array $where 	email and uid should be input via array.
	 * 
	 * @return bool
	 */
	public function verify_user($where) 
	{ 
		// if email and uid not found in array return 
		if (!isset($where['uid'], $where['email'])) 
		{	
			return false;
		}

		// for security do not select password and salt fields in login table
		$this->db->select("uid,email");
		$verifyUserQuery = $this->db->get_where($this->tables['login'], $where);
  
		if ($verifyUserQuery->num_rows() === 1) 
		{ 
			return true;
		}
		
		return false;
	}

	// --------------------------------------------------------------------

	/**
	 * Verify verify_cookie
	 *
	 * check history table to check if there exist atlease 1 row which contains ip,cookieid,uid
	 * which is same as data passed.
	 * @param  array  $data   
	 * 
	 * @return bool 
	 */
	public function verify_cookie($data = NULL) 
	{
		
		if($data == NULL)
		{
			return false;
		}

		// check if uid, email, cookie_id, ip_address , if not return false
		if (!isset($data['uid'], $data['cookie_id'], $data['ip_address'])) 
		{	
			return false;
		} 
	   

		// where condition 
		$data_from_cookie = array(
			'cookieid' => $data['cookie_id'],
			'uid' => $data['uid'],
			'ip' => $data['ip_address']
		);

		$verifySessionQuery = $this->db->get_where($this->tables['history'], $data_from_cookie);
		
		if ($verifySessionQuery->num_rows() > 0) 
		{	
			return true;
		} 

		return false;
	}

	// --------------------------------------------------------------------

	/**
	 * Set up  data
	 * 
	 *  This returns the data from setup table . These are usally filled at time of 
	 *  installing the application 
	 *  
	 * @return array  single row of setup table 
	 */
	public function setup_data()
	{
		return $this->db->get_where($this->tables['setup'])->result_array()[0];
	}
 
}
