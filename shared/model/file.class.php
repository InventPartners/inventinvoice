<?php


class invFile {

	var $id;
	var $obj_db;
	var $config;
	var $current_saved_filedata;
	var $filedata;
	var $is_new;
	var $child_data = array();
	
	public function __construct($config , $obj_db){
		if($config){
			$this->config = $config;
			$this->obj_db = &$obj_db;
			$this->id = false;
			// Instantiate tax class
			require_once(MODEL_PATH . 'tax.class.php');
			$this->obj_tax = new InvTax($this->obj_db);
		} else {
			return false;
		}
	}
	
	/*********************************
	Begin to be modified in every file class 
	**********************************/
	
	public function getCartableValues(){
		return false;
	}
	
	/*********************************
	End to be modified in every file class 
	**********************************/
	
	
	
	// Open the file
	public function open($id){
		$this->is_new = false;
		$query = 'SELECT * FROM `' . $this->config->table . '` ';
		if($this->config->joined_tables){
			$query .= ' , ' . $this->config->joined_tables;
		}
		
		$query .= ' WHERE `' . $this->config->table . '`.`' . $this->config->pri_key . '` = ?';
		if($this->config->join_statement){
			$query .= ' AND ' . $this->config->join_statement;
		}
		$params_array = array($id);
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			if(count($this->obj_db->result_set) == 1){
				// Only open the file if one result and ONE RESULT ONLY is found
				$this->filedata = $this->obj_db->result_set[0];
				// To to calculate?
				for($c=0; $c<count($this->config->calc_tax_on); $c++){
					if(isset($this->filedata[$this->config->calc_tax_on[$c]])){
						$this->filedata[$this->config->calc_tax_on[$c] . '_tax'] = $this->obj_tax->calcTax($this->filedata[$this->config->calc_tax_on[$c]] , 0);
						$this->filedata[$this->config->calc_tax_on[$c] . '_inc'] = $this->filedata[$this->config->calc_tax_on[$c]] + $this->filedata[$this->config->calc_tax_on[$c] . '_tax'];
					} else {
						$this->filedata[$this->config->calc_tax_on[$c] . '_tax'] = 0;
						$this->filedata[$this->config->calc_tax_on[$c] . '_inc'] = 0;
					}
				}
				$this->current_saved_filedata = $this->filedata;
				$this->id = $this->obj_db->result_set[0][$this->config->pri_key];
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	// Open the file
	public function create(){
		for($i=0; $i<count($this->config->fields); $i++){
			$fieldname = $this->config->fields[$i]['Field'];
			$this->updateValue($fieldname , '');
		}
		$this->is_new = true;
		return true;
	}

	// Copy this invoice to a new ID
	public function copy(){
		// Create a new file
		$this->is_new = true;
		// Save new file and return new ID
		return $this->save();
	}

	// Save the file after editing
	public function save(){
		if($this->is_new){
			$query_data = $this->buildInsertFields();
			$query = 'INSERT INTO `' . $this->config->table . '` (' . $query_data['fields'] . ') VALUES (' . $query_data['values'] . ')'; 
			$params = $query_data['data'];
			if($this->obj_db->prepareAndDoQuery($query , $params)){
				$this->id = $this->obj_db->lastInsertId();
				return $this->id;
			} else {
				return false;
			}
		} else if($this->id) {
			$query_data = $this->buildUpdateFields();
			if(count($query_data['data']) > 0){
				$query = 'UPDATE `' . $this->config->table . '` SET ' . $query_data['fields'] . ' WHERE `' . $this->config->pri_key . '` = ?'; 
				$params = array();
				$params = $query_data['data'];
				$params[] = $this->id;
				if($this->obj_db->prepareAndDoQuery($query , $params)){
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		}
		return true;	
	}
	
	public function delete(){
		if($this->id) {
			// Delete child data
			while(list($key , $value) = each($this->config->child_data)){
				$query = 'DELETE FROM `' . $key . '` WHERE `' . $value . '` = ?'; 
				$params = array();
				$params[] = $this->id;
				$this->obj_db->prepareAndDoQuery($query , $params);
			}
			$query = 'DELETE FROM `' . $this->config->table . '` WHERE `' . $this->config->pri_key . '` = ?'; 
			$params = array();
			$params[] = $this->id;
			if($this->obj_db->prepareAndDoQuery($query , $params)){
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function updateFromFormData(){
		global $_POST;
		for($i=0; $i<count($this->config->fields); $i++){
			$fieldname = $this->config->fields[$i]['Field'];
			if(isset($_POST[$fieldname])){
				$posted_value = $_POST[$fieldname];
				if(get_magic_quotes_gpc()){
					$posted_value = stripslashes($posted_value);
				}
				// No some numeric conversion on numeric values
				if($this->config->fields[$i]['validation'] == 'numeric') {
					$posted_value=number_format_app($posted_value , -1);
				}
				if(!isset($this->filedata[$fieldname]) || $posted_value != $this->filedata[$fieldname]){
					$this->updateValue($fieldname , $posted_value);
				}
			} else if( $this->config->fields[$i]['validation'] == 'datetime') {
				// Date / Datetime fields with advanced inputs
				if( isset($_POST[$fieldname . '_y']) &&
				    isset($_POST[$fieldname . '_m']) &&
				    isset($_POST[$fieldname . '_d'])
				){
					$posted_value = $_POST[$fieldname . '_y'] . '-' . $_POST[$fieldname . '_m'] . '-' . $_POST[$fieldname . '_d'];
					$this->updateValue($fieldname , $posted_value);
				}
			}
		}
		return true;
	}
	
	// Update value
	public function updateValue($key , $value){
		if(isset($this->filedata[$key])){
			$old = $this->filedata[$key];
		} else {
			$old = 'NOT_SET';
		}
		$this->arr_change_history[] = array('key' => $key , 'old' => $old , 'new' => $value);
		$this->filedata[$key] = $value;
		return true;
	}

	protected function buildInsertFields(){
	
		$return_data = array();
	
		$sql_statement_fields = '';
		$sql_statement_values = '';
		$sql_statement_data = array();
		for($i=0; $i<count($this->config->fields); $i++){
			// has this value changed?
			$fieldname = $this->config->fields[$i]['Field'];
			if($fieldname != 'updated' && $fieldname != 'created'){
				if(isset($this->config->fields['tablename'])){
					$sql_statement_fields .= ' `' . $this->config->fields[$i]['tablename'] . '`.';
				} else {
					$sql_statement_fields .= ' `' . $this->config->table . '`.';
				}
				$sql_statement_fields .= '`' . $fieldname . '` , ';
				$sql_statement_values .= ' ? , ';
				$sql_statement_data[] = $this->filedata[$fieldname];
			}
		}
					
		if($this->config->do_created_date){
			$sql_statement_fields .= ' `' . $this->config->table . '`.`created` ';
			$sql_statement_values .= ' UTC_TIMESTAMP() , ';
		}
	
		$return_data['fields'] = preg_replace("/,$/" , '' , trim($sql_statement_fields));
		$return_data['values'] = preg_replace("/,$/" , '' , trim($sql_statement_values));
		$return_data['data'] = $sql_statement_data;
		//$return_data['data'] = preg_replace("/,$/" , '' , trim($sql_statement_data));
	
		return $return_data;
	
	}

	protected function buildUpdateFields(){
	
		$return_data = array();
		$sql_statement_fields = '';
		$sql_statement_data = array();
		for($i=0; $i<count($this->config->fields); $i++){
			// has this value changed?
			$fieldname = $this->config->fields[$i]['Field'];
			if($fieldname != 'updated'){
				if($this->filedata[$fieldname] != $this->current_saved_filedata[$fieldname]){
					if(isset($this->config->fields['tablename'])){
						$sql_statement_fields .= ' `' . $this->config->fields[$i]['tablename'] . '`.';
					} else {
						$sql_statement_fields .= ' `' . $this->config->table . '`.';
					}
					$sql_statement_fields .= '`' . $fieldname . '` =';
					$sql_statement_fields .= ' ? , ';
					$sql_statement_data[] = $this->filedata[$fieldname];
				}
			}
		}
		
		$return_data['fields'] = preg_replace("/,$/" , '' , trim($sql_statement_fields));
		$return_data['data'] = $sql_statement_data;
		//$return_data['data'] = preg_replace("/,$/" , '' , trim($sql_statement_data));
		return $return_data;
	
	}

}


?>