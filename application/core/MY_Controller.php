<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * MY_Controller Class
 * 
 * This class enables you to controll common things like render,history etc 
 * to some users 
 * @category	Controller
 * @author	jayakrishnancn
 * @link		https://github.com/jayakrishnancn/College-Management-System
 */ 
class MY_Controller extends CI_Controller {

 	/**
 	 * Session data in array so that redudent function call can be reduced
 	 * @var array
 	 */
	protected $session_data = [];
	protected $current_url = NULL;
	/**
	 * MY_Controller constructor
	 * 
	 * load  common_functions library
	 * verify session, user, cookie, ip etc.
	 * set session_data
	 */
	public function __construct()
	{ 
		parent::__construct();
		// load session and common function libraries
		$this->load->library('common_functions');

		// verify session user ip and cookie redirect if any of this is invalid else continue
		$this->common_functions->verify_suci();

		// set session data to a private variable
		$this->session_data = $this->common_functions->session_data; 
		$this->current_url = $this->common_functions->current_url; 

		log_message("info","MY_Controller initiated");
	}

	// -------------------------------------------------------------------- 

	/**
	 * Render  View
	 * 
	 * For loading a common  bootstrap view from application/ view .
	 * This is to improve consistency and reduce redudent code. Use	
	 * $this->_render_view() insted of $this->load->view() 
	 * 
	 * @param	string	$page 		path to php view file
	 * @param	array	 $data 		data to view file 
	 * @param	boolean $directory 		If true relative path will start from view/$directory. 
	 *														 If false	relative path will start from view
	 *														 
	 * @return void
	 */
	protected function _render_view($page, $data = array(), $directory = FALSE) 
	{
 
		// store data, page etc to an array to pass to view so that it can render that page with 
		// data on view
		$data_to_pass['data'] = $data;

		//if there are a number of pages to render pass as an array
		if(is_array($page))
		{
			// append $directory for each page if its an array
			foreach ($page as $key => &$value) 
			{
				// check if directory is passed
				if($directory)
				{
					$value = rtrim($directory,"/")."/" . $value;
				}
			}
		}
		else
		{
			// if $default_directory is true render from view/public directory	
			$page= ($directory != FALSE) ?	rtrim($directory,"/")."/" . $page:$page;
			
		} 
		$data_to_pass['page'] = $page;

		unset($page,$directory,$data);

	 	// session data
		$data_to_pass['data']['session_data'] = $this->session_data; 
	
		 // setup data from public model
		$this->load->model('public_model');
		
		// To display college name and other details 
		$data_to_pass['setup'] = $this->public_model->setup_data();

		// To display in navbar
		$data_to_pass['userpermission'] = $this->public_model->user_groups($this->session_data['uid']);

		// filter all data  
		// load default teacher bootstrap view	in application/view directory 
		$this->load->view('bootstrap', $data_to_pass);
	}
	public function history()
	{ 

		$data['table'] =  $this->common_functions->history();
		$this->_render_view('history', $data,'public');
	}
}
