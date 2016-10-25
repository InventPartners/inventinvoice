<?php

class Error404Controller extends InvController{

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->show404();
	}

}

?>