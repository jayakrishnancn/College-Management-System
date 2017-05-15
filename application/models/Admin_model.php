<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

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

	private function _random($length = 10) {
		
		return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijkl_mnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_', ceil($length / strlen($x)))) , 1, $length);

	}


	private function _hashPassword($data, $salt = NULL) {

		if ($salt == NULL) {
			$salt = $this->_random(12);
		}
		
		return [sha1($salt . $data) , $salt];
	}

 
	public function get_permissions()
	{

		return $this->db->get($this->tables['permission'])->result_array();
	}

	public function add_user($input)
	{
		if( ! isset($input['username'], $input['password'], $input['permissionid']))
		{
			return false;
		}

		$password=$this->_hashPassword($input['password']);

		$this->db->trans_start();

		 $this->db->insert($this->tables['login'],[
					'email' => $input['username'],
					'password' => $password[0],
					'salt' => $password[1]
				]);

		 $uid=$this->db->insert_id();

		 $this->db->insert($this->tables['userpermission'],['uid' =>$uid,'permissionid' =>$input['permissionid']]);

		 $this->db->trans_complete(); 

		 return $this->db->trans_status(); 
	}
	public function get_user($where=NULL)
	{
		$this->db->select("uid,email");
		if($where==NULL)
		{
			return $this->db->get_where($this->tables['login'])->result_array();
		}
		$query=$this->db->get_where($this->tables['login'],$where);
			if($query->num_rows()==1)
				return $query->result_array()[0];
		return false;
	}
	 
	/**
	 * get user with access 
	 * @param  int  $uid           user id
	 * @param  boolean $coma_seperated if true return a sting with comaseperated list 
	 *                                	of  access group name
	 * @return [type]                 [description]
	 */
	public function get_user_with_access($uid=NULL,$coma_seperated=true)
	{
		if($uid==NULL)
		{
			$where=[];
		}
		else{
			if(!is_array($uid) )
			{
				$where= array('uid'=>$uid);
			}
			else
			{
				$where=$uid;
			}
		}

		$query=$this->db->get_where($this->tables['login'],$where);

		$result = $query->result_array();
		foreach ($result as $key => &$value) {
			$x=$this->db->query('SELECT groupname FROM permission WHERE permissionid IN ( SELECT permissionid from userpermission WHERE userpermission.uid = ' .  $value['uid'] .  ' )')->result_array();
			if($coma_seperated===true)
			{
				$value['access']= implode(",", array_column($x,'groupname')) ;
			}
			else{
				$value['access']=array_column($x,'groupname');
			}
		}
		if($query->num_rows()==1)
			return $result[0];
		return $result;
	}
	public function add_user_permission($data)
	{

		$result=$this->db->get_where($this->tables['userpermission'], [
		 'uid'=>$data['uid'], 'permissionid'=>$data['permissionid']   
		 ] );
		 
		// there should nt be any rows
		if($result->num_rows()>0)
			return false;
		
		return $this->db->insert($this->tables['userpermission'], [
		 'uid'=>$data['uid'], 'permissionid'=>$data['permissionid']   
		 ] );
	}
	public function delete_user_permission($data)
	{ 

		$result=$this->db->get_where($this->tables['userpermission'], [
		 'uid'=>$data['uid'], 'permissionid'=>$data['permissionid']   
		 ] );

		// there should be only 1 row with same user and permission
		if($result->num_rows() != 1)
		{
			return false;
		}

		return $this->db->delete($this->tables['userpermission'], [
			 'uid'=>$data['uid'], 'permissionid'=>$data['permissionid']   
			 ]); 
	}
	public function reset_password($email,$newpassword = NULL)
	{
		if(strlen($email)<3)
			return false;

		if($newpassword == NULL)
		{
			$newpassword = $email;
		}

		$password = $this->_hashPassword($newpassword);
		 
		$this->db->trans_start();
		
		$query=$this->db->get_where($this->tables['login'],['email'=>$email]);
		
		if($query->num_rows()!=1)
		{
			return false;
		}
		$uid=$query->result_array()[0]['uid'];
		
		$this->db->update(
				$this->tables['login'], [ 
					'password'=>$password[0],
					'salt'=>$password[1]
					],['email'=>$email]);

		$this->db->delete($this->tables['history'],['uid'=>$uid]);

		 $this->db->trans_complete();

		 return $this->db->trans_status(); 
	}
	public function delete_user($email)
	{

		$this->db->trans_start();
		$query=$this->db->get_where($this->tables['login'],['email'=>$email]);
		if($query->num_rows()!=1)
		{
			return false;
		}
		$uid=$query->result_array()[0]['uid'];
		
		$this->db->delete($this->tables['login'],['email'=>$email]);

		$this->db->delete($this->tables['history'],['uid'=>$uid]);
		$this->db->delete($this->tables['userpermission'],['uid'=>$uid]);

		 $this->db->trans_complete();

		 return $this->db->trans_status(); 
	}
	public function update_user_details($value,$where)
	{
		return $this->db->update($this->tables['login'],$value,$where);
	}
	
}
