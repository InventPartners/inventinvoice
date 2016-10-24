<?php

class FileselectorController extends InvController{

	public $manufacturer;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Select File';
	}
	
	protected function setInputValues(){
		$this->setInputValue('fieldname');
	}
	
	public function doController(){
		require_once(MODEL_PATH . 'uploadedfiles.class.php');
		$filelist = new InvUploadedfiles;
		$this->files = $filelist->listFiles();
	}
	
	protected function displayView(){
		include(VIEW_PATH . $this->view_template . '.html');
	}

}

?>