<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Principal extends CI_Controller {
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
        $this->load->model(['accountsModel', 'publicModel']);
        $cookiedataforverification = array(
            'ipAddress' => $this->input->ip_address() ,
            'cookieid' => $this->sessionData['cookieid'],
            'uid' => $this->sessionData['uid']
        );
        if ($this->accountsModel->verifyUserSessionAndIp($cookiedataforverification) != true) {
            redirect('accounts?msg=can\'t verify user!. Login Again', 'refresh');
            die;
        }
        $this->load->library('common_functions');
        $this->common_functions->verifypermission('principal');
    }
 
    public function index() {
    }
}
