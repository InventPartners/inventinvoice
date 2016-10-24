<?php


class InvDataImport{

	public $obj_db;
	public $obj_import;
	
	public function __construct($obj_db){
		$this->obj_db = &$obj_db;
		return true;
	}
	
	public function openFile($source_file){
		require_once(MODEL_PATH . 'dataimport.csv.class.php');
		// Is this an XML file
		$file_extension = array_reverse(explode('.' , $source_file));
		if($file_extension[0] == 'xml'){
			require_once(MODEL_PATH . 'dataimport.officexml.class.php');
			$this->obj_import = new InvOfficeXML($this->obj_db);
		} else {
			$this->obj_import = new InvCSV($this->obj_db);
		}
		return $this->obj_import->openFile($source_file);
	}
	
	public function getHeadings(){
		return $this->obj_import->getHeadings();
	}
	
	public function importData($import_table , $import_col_mapping , $hard_mapping = array() , $required_fields = array() , $custom_fields = array() ,$replace=false){
		return $this->obj_import->importData($import_table , $import_col_mapping , $hard_mapping , $required_fields , $custom_fields , $replace);
	}
	
}

?>