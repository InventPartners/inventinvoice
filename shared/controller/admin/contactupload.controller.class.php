<?php

class ContactuploadController extends InvController {

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Upload Contacts';
		$this->view_area = 'contacts';
	}
	
	protected function setInputValues(){
		$this->setInputValue('tmp_file');
		$this->setInputValue('import');
		$this->setInputValue('manufacturer_id');
		$this->import_col_mapping = array();
		reset($this->arr_user_input);
		while(list($key , $value) = each($this->arr_user_input)){
			if(substr($key , 0 , 8) == 'heading_'){
				$key = preg_replace("/heading_/A" , '' , $key);
				$this->import_col_mapping[$value] = $key;
			}
		}
	}
	
	public function doController(){
		$this->imported = 0;
		require_once(CONFIG_PATH . 'contact.fileconfig.class.php');
		$this->import_config = new InvProductConfig($this->obj_db);
		$this->hard_mapping = array();
		if($this->arr_input['tmp_file']){
			require_once(MODEL_PATH . 'dataimport.class.php');
			$dataimport = new InvDataImport($this->obj_db);
			if($dataimport->openFile($this->arr_input['tmp_file'])){
				if($this->arr_input['import']){
					$this->imported = $dataimport->importData(
											$this->import_config->table , 
											$this->import_col_mapping , 
											$this->hard_mapping ,
											array (
												'model' => 1 ,
												'selling_price' => 1
											) , 
											array ( 
											) ,
											true);
				} else {
					$this->headings = $dataimport->getHeadings();
				}
			} else {
				echo 'Error';
			}
		}
	}
	
	protected function getWhereAnd($where , $new_bit){
		if($where){
			$where .= ' AND ' . $new_bit;
		} else {
			$where = 'WHERE ' . $new_bit;
		}
		return $where;
	}

}

?>