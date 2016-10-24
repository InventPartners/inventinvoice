<?php


class InvAccountFile extends InvFile {
	
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
	

}


?>