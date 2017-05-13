<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {
    function __construct() {
        parent::__construct();
        /**
         *  publicModel     :   model  for  general  use.
         *  accountModel  :       model for account related activities .
         *     includes  login, getPermissions, addUser,        getUser,
         *     getUserWithAccess, addUserPermission, resetpassword,
         *     deleteuser, updateuserdetails.
         */
        $this->load->model(['publicModel', 'accountsModel']);
    }
    /**
     * for rendering public pages inside a bootstrap view for consistency
     *
     * @param  [string]  $page       [page to render inside]
     * @param  array   $data       [data to pass to view loader]
     * @param  boolean $defaultdir [to use default directory ie inside
     *                                 use this false to view from other directories
     *                                 other than view/public ]
     */
    private function renderpublic($page, $data = [], $defaultdir = true) {
        $pagedata['data'] = $data;
        if ($defaultdir == true) {
            $page = 'public/' . $page;
        }
        $pagedata['page'] = $page;
        $pagedata['data']['setup'] = $this->publicModel->setupData();
        $this->load->view('public/bootstrap', $pagedata);
    }
    public function index($value = '') {
        $this->login();
    }
    public function login() {
        
        $this->load->library('session');
        if ($this->session->userdata('uid')) {
            redirect('user', 'refresh');
            
            return;
        }
        if ($this->input->post('username') && $this->input->post('password')) {
            $input = $this->input->post();
            if ($this->accountsModel->login(['username' => $input['username'], 'password' => $input['password']])) {
                $this->load->library('session');
                $this->session->set_userdata($this->accountsModel->userLoginDetails);
                redirect('user?msg=Login successful', 'refresh');
                
                return;
            } else {
                redirect('accounts/login?msg=error ! check username and password', 'refresh');
                
                return;
            }
            
            return;
        }
        $this->load->helper('form');
        $data['title'] = 'Login';
        $data['loginAction'] = 'accounts/login';
        $this->renderpublic('public/login.php', $data, false);
    }
    public function signup() {
        if ($this->input->post('username') && $this->input->post('password')) {
            $input = $this->input->post();
            if ($this->accountsModel->signup(['username' => $input['username'], 'password' => $input['password']])) {
                redirect('accounts/login?msg=Account created login to continue', 'refresh');
            } else {
                redirect('accounts/signup?msg=error', 'refresh');
            }
            exit;
        }
        $this->load->helper('form');
        $data['title'] = 'Signup';
        $data['signupAction'] = 'accounts/signup';
        $this->renderpublic('public/signup.php', $data, false);
    }
    public function logout() {
        $this->load->library('session');
        $user_data = $this->session->all_userdata();
        
        foreach ($user_data as $key => $value) {
            if ($key != 'session_id' && $key != 'ip_address' && $key != 'user_agent' && $key != 'last_activity') {
                $this->session->unset_userdata($key);
            }
        }
        $this->session->sess_destroy();
        $prevmsg = (($this->input->get('msg') == FALSE) ? FALSE : $this->input->get('msg'));
        $msg = ($prevmsg == FALSE) ? "msg=Logout successful" : "msg=" . $prevmsg;
        redirect("accounts/login?" . $msg, "refresh");
    }
    
}
