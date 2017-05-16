<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form_builder{

	public $error = NULL;
	public $form = array();
	private $CI = NULL; 
	private $countfields = 0;
	private $optioncount = 0;
	public $formviewlocation='renderform';

	function __construct()
	{ 

		$this->CI=& get_instance();
		$this->CI->load->helper('form');
	} 

	public function start_form($param)
	{

		$this->form['action']=$param['action'];
		$this->form['attributes']['method']=isset($param['method']) ?$param['method']: "post";
		$this->form['attributes']['accept-charset']=isset($param['accept-charset']) ?$param['accept-charset']: "utf-8";
		$this->form['attributes']['extra']=isset($param['extra']) ?$param['extra']: ""; 

		$this->form['heading']=isset($param['heading']) ?$param['heading']:NULL;
		$this->form['desc']=isset($param['desc']) ?$param['desc']: NULL;
	}
	public function addinput($name='',$type='text',$autofocus=false,$value="")
	{
		if(!is_array($name))
		{
			$attribute['name']=$name;
			$attribute['type']=$type;
		}
		else{
			$attribute=$name;
		}
		$default_attribute=array(
				'name' =>'name'.$this->countfields,	
				'type' => 'text',	
				'id' => 'input'.$this->countfields,	
				'value' => $value,	
				'placeholder'=>' ',
				'autofocus' => $autofocus,
				'extra' => ""
			);
		
		$this->form['fields'][$this->countfields++]=array_merge( $default_attribute,$attribute) ;		 

	}

	public function addlabel($value,$for=NULL)
	{ 

		$attributes=array(
				'type' => 'label',	 
				'value' => $value,	
				'for'=> $for, 
			);

		$this->form['fields'][$this->countfields++]=$attributes;
	}
	public function startdropdown($name='')
	{
		 
		$attribute['name']=$name; 
		 
		$default_attribute=array( 
				'type' => 'dropdown',
				'autofocus' => false,
				'extra' => ' ',
			);
		$this->form['fields'][$this->countfields]=array_unique(array_merge( $default_attribute,$attribute)) ;
		 
	}
	
	public function enddropdown()
	{
		$this->countfields++;
		$this->optioncount = 0;
	}

	public function dropdownoption($displayvalue,$realvalue=NULL,$selected=false)
	{
		if($realvalue===NULL){
			$realvalue=$displayvalue;
		}
		$this->form['fields'][$this->countfields]['option'][$this->optioncount++] = [
					'value' => $realvalue,
					'displayvalue' => $displayvalue,
					'selected' => $selected
					];
	}

	public function setbutton($value,$class = NULL)
	{

		$this->form['button']['value']=$value;
		$this->form['button']['class']=$class;
	}
	
	public function renderform($formviewlocation=NULL)
	{
		if($formviewlocation!==NULL)
			$this->formviewlocation=$formviewlocation;

		$CI = &get_instance();
		$CI->load->view($this->formviewlocation,$this->form);
	}

}
