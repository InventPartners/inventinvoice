<?php

class PurchasesController extends InvController{

	public $manufacturers;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Purchases';
		$this->view_area = 'purchases';
	}
	
	protected function setInputValues(){
		$this->setInputValue('page' , true);
		$this->setInputValue('q' , true);
		$this->setInputValue('status' , true);
		$this->setInputValue('purchase_void');
		$this->setInputValue('purchase_paid');
		$this->setInputValue('purchase_unpaid');
		$this->setInputValue('processorder');
		$this->setInputValue('reopen');
	}
	
	public function doController(){
	
		//Are we marking an order as processed?
		if($this->arr_input['purchase_paid'] || $this->arr_input['purchase_unpaid'] || $this->arr_input['purchase_void']){
			$this->invoice = $this->obj_db->getFileModel('purchase');
			$order_found = false;
			if($this->arr_input['purchase_paid']){
				if($this->invoice->open($this->arr_input['purchase_paid'])){
					$this->invoice->setStatus('paid');
				}
			} else if ($this->arr_input['purchase_unpaid']){
				if($this->invoice->open($this->arr_input['purchase_unpaid'])){
					$this->invoice->setStatus('outstanding');
				}
			} else if ($this->arr_input['purchase_void']){
				if($this->invoice->open($this->arr_input['purchase_void'])){
					$this->invoice->setStatus('void');
				}
			}
		}
	
		$orderlist = $this->obj_db->getListModel('purchase');
		if(!$this->arr_input['status']){
			$this->arr_input['status'] = 'unpaid';
		}
		if($this->arr_input['status'] == 'void' || $this->arr_input['status'] == 'paid'){
			$where = 'WHERE `purchase`.`purchase_status` = ?';
			$params_array = array();
			$params_array[] = $this->arr_input['status'];
		} else if($this->arr_input['status'] == 'unpaid'){
			$where = 'WHERE (`purchase`.`purchase_status` = ? OR `purchase`.`purchase_status` = ?)';
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
			$where .= '`purchase`.`purchase_from_company` LIKE ?';
			$params_array[] = '%' . trim($this->arr_input['q']) . '%';
		}
		
		$order = '`purchase`.`purchase_date`';
		$orderlist->getList($where , $params_array , $order , $this->arr_input['page']);
		$this->results = $orderlist->resultset;
		$this->pagination = $orderlist->pagination;
	}

}

?>