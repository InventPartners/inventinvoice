<?php

class LoginController extends InvController{

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Login';
	}
	
	public function doController(){
		// redirect already logged in users
		if($this->obj_session->userPermitted('/admin/')){
			redirectRequest('/admin/');
		}
	}
	
	protected function displayView(){
		include(VIEW_PATH . $this->view_template . '.html');
	}

}

?>