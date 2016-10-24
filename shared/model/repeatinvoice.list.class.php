<?php


class InvRepeatinvoiceList extends InvList {
	
	public function __construct($config , $obj_db){
		parent::__construct($config , $obj_db);
	}
	
	public function getRepeatDueInvoices($count = false){
	
		// Build queries
		$from = '`' . $this->config->table . '` ';
		if($this->config->joined_tables){
			$from .= ' , ' . $this->config->joined_tables;
		}
		// The page results query
		if($count){
			$query = 'SELECT COUNT(repeatinvoice_id) AS count FROM ' . $from;
		} else {
			$query = 'SELECT * FROM ' . $from;
		}
		$query .= "
			WHERE `repeatinvoice`.`next_date` < NOW()
			AND `repeatinvoice`.`repeatinvoice_status` = 'active'
		";
		if($this->config->join_statement){
			$query .= ' AND ' . $this->config->join_statement;
		}
	
		$this->obj_db->prepareAndDoQuery($query , array());
	
		/*
		$query .= "
			WHERE `repeatinvoice`.`next_date` < NOW()
			AND `repeatinvoice`.`repeatinvoice_status` = 'active';
		";
		$order = '`repeatinvoice`.`next_date`';
		$orderlist->getList($where , $params_array , $order , $this->arr_input['page']);
		*/
	
	}
	
	public function doAllRepeatDueInvoices(){
	
		$this->getRepeatDueInvoices();
	
	}
	
}


?>