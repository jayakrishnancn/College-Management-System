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
	 * Add Course 
	 * @param  string $course_name name of course to insert
	 * 
	 * @return bool  true if successfuly inserted
	 */
	public function add_course($course_name = false)
	{
		if($course_name)
		{
			return $this->db->insert('courses',['course_name'=>$course_name]);
		}
		return false;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get Course 
	 * 
	 * @return array 	return courses name in course table
	 */
	public function get_course($all_fields = false)
	{ 
		if(!$all_fields)
		{
			$this->db->select('course_name');		
		}
		return $this->db->get('courses')->result_array();
	}
	// --------------------------------------------------------------------
	
	/**
	 * Delete Course 
	 *
	 * delete course from courses table
	 * @return bool if success
	 */
	public function delete_course($course_name = false)
	{ 
		if(!$course_name)
		{
			return false;
		}
		return $this->db->delete('courses',['course_name' => $course_name]);
	}

}
