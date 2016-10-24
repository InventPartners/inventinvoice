<?php


class InvContactConfig extends InvFileConfig {

	public function __construct($obj_db){
		parent::__construct($obj_db);
		$this->table = 'contact';
		$this->pri_key = 'contact_id';
		$this->initFromTable();
		$this->enhanceFieldSettings();
	}
	
	public function enhanceFieldSettings(){
	}

}


?>