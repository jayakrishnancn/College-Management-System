<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    private $uid = NULL;
    private $sessionData = [];
    private $tables = array(
        'login' => 'login',
        'userpermission' => 'userpermission',
        'permission' => 'permission',
        'setup' => 'setup',
        'history' => 'history'
    );
    private function renderadmin($page, $data = [], $defaultdir = true) {
        $info['data'] = $data;
        if ($defaultdir == true) {
            $page = 'admin/' . $page;
        }
        $info['page'] = $page;
        $info['data']['setup'] = $this->publicModel->setupData();
        $info['data']['sessionData'] = $this->sessionData; 
        $info['userpermission']=$this->adminModel->getusergroup($this->sessionData['uid']);
        $this->load->view('admin/bootstrap', $info);
    }
    function __construct() {
        parent::__construct();
        $this->load->library(['session', 'common_functions']);
        if (!$this->session->userdata('uid')) {
            redirect('accounts?msg=Login again', 'refresh');
            die;
        }
        $this->sessionData = $this->session->userdata();
        $this->load->model(['accountsModel', 'publicModel']);
        $this->common_functions->verifyip();
        $cookiedataforverification = array(
            'ipAddress' => $this->input->ip_address() ,
            'cookieid' => $this->sessionData['cookieid'],
            'uid' => $this->sessionData['uid']
        );
        if ($this->accountsModel->verifyUserSessionAndIp($cookiedataforverification) != true) {
            redirect('accounts?msg=can\'t verify user!. Login Again', 'refresh');
            die;
        }
        $this->common_functions->verifypermission('admin');
        $this->load->model('adminModel');
    }
    public function index() {
        $this->renderadmin('home');
    }
    public function adduser() {
        if ($input = $this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]|is_unique[' . $this->tables['login'] . '.email]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]');
            $this->form_validation->set_rules('permissionid', 'permissions', 'trim|required|greater_than_equal_to[0]|numeric|is_natural');
            if ($this->form_validation->run() == FALSE) {
                redirect('admin/adduser?msg=Check all fields', 'refresh');
                
                return;
            }
            $userdata = array(
                'username' => $input['username'],
                'password' => $input['password'],
                'permissionid' => $input['permissionid']
            );
            if ($this->adminModel->addUser($userdata)) {
                redirect('admin/adduser?msg=User added', 'refresh');
                
                return;
            }
            die;
            redirect('admin/adduser?msg=Couldn\'t add User. Try again', 'refresh');
            
            return;
        }
        $this->load->helper('form');
        $this->load->library('FormBuilder');
        $this->formbuilder->startform(['action' => 'admin/adduser', 'heading' => 'Add User']);
        $this->formbuilder->addlabel('Username');
        $this->formbuilder->addinput(['name' => 'username', 'placeholder' => "Username", 'autofocus' => true]);
        $this->formbuilder->addlabel('Password');
        $this->formbuilder->addinput(['name' => 'password', 'placeholder' => "Password", 'type' => 'password']);
        $this->formbuilder->addlabel('Permission');
        $this->formbuilder->startdropdown('permissionid');
        
        foreach ($this->adminModel->getPermissions() as $key => $value) {
            $this->formbuilder->dropdownoption($value['groupname'], $value['permissionid']);
        }
        $this->formbuilder->enddropdown();
        $this->formbuilder->setbutton('Add user');
        $this->renderadmin('form_builder');
    }
    public function adddeleteuserpermissions() {
        if ($input = $this->input->post()) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('uid', 'user id', 'required');
            $this->form_validation->set_rules('permissionid', 'permissions', 'trim|required|greater_than_equal_to[0]|numeric|is_natural');
            $this->form_validation->set_rules('deletekey', 'deletekey', '');
            if ($this->form_validation->run() == FALSE) {
                redirect('admin/adddeleteuserpermissions?msg=Check all fields', 'refresh');
                
                return;
            }
            $userdata = array(
                'uid' => $input['uid'],
                'permissionid' => $input['permissionid'],
                'deletekey' => $input['deletekey']
            ); 
            if ($this->adminModel->addUserPermission($userdata)) {
                $process=($input['deletekey']=='true')?"deleted":"added";
                redirect('admin/adddeleteuserpermissions?msg=User permission '.$process, 'refresh');
                
                return;
            }
            redirect('admin/adddeleteuserpermissions?msg=Couldn\'t add permission. Likely to be Already done before ', 'refresh');
            
            return;
        }
        $this->load->helper('form');
        $this->load->library('FormBuilder');
        $heading='Add User Permission';
        if($this->input->get('delete')=='true')
        {
            $heading='Delete User Permission';
        }
        $this->formbuilder->startform(['action' => 'admin/adddeleteuserpermissions', 'heading' => $heading]);
        $this->formbuilder->addlabel('Username');
        $this->formbuilder->startdropdown('uid');
        
        foreach ($this->adminModel->getUser() as $key => $value) {
            $this->formbuilder->dropdownoption($value['email'], $value['uid']);
        }
        $this->formbuilder->enddropdown();
        $this->formbuilder->addlabel('Permission');
        $this->formbuilder->startdropdown('permissionid');
        
        foreach ($this->adminModel->getPermissions() as $key => $value) {
            $this->formbuilder->dropdownoption($value['groupname'], $value['permissionid']);
        }
        $this->formbuilder->enddropdown();




        if ($this->input->get('delete') == 'true') {
        $this->formbuilder->addinput('deletekey', 'hidden', false,'true');
        $this->formbuilder->setbutton('Delete permission');

        } else {
       $this->formbuilder->addinput('deletekey', 'hidden', false,'false');
        $this->formbuilder->setbutton('Add permission');
        }

        $this->renderadmin('form_builder');
    }
    public function manageusers() { 
        $data['table'] = $this->adminModel->getUserWithAccess();
        $this->renderadmin('table', $data);
    }
    public function history() { 
        $data['table'] =  $this->common_functions->history();
        $this->renderadmin('public/history', $data,false);
    }
    public function resetpassword() {
        if (($email = $this->input->get('email')) == false || ($this->input->get('email') == NULL)) {
            redirect('admin/manageusers?msg=Couldn\'t reset password.Empty Fields', 'refresh');
            
            return;
        }
        if ($this->adminModel->resetpassword($email)) {
            redirect('admin/manageusers?msg=Password Reset to email id ', 'refresh');
            
            return;
        }
        redirect('admin/manageusers?msg=Couldn\'t reset password', 'refresh');
    }
    public function deleteuser() {
        $this->load->helper(array(
            'form',
            'url'
        ));
        $this->load->library('form_validation');
        if ($this->input->get('emailid')) {
            $this->form_validation->set_data($_GET);
            $this->form_validation->set_rules('emailid', 'emailid', 'required');
            if ($this->form_validation->run() == FALSE) {
                redirect('admin/manageusers?msg=username not valid', 'refresh');
                
                return;
            }
            $email = $this->input->get('emailid');
            if ($this->adminModel->deleteuser($email)) {
                redirect('admin/manageusers?msg=user  account ( ' . $email . ' ) deleted', 'refresh');
                
                return;
            }
            redirect('admin/manageusers?msg=Cant delete user.Try again', 'refresh');
            
            return;
        }
        redirect('admin/manageusers?msg=username required', 'refresh');
        
        return;
    }
    public function edituser() {
        $this->load->library(['FormBuilder', 'form_validation']);
        if ($email = $this->input->post('email')) {
            $oldemail = $this->input->post('oldemail');
            $this->form_validation->set_rules('oldemail', 'oldemail', 'required|min_length[4]');
            $this->form_validation->set_rules('email', 'email', 'required|min_length[4]');
            if ($this->form_validation->run() == FALSE) {
                redirect('admin/edituser?msg=username not valid', 'refresh');
                
                return;
            }
            if ($this->adminModel->updateuserdetails(['email' => $email], ['email' => $oldemail])) {
                redirect('admin/edituser?msg=user details updated', 'refresh');
                
                return;
            }
            redirect('admin/edituser?msg=can\'t  update user details', 'refresh');
            
            return;
        } elseif ($email = $this->input->get('email')) {
            $userdata = $this->adminModel->getUser(['email' => $email]);
            $this->formbuilder->startform(['action' => 'admin/edituser', 'heading' => 'Change User Details (' . $email . ') ']);
            $this->formbuilder->addlabel('E-mail id');
            $this->formbuilder->addinput('email', 'text', true, $userdata['email']);
            $this->formbuilder->addinput('oldemail', 'hidden', true, $userdata['email']);
            $this->formbuilder->setbutton('Change');
            $this->renderadmin('form_builder');
            
            return;
        }
        redirect('admin/manageusers?msg=user not found', 'refresh');
        
        return;
    }
}
