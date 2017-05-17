<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 *  Teacher Model
 *
 *  This model is used for teacher,principal,hod,satff_adv Controllers etc
 * @category	Model
 * @author	jayakrishnancn
 * @link		https://github.com/jayakrishnancn/College-Management-System
 */ 
class Teacher_model extends CI_Model {

	/**
	 * Teacher model Constructor
	 */
	function __construct() 
	{ 
		parent::__construct();
		$this->load->database(); 
	}

// --------------------------------------------------------------------

	/**
	 * User Groups
	 *
	 * This returns either false if no data found or an array with permission.groupname 
	 * corresponds to a specific  user.
	 * 	example: 
	 * 		amin,principal
	 * @param  int $uid  	should supply user id
	 *   
	 * @return array 
	 */
	public function user_groups($uid=NULL)
	{ 
		if(!is_numeric($uid))
			return false;

		$query='select groupname from permission WHERE  permissionid in 
						(SELECT permissionid FROM userpermission WHERE uid ='.$uid.' ) order by prio  ';
		$result=$this->db->query($query);

		if($result->num_rows()>0)
		{ 
			return array_column($result->result_array(),'groupname');
		}

		return false;
	}

	/* ***************************************************
	 *   Course details
	/****************************************************
	/****************************************************
	 */

	// --------------------------------------------------------------------
	
	/**
	 * Add Course 
	 * @param  string $course_name name of course to insert
	 * 
	 * @return bool  true if successfuly inserted
	 */
	public function add_course($course_name = false)
	{
		if($course_name)
		{
			return $this->db->insert('course',['course_name'=>$course_name]);
		}
		return false;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get Course 
	 * 
	 * @return array 	return course name in course table
	 */
	public function get_course($all_fields = false)
	{ 
		if(!$all_fields)
		{
			$this->db->select('course_name');		
		}
		return $this->db->get('course')->result_array();
	}
	// --------------------------------------------------------------------
	
	/**
	 * Delete Course 
	 *
	 * delete course from course table
	 * @return bool if success
	 */
	public function delete_course($course_name = false)
	{ 
		if(!$course_name)
		{
			return false;
		}
		return $this->db->delete('course',['course_name' => $course_name]);
	}


	/**
	 *   Department details
	 */
	
	// --------------------------------------------------------------------
	
	/**
	 * Add Department Details 
	 * @param  string $department_name name of department to insert
	 * 
	 * @return bool  true if successfuly inserted
	 */
	public function add_department($department_name = false,$hod_name= false)
	{
		if($department_name && $hod_name)
		{
			$hod_check = $this->db->get_where('login',['email' => $hod_name]);
			if($hod_check->num_rows() !=1 )
			{
				return FALSE;
			}
			$hod_uid = $hod_check->result_array()[0]['uid'];

			$permissionid_hod = $this->db->get_where('permission',['groupname'=>'hod'])->result_array()[0]['permissionid'];

			$grps = $this->user_groups($hod_uid);
			
			if (!in_array('hod', $grps)) 
			{ 
				$this->db->insert('userpermission',['uid' => $hod_uid, 'permissionid' =>$permissionid_hod ]);
			}
			return $this->db->insert('department',['department_name'=>$department_name,'hod'=>$hod_name]);
		}
		return FALSE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get Department Details 
	 * 
	 * @return array 	return department name in department table
	 */
	public function get_department($all_fields = false)
	{ 
		if(!$all_fields)
		{
			$this->db->select('department_name,hod');		
		}
		return $this->db->get('department')->result_array();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Delete Department Details 
	 *
	 * delete department from department table
	 * @return bool if success
	 */
	public function delete_department($department_name = false)
	{ 
		if(!$department_name)
		{
			return false;
		}

		$this->db->trans_start();

		//delete hod permission
		if($this->db->query("select * from department WHERE hod =( SELECT hod from department WHERE department_name = '" . $department_name. "' )")->num_rows() <= 1)
		{

		$this->db->query(' delete from userpermission where uid = (SELECT uid from login WHERE email = (SELECT hod from department WHERE department_name ="' . $department_name . '") ) and permissionid = (SELECT permissionid from permission WHERE groupname = "hod") '); 
		}

		// delete dept
		$this->db->delete('department',['department_name' => $department_name]);
 
		 $this->db->trans_complete(); 

		 return $this->db->trans_status();  
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get User 
	 *
	 * return  user details
	 * @return array  all user details form login table except pass and salt
	 */
	public function get_user($where = false)
	{
		$this->db->select("uid,email");
		if(!$where)
		{
			return $this->db->get_where('login')->result_array();
		}
		$query=$this->db->get_where('login',$where);
		if($query->num_rows() == 1)
		{
			return $query->result_array()[0];
		}

		return false;
	}

	// --------------------------------------------------------------------

	/**
	 * Add Subject
	 * 
	 * by H.O.D 
	 */
	public function add_subject($subject = false,$department_id = false)
	{
		if(!$subject || (!$department_id))
		{
			return FALSE;
		}
		if($this->db->get_where('subject',['subject_name'=>$subject, 'department_id' => $department_id])->num_rows() > 0)
		{
			return false;
		}
		return $this->db->insert('subject',['subject_name'=>$subject, 'department_id' => $department_id]);
	}

	// --------------------------------------------------------------------
	/**
	 * Return dept row of corresponding uid
	 *
	 * Assumed that 1 can not be H.O.D of more than 1 department
	 * @param  int  	uid user id to check department
	 * @return array      1 dept row 
	 */
	public function get_dept($uid)
	{
		// since department.hod is email not uid we have to get email from 
		// login first cooresponding to uid
		$query = $this->db->get_where('login',['uid'=>$uid]);
		if($query->num_rows()!=1)
		{
			return FALSE;
		}
		$email = $query->result_array()[0]['email'];

		$query_dept = $this->db->get_where('department',['hod'=>$email]);
		if($query_dept->num_rows() != 1)
		{
			return FALSE;
		}
		return $query_dept->result_array()[0];

	}

	/**
	 * Get Subject
	 * 
	 * Return subjects in subject table
	 * @return array subject table row(s)
	 */
	public function get_subject()
	{	 

		 return $this->db->query("SELECT department_name as 'Department Name',subject_name as 'Subject Name' FROM subject, department WHERE department.id =subject.department_id")->result_array();
	}
}
