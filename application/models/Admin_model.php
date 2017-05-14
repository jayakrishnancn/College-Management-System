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

	private function random($length = 10) {
		
		return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijkl_mnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_', ceil($length / strlen($x)))) , 1, $length);

	}


	private function hashPassword($data, $salt = NULL) {

		if ($salt == NULL) {
			$salt = $this->random(12);
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

		$password=$this->hashPassword($input['password']);

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
	public function getUser($where=NULL)
	{
		if($where==NULL){
			return $this->db->get_where($this->tables['login'])->result_array();
		}
		$query=$this->db->get_where($this->tables['login'],$where);
			if($query->num_rows()==1)
				return $query->result_array()[0];
		return false;
	}
	public function get_usergroup($uid)
	{
			$array=$this->db->query('SELECT groupname FROM permission WHERE permissionid IN ( SELECT permissionid from userpermission WHERE userpermission.uid = ' .  $uid.  ' )')->result_array();
			return array_column($array,'groupname');
			
	}
	public function get_user_with_access($uid=NULL,$comaseperated=true)
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
			if($comaseperated===true)
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
		 
		if(isset($data['deletekey']) && $data['deletekey'] =='true' )
		{
			if($result->num_rows()<=0)
			{
				return false;
			}
			return $this->db->delete($this->tables['userpermission'], [
				 'uid'=>$data['uid'], 'permissionid'=>$data['permissionid']   
				 ]);
			return false;
		}

		if($result->num_rows()>0)
			return false;
		return $this->db->insert($this->tables['userpermission'], [
		 'uid'=>$data['uid'], 'permissionid'=>$data['permissionid']   
		 ] );
	}
	public function resetpassword($email)
	{
		if(strlen($email)<3)
			return false;

		$password = $this->hashPassword($email);

		return $this->db->update(
				$this->tables['login'], [ 
					'password'=>$password[0],
					'salt'=>$password[1]
					],['email'=>$email]);
	}
	public function deleteuser($email)
	{

		$this->db->trans_start();
		$query=$this->db->get_where($this->tables['login'],['email'=>$email]);
		if($query->num_rows()!=1)
			return false;
		$uid=$query->result_array()[0]['uid'];
		
		$this->db->delete($this->tables['login'],['email'=>$email]);

		$this->db->delete($this->tables['history'],['uid'=>$uid]);
		$this->db->delete($this->tables['userpermission'],['uid'=>$uid]);

		 $this->db->trans_complete();

		 return $this->db->trans_status(); 
	}
	public function updateuserdetails($value,$where)
	{
		return $this->db->update($this->tables['login'],$value,$where);
	}
}
