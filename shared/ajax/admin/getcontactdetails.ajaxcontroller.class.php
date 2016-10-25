<?php

class GetcontactdetailsAjaxController extends InvAjaxController{

	public $results;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
	}
	
	protected function setInputValues(){
	}
	
	public function doController(){

		//header('Content-Type: application/x-javascript; charset=UTF-8');
		header('Content-Type: application/json; charset=UTF-8');

		$this->contact = $this->obj_db->getFileModel('contact');
		if($this->path_info[0]){
			if($this->contact->open($this->path_info[0])){
   				echo json_encode($this->contact->filedata);  
			} else {
				return false;
			}
		} else {
			return false;
		}
		
	}

}

?>