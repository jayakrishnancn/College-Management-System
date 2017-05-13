<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AccountsModel extends CI_Model {
    private $tables = array(
        'login' => 'login',
        'userpermission' => 'userpermission',
        'history' => 'history'
    );
    public $userLoginDetails = NULL;
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
    public function login($data) {
        $q = $this->db->get_where($this->tables['login'], ['email' => $data['username']]);
        if ($q->num_rows() == 1) {
            $salt = $q->result_array() [0]['salt'];
            $hashedPassword = $this->hashPassword($data['password'], $salt) [0];
            $q2 = $this->db->get_where($this->tables['login'], ['email' => $data['username'], 'password' => $hashedPassword]);
            if ($q2->num_rows() == 1) {
                $this->load->library('user_agent');
                $result2 = $q->result_array() [0];
                $browser = $this->agent->browser();
                $ip = $this->input->ip_address();
                $platform = $this->agent->platform();
                $cookieid = $this->random(12);
                $this->db->insert($this->tables['history'], ['uid' => $result2['uid'], 'browser' => $browser, 'ip' => $ip, 'os' => $platform, 'cookieid' => $cookieid, 'dateandtime' => date("Y-m-d h:i:sa") ]);


                $queryfordelhistory=$this->db->query("select count(*) from ".$this->tables['history']." where uid=".$result2['uid']." ");
                if($queryfordelhistory->num_rows()==1){
                    if($queryfordelhistory->result_array()[0]['count(*)'] >30 )
                        $this->db->query("delete from " . $this->tables['history']. " where uid = "  . $result2['uid'].  " ORDER BY dateandtime ASC LIMIT 1  " );
            
                }



                $this->userLoginDetails = array(
                    'uid' => $result2['uid'],
                    'email' => $result2['email'],
                    'cookieid' => $cookieid,
                    'ipAddress' =>$ip
                );
                
                return true;
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
        $tmpPass = $this->hashPassword($data['password']);
        $hashedPassword = $tmpPass[0];
        $salt = $tmpPass[1];
        
        return $this->db->insert($this->tables['login'], ['email' => $data['username'], 'password' => $hashedPassword, 'salt' => $salt]);
    }
    public function verifyUser($data) {
        $where = array(
            'uid' => $data['uid']
        );
        $verifyUserQuery = $this->db->get_where($this->tables['login'], $where);
        if ($verifyUserQuery->num_rows() == 1) {
            
            return true;
        }
        
        return false;
    }
    public function verifyUserSessionAndIp($data) {
        if (!isset($data['uid'], $data['cookieid'], $data['ipAddress'])) {
            
            return false;
        }
        $where = array(
            'cookieid' => $data['cookieid'],
            'uid' => $data['uid'],
            'ip' => $data['ipAddress']
        );
        $verifySessionQuery = $this->db->get_where($this->tables['history'], $where);
        if ($verifySessionQuery->num_rows() > 0) {
            
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
}
