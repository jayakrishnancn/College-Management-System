<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Principal Class
 * 
 * This class enables you to controll and manage principal login 
 * @category	Controller
 * @author	jayakrishnancn
 * @link		https://github.com/jayakrishnancn/College-Management-System
 */ 
class Principal extends MY_Controller { 
	/**
	 * Class Constructor
	 * @return  void
	 */
	function __construct() 
	{
		//  MY_Controller constructor will load common_functions library,
		//   verify session, user, cookie, ip etc. set session_data etc
		parent::__construct();

		// already do a check and verify user and cookie in parent class.
		// so only checkfor permission in this class 
 		// verify if user has permission to this principal controller (principal panel).
		$this->common_functions->verify_permission('principal');  

		// load teacher model
		$this->load->model('teacher_model');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Render Principal View
	 * 
	 * For loading a common principal bootstrap view .This is to improve consistency and reduce 
	 * redudent code. Use  $this->_render_principal_view() insted of $this->load->view() 
	 * 
	 * @param  string  $page 		relative path of php view file to render
	 * @param  array   $data 		To supply data to view 
	 * @param  boolean $default_directory 		pass a directory path to render. To render from 
	 *                                      view/ pass use $default_directory = false 
	 *                             
	 * @return void
	 */
	private function _render_principal_view($page, $data = array(), $default_directory = 'principal') 
	{ 
		$data['title'] = 'Principal';
		// load default principal/ bootstrap view  in application/view directory 
		$this->_render_view($page,$data,$default_directory);
	} 

	// --------------------------------------------------------------------
	
	/**
	 *  Principal Index 
	 *
	 * Default method for accounts controller 
	 * 
	 * @return void
	 */
	public function index() 
	{
		$this->_render_principal_view('home');
	}

	// --------------------------------------------------------------------
	/**
	 * Add Course
	 *
	 * This will add course in courses table
	 * 
	 * @return void
	 */
	public function add_course()
	{ 

		if($course_name = $this->input->post('course_name'))
		{
			$course_name = trim($course_name);

			if(strlen($course_name)>2)
			{
				if($this->teacher_model->add_course($course_name))
				{
					$this->session->set_flashdata('msg','Course created');
					redirect($this->current_url );
					return;
				}

				$this->session->set_flashdata('msg','Cant create course. Try again later');
				redirect($this->current_url);
				return;
			}

				$this->session->set_flashdata('msg','insufficient Inputs.Try again');
			redirect($this->current_url);
			return;
		}

		$this->load->library('form_builder');
		$this->form_builder->start_form(['action' => $this->current_url,'heading'=>'Add Course']);

		$this->form_builder->addlabel('Course Name');
		$this->form_builder->addinput(['name'=>'course_name','autofocus'=>true]);

		$this->_render_principal_view('public/form_builder', FALSE, FALSE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Add Course
	 *
	 * This will Display all courses
	 * 
	 * @return void
	 */
	public function view_courses()
	{ 

		// table data
		$data['table'] = $this->teacher_model->get_course();
		$data['table_title'] = 'Current Courses ';
 		foreach ($data['table'] as $key => &$value) {
 			$value['action'] = "<a href='". base_url($this->router->class."/delete_course")."?course_name=" . urlencode($value['course_name'] ). "' class='btn btn-default btn-sm confirmation'>Delete Course</a>";
 		}
 		
		$this->_render_principal_view('public/table',$data,false);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Delete Course
	 *
	 * This will delete course from courses table
	 * 
	 * @return void
	 */
	public function delete_course()
	{ 

		if($course_name = $this->input->get('course_name'))
		{
			$course_name = trim($course_name);

			if(strlen($course_name)>2)
			{
				if($this->teacher_model->delete_course($course_name))
				{

					$this->session->set_flashdata('msg','Course Deleted');
					redirect($this->router->class . '/view_courses');
					return;
				}

				$this->session->set_flashdata('msg','can\'t delete Course. Try Again');
				redirect($this->router->class . '/view_courses');
				return;
			}
			$this->session->set_flashdata('msg','insufficient Inputs. Try Again');
			redirect($this->router->class . '/view_courses');
			return;
		}

			$this->session->set_flashdata('msg','Invalid input. Try Again');
			redirect($this->router->class . '/view_courses');
			return;

	}

	// --------------------------------------------------------------------
	/**
	 * Add Department
	 *
	 * This will add course in courses table
	 * 
	 * @return void
	 */
	public function add_department()
	{ 

		if($inputs = $this->input->post())
		{
			 
			// validate the username and password
			$this->load->library('form_validation');
			
			// username and password : Required and check for min_length
			$this->form_validation->set_rules('department_name', 'Department Name', 'trim|required|min_length[4]'); 
			$this->form_validation->set_rules('hod_name', 'HOD name', 'trim|required|min_length[4]'); 

			// if the requirement are not meet redirect to signup page to re-enter the signup details 
			if ($this->form_validation->run() == FALSE)
			{ 

				$this->session->set_flashdata('msg','Insufficient Inputs. Try Again');
				redirect($this->current_url);
				return;
			}

			if($this->teacher_model->add_department($inputs['department_name'],$inputs['hod_name']))
			{
				$this->session->set_flashdata('msg','Department created');
				redirect($this->current_url);
				return;
			}

			$this->session->set_flashdata('msg','Cant create department. Try again');
			redirect($this->current_url);
			return;
			
		}

		$this->load->library('form_builder');
		$this->form_builder->start_form(['action' => $this->current_url,'heading'=>'Add Department']);

		$this->form_builder->addlabel('Department Name');
		$this->form_builder->addinput(['name'=>'department_name','autofocus'=>true]);
		
		$this->form_builder->addlabel('Department Hod');
  
		// dropdown starts
		$this->form_builder->startdropdown('hod_name');
		
		foreach ($this->teacher_model->get_user() as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['email']);
		}
		$this->form_builder->enddropdown();
		// dropdown ends 

		$this->_render_principal_view('public/form_builder', FALSE, FALSE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * View Departments
	 *
	 * This will Display all depeartments
	 * 
	 * @return void
	 */
	public function view_departments()
	{ 

		// table data
		$data['table'] = $this->teacher_model->get_department();
		$data['table_title'] = 'Current Department ';
 		foreach ($data['table'] as $key => &$value) {
 			$value['action'] = "<a href='". base_url($this->router->class."/delete_department")."?department_name=" . urlencode($value['department_name'] ). "' class='btn btn-default btn-sm confirmation'>Delete Department</a>";
 		}
 		
		$this->_render_principal_view('public/table',$data,false);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Delete Course
	 *
	 * This will delete course from courses table
	 * 
	 * @return void
	 */
	public function delete_department()
	{ 

		if($department_name = $this->input->get('department_name'))
		{
			$department_name = trim($department_name);

			if(strlen($department_name)>2)
			{
				if($this->teacher_model->delete_department($department_name))
				{

					$this->session->set_flashdata('msg','Department deleted');
					redirect($this->router->class . '/view_departments');
					return;
				} 

				$this->session->set_flashdata('msg','cant delete department. Try again');
				redirect($this->router->class . '/view_departments');
				return;
			}

			$this->session->set_flashdata('msg','Insufficient Inputs. Try again');
			redirect($this->router->class . '/view_departments');
			return;
		} 
				$this->session->set_flashdata('msg','Invalid inputs. Try Again');
			redirect($this->router->class . '/view_departments');
			return;

	}


	// --------------------------------------------------------------------
	
	/**
	 * Change  Department hod
	 *
	 * This will Change hod of any department
	 * 
	 * @return void
	 */
	public function change_hod()
	{
		if($input = $this->input->post())
		{

			// validate the username and password
			$this->load->library('form_validation');
			
			// username and password : Required and check for min_length
			$this->form_validation->set_rules('department', 'Department Name', 'trim|required|min_length[4]'); 
			$this->form_validation->set_rules('hod_id', 'HOD ID', 'required|min_length[1]'); 

			// if the requirement are not meet redirect to signup page to re-enter the signup details 
			if ($this->form_validation->run() == FALSE)
			{  
				$this->session->set_flashdata('msg','Insufficient inputs. Try again');
				redirect($this->current_url);
				return;
			}

			if($this->teacher_model->change_hod($input['department'],$input['hod_id']))
			{
				$this->session->set_flashdata('msg','HOD created');
				redirect($this->current_url);
				return;
			} 
			$this->session->set_flashdata('msg','Cant change HOD. Contact admin');
			redirect($this->current_url);
			return;
		}


		$departments = $this->teacher_model->get_department();
		if(!$departments)
		{
		$this->session->set_flashdata('msg','add department first');
			redirect($this->router->class . "/add_department");
			return FALSE;
		}

		$staff =$this->teacher_model->get_user_but_not_hod();
		if(!$staff)
		{
			$this->session->set_flashdata('msg','No staffs found. Add staff and try again');
			redirect('principal/add_staff');
			return FALSE;
		}

		$this->load->library('form_builder');
		$this->form_builder->start_form(['heading' => 'change Head of department']);

		$this->form_builder->addlabel('department');
		$this->form_builder->startdropdown('department');
		foreach ($departments as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['department_name']);
		}		
		$this->form_builder->enddropdown();
	
		$this->form_builder->addlabel('staff name');
		$this->form_builder->startdropdown('hod_id');


		foreach ($staff as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['email'],$value['uid']);
		}
		$this->form_builder->enddropdown();

		$this->form_builder->set_cancel_button();
		$this->_render_principal_view('public/form_builder',false,false);
	}

	//---------------------------------------------------------------------

	/**
	 * Add Staff
	 * 
	 *  @return void
	 */
	public function add_staff()
	{
		if($input = $this->input->post())
		{

			// validate the username and password
			$this->load->library('form_validation');
			
			// username and password : Required and check for min_length
			$this->form_validation->set_rules('email', 'E-mail', 'trim|required|min_length[4]|valid_email');
			$this->form_validation->set_rules('department', 'Department Name', 'trim|required|min_length[4]');
			$this->form_validation->set_rules('username', 'username', 'trim|required|min_length[4]'); 

			// if the requirement are not meet redirect to signup page to re-enter the signup details 
			if ($this->form_validation->run() == FALSE)
			{ 
				$this->session->set_flashdata('msg','Insufficient inputs. Try again.');
				redirect($this->current_url);
				return;
			}
			if($this->teacher_model->add_new_user($input))
			{
				$this->session->set_flashdata('msg','New user added.');
				redirect($this->current_url);
				return FALSE;
			}
			$this->session->set_flashdata('msg','user not added. Try again.');
			redirect($this->current_url);
			return FALSE;
		}
		$this->load->library('form_builder');
		$this->form_builder->start_form(['heading' => 'Add  Staff']);
	
		$this->form_builder->addlabel('Name');
		$this->form_builder->addinput('username');
		$this->form_builder->addlabel('E-mail');
		$this->form_builder->addinput('email');
		$this->form_builder->addlabel('department');
		$this->form_builder->startdropdown('department');	

		$departments = $this->teacher_model->get_department();
		if(!$departments)
		{
			$this->session->set_flashdata('msg','Add department first.');
			redirect($this->router->class . "/add_department");
			return FALSE;
		}
		foreach ($departments as $key => $value) 
		{
			$this->form_builder->dropdownoption($value['department_name']);
		}		
		$this->form_builder->enddropdown();
		$this->form_builder->setbutton('Add Staff');
		$this->_render_principal_view('public/form_builder',false,false);
	}
}
