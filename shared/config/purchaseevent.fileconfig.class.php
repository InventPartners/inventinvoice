<?php

require_once(CONFIG_PATH . 'purchase.fileconfig.class.php');

class InvPurchaseeventConfig extends InvPurchaseConfig {

	public function __construct($obj_db){
		parent::__construct($obj_db);
		$this->joined_tables = '`purchaselog`';
		$this->join_statement = '`purchase`.`purchase_id` = `purchaselog`.`purchase_id`';
		$this->initFromTable();
	}

}


?>