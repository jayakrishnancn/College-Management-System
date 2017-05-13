<?php
/**
 *  IceCup
 */

class common_functions {
    private $CI;
    function __construct() {
        $this->CI = & get_instance();
        $this->CI->load->library('session');
        $this->CI->load->model('publicModel');
    }
    public function verifypermission($value, $return = false) {
        $dbdata = $this->CI->publicModel->userGroups($this->CI->session->userdata('uid'));
        if (is_array($dbdata) && in_array($value, $dbdata)) {
            
            return true;
        }
        if ($return) {
            
            return false;
        }
        redirect('user?msg=you dont have the permission to view this page', 'refresh');
        die;
    }
    public function verifyip() {
        
        return $this->CI->input->ip_address() == $this->CI->session->userdata('ipAddress');
    }
    public function history() {
        $this->CI->load->model('accountsModel');
        
        return $this->CI->accountsModel->get_history($this->CI->session->userdata('uid'));
    }
}
