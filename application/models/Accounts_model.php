<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts_model extends MY_Model {
 
	function __construct() 
	{
		parent::__construct(); 
	} 

	public function login($data) {
		$q = $this->db->get_where($this->tables['login'], ['email' => $data['username']]);
		if ($q->num_rows() == 1) {
			$salt = $q->result_array() [0]['salt'];
			$hashed_password = $this->_hash_password($data['password'], $salt) [0];
    
			$q2 = $this->db->get_where($this->tables['login'], ['email' => $data['username'], 'password' => $hashed_password]);
			if ($q2->num_rows() == 1) {
				$this->load->library('user_agent');
				$result2 = $q->result_array() [0];
				$browser = $this->agent->browser();
				$ip = $this->input->ip_address();
				$platform = $this->agent->platform();
				$cookie_id = $this->_random(12);
				$this->db->insert($this->tables['history'], ['uid' => $result2['uid'], 'browser' => $browser, 'ip' => $ip, 'os' => $platform, 'cookieid' => $cookie_id, 'dateandtime' => date("Y-m-d h:i:sa") ]);


				$queryfordelhistory=$this->db->query("select count(*) from ".$this->tables['history']." where uid=".$result2['uid']." ");
				if($queryfordelhistory->num_rows()==1){
					if($queryfordelhistory->result_array()[0]['count(*)'] >30 )
						$this->db->query("delete from " . $this->tables['history']. " where uid = "  . $result2['uid'].  " ORDER BY dateandtime ASC LIMIT 1  " );
			
				}
 
				return array(
					'uid' => $result2['uid'],
					'email' => $result2['email'],
					'cookie_id' => $cookie_id,
					'ip_address' =>$ip
				);
			} else {
				
				return false;
			} // else of if ($q2->num_rows() == 1)
			
		} //($q->num_rows() == 1)
		
		return false;
	}
	public function signup($data) {
		if ($this->db->get_where($this->tables['login'], ['email' => $data['username']])->num_rows() > 0) {
			
			return false;
		}
		$tmpPass = $this->_hash_password($data['password']);
		$hashed_password = $tmpPass[0];
		$salt = $tmpPass[1];
		
		return $this->db->insert($this->tables['login'], ['email' => $data['username'], 'password' => $hashed_password, 'salt' => $salt]);
	}
	public function verify_user($where) 
	{
		$verifyUserQuery = $this->db->get_where($this->tables['login'], $where);

		if ($verifyUserQuery->num_rows() === 1) 
		{
			
			return true;
		}
		
		return false;
	}
	/**
	 * Verify user and cookie 
	 *
	 * This function Verify user by checking the login table
	 * if table login.uid and login.email is same as session data
	 * if user exist then check history to check if cookieid and ip address exist 
	 * return true if both user exist and atleast 1 row in history then return true   
	 * @param  array  $data   
	 * 
	 * @return bool 
	 */
	public function verify_user_and_cookie_id($data=[]) 
	{
		// check if uid, email, cookie_id, ip_address , if not return false
		if (!isset($data['uid'], $data['email'], $data['cookie_id'], $data['ip_address'])) 
		{	
			return false;
		}

		$verify_user_data = array('uid' => $data['uid'], 'email' => $data['email']);
	  
		// check if user exist
		if($this->verify_user($verify_user_data)!=true)
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

	public function get_history($uid)
	{
		$this->db->select('browser,ip,os,dateandtime');
		$this->db->order_by('dateandtime','desc');
		$query=$this->db->get_where($this->tables['history'],['uid'=>$uid]);
		if($query->num_rows()<=0)
			return false;
		return $query->result_array();
	}
	

	/**
	 * Change User password
	 * 
	 * @param  int $uid      user id to change passowrd
	 * @param  array $data should contain new_password, old_password, 
	 *                         confirm_password
	 * @return bool           true if password changed
	 */
	public function change_password($uid = NULL, $data = NULL)
	{
		if($uid == NULL || $data == NULL)
		{
			return FALSE;
		}
		if(!isset($data['new_password'],$data['confirm_password'],$data['old_password']))
		{
			return FALSE;
		}

		if($data['new_password'] != $data['confirm_password'])
		{
			return FALSE;
		}

		// for checking and getting salt form table
		$q = $this->db->get_where('login', ['uid' =>$uid]);
		if ($q->num_rows() != 1) 
		{
			return FALSE;
		}

		$login_query_result = $q->result_array()[0];
		$salt = $login_query_result['salt'];
		$password = $login_query_result['password'];

		$hashed_password = $this->_hash_password($data['old_password'], $salt)[0];

		if($hashed_password != $password)
		{
			return fALSE;
		} 

		// unnecessary  can be deleted
		$q2 = $this->db->get_where('login', ['uid' => $uid, 'password' => $hashed_password]);

		if($q2->num_rows() != 1)
		{
			return FALSE;
		}
		
		
		$new_password = $this->_hash_password($data['new_password']);

		return $this->db->update('login',['password' => $new_password[0], 'salt' => $new_password[1]],['uid' =>$uid, 'password' => $login_query_result['password']]);

	}
}
