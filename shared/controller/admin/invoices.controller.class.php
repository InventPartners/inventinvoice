<?php

class InvoicesController extends InvController{

	public $manufacturers;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Invoices';
		$this->view_area = 'invoices';
	}
	
	protected function setInputValues(){
		$this->setInputValue('page' , true);
		$this->setInputValue('q' , true);
		$this->setInputValue('status' , true);
		$this->setInputValue('invoice_void');
		$this->setInputValue('invoice_paid');
		$this->setInputValue('invoice_unpaid');
		$this->setInputValue('processorder');
		$this->setInputValue('reopen');
	}
	
	public function doController(){
	
		//Are we marking an order as processed?
		if($this->arr_input['invoice_paid'] || $this->arr_input['invoice_unpaid'] || $this->arr_input['invoice_void']){
			$this->invoice = $this->obj_db->getFileModel('invoice');
			$order_found = false;
			
			if($this->arr_input['invoice_paid']){
				if($this->invoice->open($this->arr_input['invoice_paid'])){
					$this->invoice->setStatus('paid');
				}
			} else if ($this->arr_input['invoice_unpaid']){
				if($this->invoice->open($this->arr_input['invoice_unpaid'])){
					$this->invoice->setStatus('outstanding');
				}
			} else if ($this->arr_input['invoice_void']){
				if($this->invoice->open($this->arr_input['invoice_void'])){
					$this->invoice->setStatus('void');
				}
			}
			
		}
	
		$orderlist = $this->obj_db->getListModel('invoice');
		if(!$this->arr_input['status']){
			$this->arr_input['status'] = 'unpaid';
		}
		if($this->arr_input['status'] == 'void' || $this->arr_input['status'] == 'paid'){
			$where = 'WHERE `invoice`.`invoice_status` = ?';
			$params_array = array();
			$params_array[] = $this->arr_input['status'];
		} else if($this->arr_input['status'] == 'unpaid'){
			$where = 'WHERE (`invoice`.`invoice_status` = ? OR `invoice`.`invoice_status` = ?)';
			$params_array = array();
			$params_array[] = 'pending';
			$params_array[] = 'outstanding';
		} else {
			$where = '';
			$params_array = array();
		}
		
		if($this->arr_input['q']){
			if($where){
				$where .= ' AND ';
			} else {
				$where .= ' WHERE ';
			}
			$where .= '`invoice`.`invoice_to_company` LIKE ?';
			$params_array[] = '%' . trim($this->arr_input['q']) . '%';
		}
		
		$order = '`invoice`.`invoice_date`';
		$orderlist->getList($where , $params_array , $order , $this->arr_input['page']);
		$this->results = $orderlist->resultset;
		$this->pagination = $orderlist->pagination;
	}

}

?>