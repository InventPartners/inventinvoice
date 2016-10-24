<?php


class InvRepeatinvoiceConfig extends InvFileConfig {

	public function __construct($obj_db){
		parent::__construct($obj_db);
		$this->table = 'repeatinvoice';
		$this->pri_key = 'repeatinvoice_id';
		$this->initFromTable();
		$this->joined_tables = '`contact`';
		$this->join_statement = '`repeatinvoice`.`contact_id` = `contact`.`contact_id`';
		//$this->calc_tax_on = array('order_total');
	}

}


?>