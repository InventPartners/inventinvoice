<?php

require_once(CONFIG_PATH . 'invoice.fileconfig.class.php');

class InvInvoiceeventConfig extends InvInvoiceConfig {

	public function __construct($obj_db){
		parent::__construct($obj_db);
		$this->joined_tables = '`invoicelog`';
		$this->join_statement = '`invoice`.`invoice_id` = `invoicelog`.`invoice_id`';
		$this->initFromTable();
	}

}


?>