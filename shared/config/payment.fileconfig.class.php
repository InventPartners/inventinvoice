<?php


class InvPaymentConfig extends InvFileConfig {

	public function __construct($obj_db){
		parent::__construct($obj_db);
		$this->table = 'payment';
		$this->pri_key = 'payment_id';
		$this->initFromTable();
		//$this->joined_tables = '`invoiceitem`';
		//$this->join_statement = '`invoiceitem`.`invoice_id` = `invoice`.`invoice_id`';
		//$this->calc_tax_on = array('order_total');
	}

}


?>