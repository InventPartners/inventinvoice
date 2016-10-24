<?php


class InvOfficeXML extends InvCSV {

	public $fielddata  = array();
	public $row_pos;
	
	public function __construct($obj_db){
		parent::__construct($obj_db);
		return true;
	}
	
	protected function getRow(){
	
		if(isset($this->fielddata[$this->row_pos])){
			$row = $this->fielddata[$this->row_pos];
			$this->row_pos ++;
			return $row;
		} else {
			return false;
		}
		
	
	}
	
	protected function loadFile(){
		$this->row_pos = 0;
		$this->fielddata = array();
		if($this->source_file){
			if($this->fp = fopen($this->source_file , 'r')){
				$xml = fread($this->fp , filesize($this->source_file));
				$p = xml_parser_create();
				xml_parse_into_struct($p, $xml, $vals, $index);
				$row = array();
				$element_data = '';
				//print_r($vals);
				for($i=0; $i<count($vals); $i++){
					// Rows
					if($vals[$i]['tag'] == 'ROW'){
						// Opening / closing a row of data
						if($vals[$i]['type'] == 'open'){
							$row = array();
						} else if($vals[$i]['type'] == 'close'){
							$this->fielddata[] = $row;
							$row = array();
						}
					}
					// Cells
					if($vals[$i]['tag'] == 'CELL'){
						// Opening / closing a cell of data
						if($vals[$i]['type'] == 'open'){
							$element_data = '';
						} else if($vals[$i]['type'] == 'close'){
							$row[] = trim($element_data);
							$element_data = '';
						}
					}
					// Adding individual data element
					if($vals[$i]['tag'] == 'DATA'){
						$element_data .= $vals[$i]['value'] . ' ';
					}
				}
				//print_r($this->fielddata);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

}

?>