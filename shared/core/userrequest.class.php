<?php

class UserRequest{

	protected $obj_db;
	protected $obj_session;
	protected $arr_user_input = array();
	protected $request_array = array();
	protected $path_to_controller;
	protected $controller_file_name;
	public $obj_controller;

	public function __construct(){
	
	/*
		$this->obj_db = new InvDB;
		$this->obj_db->connect();
	*/
		// Invoice Database
		$this->obj_db = new InvDB();
		// User database
		$this->obj_userdb = new InvDB();
		$this->obj_userdb->connect(USER_DB);
		// Session
		$this->obj_session = new InvSession($this->obj_userdb , $this->obj_db);
		return true;
	}

	// We use the server request URI to find out what the user wanted to do.
	// loads the correct controller	
	public function handleRequest(){

		// Is there a login to do?
		if(isset($_POST['username']) && isset($_POST['password'])){
			$this->obj_session->login($_POST['username'] , $_POST['password']);
		}
	
		// Split the URL into path componenets to figure out which controller to load.
		// Send the remainder of the path to the controller.
		global $_SERVER;
		$strip_getstring = explode('?' , $_SERVER['REQUEST_URI']);
		$this->request_array = explode('/' , $strip_getstring[0]);
		array_shift($this->request_array);
		$this->controller_file_name = $this->request_array[0];
		//array_shift($this->request_array);
		if(isset($this->request_array[count($this->request_array)-1]) && $this->request_array[count($this->request_array)-1] == ''){
			array_pop($this->request_array);
		}
		
		//$is_standard_controller_request = true;
		if(!preg_match('/[^a-z\_]/' , $this->controller_file_name)){
			// Are we requesting an AJAX helper here?
			if($this->controller_file_name == 'ajax'){
				// Ajax request should only have one path element
				array_shift($this->request_array);
				// Ajax request are handled here
				require_once(SHARED_PATH . 'ajax/ajaxcontroller.class.php');
				$this->loadController(AJAX_PATH , '.ajaxcontroller.class.php' , 'AjaxController');
			} else {
				$this->loadController(CONTROLLER_PATH , '.controller.class.php' , 'Controller');
			}
		} else {
			$this->show404();
		}
		
		
	}
	
	protected function loadController($base_path , $controller_suffix , $controller_name_suffix){
		$this->path = $base_path;
		$auth_path = '/';
		$view_path = '';
		// Drill down the path structure
		while(isset($this->request_array[0]) && is_dir($this->path . $this->request_array[0])){
			$auth_path .= $this->request_array[0] . '/';
			if($this->obj_session->userPermitted($auth_path)){
				$this->path .= $this->request_array[0] . '/';
				$view_path .= $this->request_array[0] . '/';
				array_shift($this->request_array);
			} else {
				redirectRequest('/login');
				exit;
			}
		}

		if(isset($this->request_array[0]) && $this->request_array[0]){
			$this->controller_file_name = $this->request_array[0];
		} else {
			$this->controller_file_name = 'default';
		}

		// Try to load the controller
		if(is_file($this->path . $this->controller_file_name . $controller_suffix)){
			array_shift($this->request_array);
			$controller_obj_name = UCfirst($this->controller_file_name) . $controller_name_suffix;
			require_once($this->path . $this->controller_file_name . $controller_suffix);
			$this->obj_controller = new $controller_obj_name($this->obj_db , $this->obj_session , $view_path . $this->controller_file_name , $this->request_array);
			$this->obj_controller->processRequest();	
		} else {
			$this->show404();
		}
	
	}
	
	protected function show404(){
		require_once(CONTROLLER_PATH . 'error404.controller.class.php');
		$this->obj_controller = new error404Controller($this->obj_db , $this->obj_session , 'error404' , $this->request_array);
		$this->obj_controller->processRequest();	
		
	}

}

?>