<?php


class InvUserFile extends InvFile {
	
	public function __construct($config , $obj_db){
		parent::__construct($config , $obj_db);
	}
	
	public function open($id){
		if(parent::open($id)){
			return true;
		} else {
			return false;
		}
	}
	
	public function create(){
		if(parent::create()){
			$this->filedata['contact_status'] = 'active';
			return true;
		} else {
			return false;
		}
	}
	
	public function updatePassword($password){
		$query = 'UPDATE `' . $this->config->table . '` SET `password` = PASSWORD(?) WHERE `' . $this->config->pri_key . '` = ?'; 
		//echo $query;
		$params = array();
		$params[] = $password;
		$params[] = $this->id;
		if($this->obj_db->prepareAndDoQuery($query , $params)){
			return true;
		} else {
			// Updates which make no change are still ok...
			return false;
		}
	}
	

}


?>