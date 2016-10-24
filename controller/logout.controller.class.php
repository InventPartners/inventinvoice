<?php

class LogoutController extends InvController{

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Logout';
		$this->view_template = 'login';
	}
	
	public function doController(){
		// Log the user out
		$this->obj_session->logout();
	}
	
	protected function displayView(){
		include(VIEW_PATH . $this->view_template . '.html');
	}

}

?>