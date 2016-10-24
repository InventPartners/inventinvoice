<?php


class InvCSV{

	public $source_file;
	public $obj_db;
	public $first_row = array();
	public $import_table;
	public $import_col_mapping = array();
	
	public function __construct($obj_db){
		$this->obj_db = &$obj_db;
		return true;
	}
	
	public function openFile($source_file){
		if(preg_match("!\/\\\!" , $source_file)){
			return false;
		} else {
			$source_file = TMP_PATH . $source_file;
			if(is_file($source_file)){
				$this->source_file = $source_file;
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function getHeadings(){
		if($this->loadFile()){
			$row = $this->getRow();
			return $row;
		} else {
			return false;
		}
	}
	
	public function importData($import_table , $import_col_mapping , $hard_mapping = array() , $required_fields = array() , $custom_fields = array() , $replace=false){
		if($replace){
			$delete_query = 'DELETE FROM `' . $import_table . '` WHERE ';
			// For each col mapping...
			$col_count = 0;
			reset($hard_mapping);
			$where = '';
			$params_array = array();
			while(list($key , $value) = each($hard_mapping)){
				if($where){
					$where .= ' AND ';
				}
				$where .= '`' . $key . '` = ?';
				$params_array[] = $value;
			}
			$this->obj_db->prepareAndDoQuery($delete_query . $where , $params_array);
		}
		if($this->loadFile()){
			// Build insert query
			$insert_query = 'INSERT INTO `' . $import_table . '` ( ';
			// For each col mapping...
			$col_count = 0;
			reset($hard_mapping);
			while(list($key , $value) = each($hard_mapping)){
				if($key){
					// add an insert column
					if($col_count > 0){
						$insert_query .= ' , ';
					}
					$insert_query .= ' `' . $key . '` ';
					$col_count ++;
				}
			}
			reset($import_col_mapping);
			while(list($key , $value) = each($import_col_mapping)){
				if(!in_array($key , $custom_fields)){
					if($key){
						// add an insert column
						if($col_count > 0){
							$insert_query .= ' , ';
						}
						$insert_query .= ' `' . $key . '` ';
						$col_count ++;
					}
				}
			}
			$insert_query .= ' ) VALUES ( ';
			// For each col mapping...
			$col_count = 0;
			reset($hard_mapping);
			while(list($key , $value) = each($hard_mapping)){
				if($key){
					// add an insert value placeholder
					if($col_count > 0){
						$insert_query .= ' , ';
					}
					$insert_query .= ' ? ';
					$col_count ++;
				}
			}
			reset($import_col_mapping);
			while(list($key , $value) = each($import_col_mapping)){
				if(!in_array($key , $custom_fields)){
					if($key){
						// add an insert value placeholder
						if($col_count > 0){
							$insert_query .= ' , ';
						}
						$insert_query .= ' ? ';
						$col_count ++;
					}
				}
			}
			$insert_query .= ' ) ';
			// Go over each row of data
			$rows_inserted = 0;
			$rows_rejected = 0;
			$rows_failed = 0;
			while($row = $this->getRow()){
				$data_by_field_name = array();
				$required_fields_for_row = $required_fields;
				// Build insert params
				$params_array = array();
				// For each col mapping...
				reset($hard_mapping);
				while(list($key , $value) = each($hard_mapping)){
					if($key){
						$data_by_field_name[$key] = $value;
						// add an insert value;
						$params_array[] = $value;
					}
				}
				reset($import_col_mapping);
				while(list($key , $value) = each($import_col_mapping)){
					if($key){
						$data_by_field_name[$key] = $row[$value];
					}
					if(!in_array($key , $custom_fields)){
						if($key){
							// add an insert value;
							$params_array[] = $row[$value];
						}
						if(isset($required_fields_for_row[$key]) && $row[$value]){
							unset($required_fields_for_row[$key]);
						}
					}
				}
				// Insert a table row in DB
				//echo $insert_query;
				//print_r($params_array);
				if(count($required_fields_for_row) > 0){
					$rows_rejected++;
				} else {
					if($this->obj_db->prepareAndDoQuery($insert_query , $params_array)){
						$this->doCustomStuff($data_by_field_name , $this->obj_db->lastInsertId());
						$rows_inserted++;
					} else {
						$rows_failed++;
					}
				}
			}
			return $rows_inserted;
		} else {
			return false;
		}
	}
	
	protected function getRow(){
	
		return fgetcsv($this->fp , 10000000);
	
	}
	
	protected function loadFile(){
		if($this->source_file){
			if($this->fp = fopen($this->source_file , 'r')){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	
	// Some custom stuff for very specific datasets
	protected function doCustomStuff($data_by_field_name , $inserted_row_id){
		// Do we need to do a split and created join records on the productaccessories to products?
		if(isset($data_by_field_name['product_productaccessory']) && $inserted_row_id){
			$insert_query = 'INSERT INTO `product_productaccessory` (`productaccessory_id` , `product_id`) SELECT \'' . $inserted_row_id . '\' , `product_id` FROM `product` WHERE `model` = ?';
			$product_productaccessories = explode(',' , $data_by_field_name['product_productaccessory']);
			for($i=0; $i<count($product_productaccessories); $i++){
				$params_array = array(trim($product_productaccessories[$i]));
				$this->obj_db->prepareAndDoQuery($insert_query , $params_array);
			}
		}
	}
	
}

?>