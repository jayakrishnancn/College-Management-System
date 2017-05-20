<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** 
 * Common for all models
 */
class MY_Model extends CI_Model {

	protected $tables = array(
			'login' => 'login',
			'userpermission' => 'userpermission',
			'permission' => 'permission',
			'setup' => 'setup',
			'history' => 'history'
		);


	function __construct() 
	{ 
		parent::__construct();
		$this->load->database(); 
	}

	protected function _random($length = 10) 
	{ 
		return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijkl_mnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_', ceil($length / strlen($x)))) , 1, $length); 
	}


	protected function _hash_password($data, $salt = NULL) 
	{ 
		if ($salt == NULL) {
			$salt = $this->_random(12);
		}
		
		return [sha1($salt . $data) , $salt];
	}

}
