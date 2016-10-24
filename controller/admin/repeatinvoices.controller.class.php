<?php

class RepeatinvoicesController extends InvController{

	public $manufacturers;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Repeat Invoices';
		$this->view_area = 'invoices';
	}
	
	protected function setInputValues(){
		$this->setInputValue('page' , true);
		$this->setInputValue('status' , true);
		$this->setInputValue('repeatinvoice_start');
		$this->setInputValue('repeatinvoice_stop');
	}
	
	public function doController(){
	
		//Are we marking an order as processed?
		if($this->arr_input['repeatinvoice_start'] || $this->arr_input['repeatinvoice_stop']){
			$this->repeatinvoice = $this->obj_db->getFileModel('repeatinvoice');
			$order_found = false;
			if($this->arr_input['repeatinvoice_start']){
				if($this->repeatinvoice->open($this->arr_input['repeatinvoice_start'])){
					$this->repeatinvoice->setStatus('active');
				}
			} else if ($this->arr_input['repeatinvoice_stop']){
				if($this->repeatinvoice->open($this->arr_input['repeatinvoice_stop'])){
					$this->repeatinvoice->setStatus('stopped');
				}
			}
		}
	
		$orderlist = $this->obj_db->getListModel('repeatinvoice');
		if(!$this->arr_input['status']){
			$this->arr_input['status'] = 'active';
		}
		$where = '';
		$params_array = array();
		if($this->arr_input['status'] == 'active' || $this->arr_input['status'] == 'stopped'){
			$where = 'WHERE `repeatinvoice`.`repeatinvoice_status` = ?';
			$params_array = array();
			$params_array[] = $this->arr_input['status'];
		} else if ($this->arr_input['status'] == 'due'){
			$where = "
				WHERE `repeatinvoice`.`next_date` < NOW()
				AND `repeatinvoice`.`repeatinvoice_status` = 'active'
			";
		} else {
			$where = "WHERE `repeatinvoice`.`repeatinvoice_status` = 'active'";
		}
		$order = '`repeatinvoice`.`next_date`';
		$orderlist->getList($where , $params_array , $order , $this->arr_input['page']);
		$order = '`repeatinvoice`.`next_date`';
		$orderlist->getList($where , $params_array , $order , $this->arr_input['page']);
		$this->results = $orderlist->resultset;
		$this->pagination = $orderlist->pagination;
	}

}

?>