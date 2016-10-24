<?php


class InvInvoiceConfig extends InvFileConfig {

	public function __construct($obj_db){
		parent::__construct($obj_db);
		$this->table = 'invoice';
		$this->pri_key = 'invoice_id';
		$this->initFromTable();
		//$this->joined_tables = '`invoiceitem`';
		//$this->join_statement = '`invoiceitem`.`invoice_id` = `invoice`.`invoice_id`';
		//$this->calc_tax_on = array('order_total');
	}

}


?>