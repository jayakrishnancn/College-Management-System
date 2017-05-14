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


	public function userDefaultGroup($uid)
	{ 

		$query='select groupname from '.$this->tables['permission'].' WHERE prio = 
						(SELECT min(prio) from '.$this->tables['permission'].'  WHERE permissionid in 
						(SELECT permissionid FROM '.$this->tables['userpermission'].' WHERE uid ='.$uid.') )';
		$result=$this->db->query($query);

		if($result->num_rows()==1)
		{

			return $result->result_array()[0]['groupname'];
		}

		return false;
	}

	public function userGroups($uid=NULL)
	{ 
		if(!is_numeric($uid))
			return false;

		$query='select groupname from '.$this->tables['permission'].' WHERE  permissionid in 
						(SELECT permissionid FROM '.$this->tables['userpermission'].' WHERE uid ='.$uid.' )';
		$result=$this->db->query($query);

		if($result->num_rows()>0)
		{ 
			return array_column($result->result_array(),'groupname');
		}

		return false;
	}

	public function verify_user($where) 
	{ 
		if (!isset($where['uid'], $where['email'])) 
		{	
			return false;
		}
		
		$verifyUserQuery = $this->db->get_where($this->tables['login'], $where);
  
		if ($verifyUserQuery->num_rows() === 1) 
		{
			
			return true;
		}
		
		return false;
	}
	/**
	 * Verify verify_cookie
	 *
	 * check history table to check if there exist atlease 1 row which contains ip,cookieid,uid
	 * which is same as data passed.
	 * @param  array  $data   
	 * 
	 * @return bool 
	 */
	public function verify_cookie($data=[]) 
	{
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
	public function setup_data()
	{
		return $this->db->get_where($this->tables['setup'])->result_array()[0];
	}
 
}
