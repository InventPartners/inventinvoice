<?php

class InvController{

	protected $obj_db;
	protected $obj_countrycodes;
	
	protected $max_path_length;
	protected $request_path;
	protected $path_info;
	protected $obj_session;
	protected $user_request;
	protected $arr_user_input;
	protected $arr_input = array();
	protected $arr_list_filter_input = array();
	
	protected $view_title;
	protected $site_domain;
	protected $view_template;
	
	protected $template_top = 'top';
	protected $template_bottom = 'bottom';

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->site_domain = SITE_DOMAIN;
		global $_SERVER;
		$strip_getstring = explode('?' , $_SERVER['REQUEST_URI']);
		$this->request_path = $strip_getstring[0];
		$this->obj_db = &$obj_db;
		$this->obj_session = &$obj_session;
		$this->setPathInfo($path_info);
		$this->getUserInput();
		$this->view_template = $view_template;
		$this->max_path_length = 0;
		
		$this->obj_countrycodes = new InvCountryCodes($this->obj_db);
	}
	
	protected function setInputValues(){
		return true;
	}
	
	protected function doController(){
		return true;
	}
	
	public function processRequest(){
		$this->setInputValues();
		$this->doController();
		$this->sendHeaders();
		$this->displayView();
	}
	
	public function setPathInfo($path_info){
		if(count($path_info) <= $this->max_path_length){
			$this->path_info = $path_info;
			return true;
		} else {
			$this->show404();
		}
	}
	
	/******************************
	BASIC LIBRARY METHODS
	******************************/
	
	protected function sendHeaders(){
		
	}
	
	protected function displayView(){
		include(TEMPLATE_PATH . $this->template_top . '.html');
		include(VIEW_PATH . $this->view_template . '.html');
		include(TEMPLATE_PATH . $this->template_bottom . '.html');
	}
	
	protected function show404(){
		$this->view_title = '404 Not Found';
		$this->view_template = 'error404';
		header("HTTP/1.0 404 Not Found");
		$this->displayView();
		exit;
	}	
	
	protected function getWidget($widget){
		include(WIDGET_PATH . $widget . '.html');
	}
	
	// Basic check of an input value and set
	protected function setInputValue($name , $list_filter = false){
		if(isset($this->arr_user_input[$name])){
			$this->arr_input[$name] = $this->arr_user_input[$name];
			$this->setListFilter($name , $list_filter);
			return true;
		} else {
			$this->arr_input[$name] = '';
			$this->setListFilter($name , $list_filter);
			return false;
		}
		
	}
	
	// Basic check of a numeric input value and set
	protected function setNumericInputValue($name , $decimals=-1 , $list_filter = false){
		if(isset($this->arr_user_input[$name])){
			$this->arr_input[$name] = number_format_app($this->arr_user_input[$name] , $decimals);
			$this->setListFilter($name , $list_filter);
			return true;
		} else {
			$this->arr_input[$name] = false;
			$this->setListFilter($name , $list_filter);
			return false;
		}
	}
	
	// re-usable list filter values
	protected function setListFilter($name , $list_filter){
		if($list_filter){
			$this->arr_list_filter_input[$name] = $this->arr_input[$name];
		}
	}

	// Get User input
	protected function getUserInput(){
		// Initialise the array
		$this->arr_user_input = array();
		//Iterate over _GET _POST data - basic validation / cleaning of user input
		global $_GET;
		while(list($key , $value) = each($_GET)){
			if(get_magic_quotes_gpc()){
				$value = stripslashes($value);
			}
			$this->arr_user_input[$key] = trim($value);
			if(substr($key , 0 , 4) == 'req_'){
				$key = substr($key , 4);
				$this->arr_user_input[$key] = $value;
			}
		}
		global $_POST;
		while(list($key , $value) = each($_POST)){
			if(get_magic_quotes_gpc()){
				$value = stripslashes($value);
			}
			$this->arr_user_input[$key] = trim($value);
			if(substr($key , 0 , 4) == 'req_'){
				$key = substr($key , 4);
				$this->arr_user_input[$key] = $value;
			}
		}
	}
	
	protected function getListLink($arr_params){
		
		$getstring = '';
		// What's the base link params?
		// Are they already set?
		$get_string = '';
		reset($this->arr_list_filter_input);
		while(list($key , $value) = each($this->arr_list_filter_input)){
			if($key == 'page' && isset($arr_params[$key]) && $arr_params[$key]){
				// we're resetting the page value, so skip this one	
			} else {
				if($get_string){
					$get_string .= '&';
				}
				$get_string .= urlencode($key);
				$get_string .= '=';
				$get_string .= urlencode($value);
			}
			
		}
		// params passed through?
		reset($arr_params);
		while(list($key , $value) = each($arr_params)){
			if($get_string){
				$get_string .= '&';
			}
			$get_string .= urlencode($key);
			$get_string .= '=';
			$get_string .= urlencode($value);
			
		}
		//echo '">' . $get_string;
	
		$link = $this->request_path;
		if($get_string){
			$link .= '?' . htmlspecialchars($get_string);
		}
		
		return $link;
	
	}	

}

?>