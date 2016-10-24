<?php


class InvPurchaseConfig extends InvFileConfig {

	public function __construct($obj_db){
		parent::__construct($obj_db);
		$this->table = 'purchase';
		$this->pri_key = 'purchase_id';
		$this->initFromTable();
	}

}


?>