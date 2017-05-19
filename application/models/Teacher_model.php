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
class Teacher_model extends MY_Model {

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

// --------------------------------------------------------------------

	/**
	 * Get permission
	 *
	 * This returns array of permission table
	 * @param  int/string $groupname_or_id should be supplied to function 
	 * @param  bool  	$prio if column is prio or not in permission table
	 *   
	 * @return array 
	 */
	public function get_permission($groupname_or_id = FALSE, $prio = FALSE)
	{
		if(!$groupname_or_id)
		{
			return FALSE;
		}
  
		$column_name = ($prio == TRUE)?'prio' : is_numeric($groupname_or_id)? 'permissionid' : 'groupname';	

		$q = $this->db->get_where('permission',[$column_name => $groupname_or_id]);

		if($q->num_rows()==1)
		{
			return $q->result_array()[0];			
		}
		return FALSE;
	
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
	 * Change H.O.D 
	 *
	 * change hod of department .Set permission to new hod, revoke permission for old hod.
	 * @param  string  $dept   department name
	 * @param  string/int  $hod_id hod_id
	 * @param  boolean $id     if hod_id is id or hod_name
	 * 
	 * @return bool
	 */
	public function change_hod($dept,$hod_id,$id = TRUE)
	{
		// continue only if 1 user selected correspoinding to hod_id
		if(!$new_hod_details = $this->get_user(['uid' => $hod_id]))
		{
			return FALSE;
		} 
		$old_hod = $this->get_department(['department_name' => $dept])[0]['hod'];
		
		// continue only if 1 user selected correspoinding to hod_id
		if(!$old_hod_details = $this->get_user(['email' => $old_hod]))
		{ 
			return FALSE;
		} 

		$groupname = $this->user_groups($hod_id);
		$hod_permission_id = $this->get_permission('hod')['permissionid'];
		
		// we have to change hod only if new user is not hod  
		if((!$groupname) || (!in_array('hod',$groupname)))
		{ 	
			$this->db->trans_start();

			$this->db->update('userpermission',['uid' => $hod_id],['uid' => $old_hod_details['uid'], 'permissionid' => $hod_permission_id]);
			$this->db->update('department',['hod' => $new_hod_details['email']],['department_name' => $dept]);

			 $this->db->trans_complete(); 
			 return $this->db->trans_status();  
		}

	 	return FALSE;
 
	}
	// --------------------------------------------------------------------
	
	/**
	 * Get Department Details 
	 * 
	 * @return array 	return department name in department table
	 */
	public function get_department($where = false, $all_fields = FALSE)
	{ 
	

		if(!$all_fields)
		{
			$this->db->select('department_name,hod');		
		}
		else{
			$this->db->select($all_fields);		
		}

		if($where)
		{ 
			return $this->db->get_where('department',$where)->result_array();
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
	public function delete_department($department_name = FALSE)
	{ 
		if(!$department_name)
		{
			return FALSE;
		}

		$this->db->trans_start();

		//delete hod permission
		if($this->db->query("select * from department WHERE hod =( SELECT hod from department WHERE department_name = '" . $department_name. "' )")->num_rows() <= 1)
		{

		$this->db->query(' delete from userpermission where uid = (SELECT uid from login WHERE email = (SELECT hod from department WHERE department_name ="' . $department_name . '") ) and permissionid = (SELECT permissionid from permission WHERE groupname = "hod") '); 
		}
		if(!$department_id = $this->get_department(['department_name' => $department_name],'id'))
		{
			return FALSE;
		}

		// delete dept
		$this->db->delete('department',['department_name' => $department_name]);
		$this->db->delete('subject',['department_id' => $department_id[0]['id']]);
 
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
		$this->db->select("uid,email,name");
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
	 * get user not hod
	 * Ger User who are not hod's
	 * @param  array $where [description]
	 * 
	 * @return array  	of users who are not hod's
	 */
	public function get_user_but_not_hod($where = false)
	{

		return $this->db->query("select uid,email,name from login where email not in (SELECT hod from department) ")->result_array();
	}
	// --------------------------------------------------------------------

	/**
	 * Add Subject
	 * 
	 * by H.O.D 
	 */
	public function add_subject($subject = FALSE, $course_name = FALSE, $department_id = FALSE)
	{
		if(!($subject && $department_id && $course_name))
		{
			return FALSE;
		}

		if($this->db->get_where('subject',['subject_name'=>$subject, 'course_name' => $course_name,'department_id' => $department_id])->num_rows() > 0)
		{
			return FALSE;
		}
		return $this->db->insert('subject',['subject_name'=>$subject, 'course_name' => $course_name, 'department_id' => $department_id]);
	}

	// --------------------------------------------------------------------
	/**
	 * Return dept row of corresponding uid
	 *
	 * Assumed that 1 can not be H.O.D of more than 1 department
	 * @param  int  	uid user id to check department
	 * @return array      1 dept row 
	 */
	public function get_dept_by_uid($uid)
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

		return $this->db->query("SELECT subject_name as 'Subject Name', department_name as 'Department Name',course_name as 'Course Name' FROM subject,  department WHERE department.id =subject.department_id")->result_array(); 
	}

	//----------------------------------------------------------
	
	public function add_new_user($data)
	{
		if(!isset($data['email'],$data['department'],$data['username']))
		{
			return FALSE;
		}
		$pass = $this->_hashPassword($data['email']);
		$values = array(
			'email' => $data['email'] ,
			'name' => $data['username'],
			'password' =>$pass[0],
			'salt' => $pass[1]
			);
		
		$q = $this->db->get_where('login',['email' => $data['email']]);

		if($q->num_rows() > 0)
		{
			return FALSE;
		}
		$this->db->trans_start();
			$this->db->insert('login',$values);
			$this->db->insert('userpermission',['uid' =>$this->db->insert_id(), 'permissionid' => $this->get_permission('teacher')['permissionid'] ]);
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}
