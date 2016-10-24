<?php

class UploadAjaxController extends InvAjaxController{

	public $results;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
	}
	
	protected function setInputValues(){
	
		$this->setInputValue('type');
		
	}
	
	public function doController(){
	
		//For testing
		global $_FILES;
		//mail('matt@inventpartners.com' , 'Upload Instance' , $_FILES['image']['tmp_name'] . ' | ' . $_FILES['Filedata']['tmp_name'] . ' | ' . $this->arr_input['type']);
		//$this->arr_input['type'] = 'image';
		require_once(MODEL_PATH . 'upload.class.php');
		$upload = new InvUpload;
		if($this->arr_input['type'] == 'image'){
			if($upload->processNewUpload('Filedata' , PUBLIC_SITE_PATH . 'media/')){
				echo $upload->target_filename;
			} else {
				return false;
			}
		} else if($this->arr_input['type'] == 'csv'){
			if($upload->processNewUpload('Filedata' , TMP_PATH)){
				echo $upload->target_filename;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

}

?>