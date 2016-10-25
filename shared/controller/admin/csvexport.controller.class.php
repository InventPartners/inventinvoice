<?php

class CSVExportController extends InvController{

	public $manufacturers;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Year end';
		$this->view_area = 'reports';
	}
	
	protected function setInputValues(){
	}
	
	public function doController(){
		
		$this->setQuery();
		$this->setParams();
		$this->setCols();
		$this->doQuery();
		$this->exportCSV();
		
		
	}
	
	public function setQuery(){
		$this->dbquery = '';
	}
	
	public function setParams(){
		$this->params = array();
	}
	
	public function setCols(){
		$this->cols = array();
	}
	
	public function doQuery(){
	
		$this->obj_db->prepareAnddoQuery($this->dbquery , $this->params);
	
	}
	
	public function exportHeaders(){
	
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=export.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		//print_r($this->config->fields);
		$i=0;
		foreach($this->cols as $ckey=>$field){
			if($i>0){
				echo ",";
			}
			echo '"' . $this->csvEscape($field['caption']) . '"';
			$i++;
		}
		echo "\n";
	
	}
	
	public function exportRow($row){
				
		$i=0;
		foreach($this->cols as $ckey=>$field){
			if($i>0){
				echo ",";
			}
			echo '"';
			if(isset($row[$ckey])){
				echo $this->csvEscape($row[$ckey]);
			} else {
				echo $field['default'];
			}
			echo '"';
			$i++;
		}
		echo "\n";
		
	}
	
	public function csvEscape($text){
		return str_replace('"', '""', preg_replace("/\n/" , '\n' , preg_replace("/\r/" , '\r' , $text)));
	}
	
	public function exportCSV(){
	
		$this->exportHeaders();
		
		foreach($this->obj_db->result_set as $row){
			$this->exportRow($row);
		}
		
		exit;
	
	}

}

?>