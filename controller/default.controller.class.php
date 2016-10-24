<?php

class defaultController extends InvController{

	public $manufacturers;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'CP';
	}
	
	public function doController(){
		// Either redirct oto the admin index of the login page
		if($this->obj_session->userPermitted('/admin/')){
			redirectRequest('/admin/');
		} else {
			redirectRequest('/login/');
		}
	}

}

?>