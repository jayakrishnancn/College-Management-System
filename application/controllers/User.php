<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	private $uid = NULL;
	private $sessionData = [];


	private function findCookie() {

		if ($this->session->userdata('uid')) {
			$this->sessionData = $this->session->userdata();
			
			return true;
		}
		
		return false;
	}


	function __construct() {

		parent::__construct();
		$this->load->library('session');

		if (!$this->findCookie()) {
			redirect('accounts?msg=Login again', 'refresh');
			die;
		}

		$this->load->model(['accountsModel','publicModel']);
		$cookiedataforverification = array(
			'ipAddress' => $this->input->ip_address() ,
			'cookieid' => $this->sessionData['cookieid'],
			'uid' => $this->sessionData['uid']
		);

		if ($this->accountsModel->verifyUserSessionAndIp($cookiedataforverification) != true) {
			redirect('accounts?msg=can\'t verify user!. Login Again', 'refresh');
			die;
		}  

	}


	public function index() {
		$msg="";
		if($msg = $this->input->get('msg'))
		{
			$msg = "?msg=".$msg;	
		}
		redirect($this->publicModel->userDefaultGroup($this->sessionData['uid']).$msg,'refresh');

	}
}
