<?php


class InvUserConfig extends InvFileConfig {

	public function __construct($obj_db){
		parent::__construct($obj_db);
		$this->table = 'user';
		$this->pri_key = 'user_id';
		$this->initFromTable();
		$this->enhanceFieldSettings();
	}
	
	public function enhanceFieldSettings(){
	}

}


?>