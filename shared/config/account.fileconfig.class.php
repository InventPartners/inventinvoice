<?php


class InvAccountConfig extends InvFileConfig {

	public function __construct($obj_db){
		parent::__construct($obj_db);
		$this->table = 'account';
		$this->pri_key = 'id';
		$this->initFromTable();
		$this->enhanceFieldSettings();
	}
	
	public function enhanceFieldSettings(){
	}

}


?>