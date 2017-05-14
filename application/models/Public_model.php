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
 
}
