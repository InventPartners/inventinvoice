<?php


class InvCountryCodes {

	var $obj_db;
	
	public function __construct($obj_db){
		
		$this->obj_db = &$obj_db;
		
		return true;
	}
	
	public function getCountryCodes(){
	
		$query = 'SELECT * FROM country ORDER BY country_name';
		if($this->obj_db->prepareAndDoQuery($query , array())){
			return $this->obj_db->result_set;
		} else {
			return array();
		}
		
	}
	
	public function getCountryName($country_code){
	
		$query = 'SELECT * FROM country ORDER BY country_name WHERE country_code = ?';
		$values = array($country_code);
		if($this->obj_db->prepareAndDoQuery($query , $values)){
			return $this->obj_db->result_set[0]['country_name'];
		} else {
			return array();
		}
		
	}
		

}


?>