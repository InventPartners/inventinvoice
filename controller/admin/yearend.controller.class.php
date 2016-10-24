<?php

class YearendController extends InvController{

	public $manufacturers;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Year end';
		$this->view_area = 'reports';
	}
	
	protected function setInputValues(){
		$this->setInputValue('yearend_from_d');
		$this->setInputValue('yearend_from_m');
		$this->setInputValue('yearend_from_y');
		$this->setInputValue('yearend_to_d');
		$this->setInputValue('yearend_to_m');
		$this->setInputValue('yearend_to_y');
		$this->setInputValue('dataset');
		$this->setInputValue('pdf');
	}
	
	public function doController(){
	
		//Fetching data?
		if($this->arr_input['yearend_from_d'] && $this->arr_input['yearend_from_m'] && $this->arr_input['yearend_from_y'] && $this->arr_input['yearend_to_d'] && $this->arr_input['yearend_to_m'] && $this->arr_input['yearend_to_y']){
		
			if($this->arr_input['dataset']) {
		
				if($this->arr_input['dataset'] == 'purchase') {
				
					$list = $this->obj_db->getListModel('purchase');
					
					$where = 'WHERE `purchase`.`purchase_status` != \'void\'';
					$where .= 'AND `purchase`.`purchase_date` > ? AND `purchase`.`purchase_date` <= ?';
				
					$order = '`purchase`.`purchase_date`';
					
				} else if($this->arr_input['dataset'] == 'invoice') {
				
					$list = $this->obj_db->getListModel('invoice');
					
					$where = 'WHERE `invoice`.`invoice_status` != \'void\'';
					$where .= 'AND `invoice`.`invoice_date` > ? AND `invoice`.`invoice_date` <= ?';
				
					$order = '`invoice`.`invoice_date`';
					
				} else if($this->arr_input['dataset'] == 'purchaseevent') {
				
					$list = $this->obj_db->getListModel('purchaseevent');
					
					$where = 'WHERE `purchaselog`.`purchaselog_status` != \'void\'';
					$where .= 'AND `purchaselog`.`purchaselog_date` > ? AND `purchaselog`.`purchaselog_date` <= ?';
				
					$order = '`purchaselog`.`purchaselog_date`';
					
				} else if($this->arr_input['dataset'] == 'invoiceevent') {
				
					$list = $this->obj_db->getListModel('invoiceevent');
					
					$where = 'WHERE `invoicelog`.`invoicelog_status` != \'void\'';
					$where .= 'AND `invoicelog`.`invoicelog_date` > ? AND `invoicelog`.`invoicelog_date` <= ?';
				
					$order = '`invoice`.`invoice_id` , `invoicelog`.`invoicelog_date`';
					
				}
				
				$list->num_results_per_page = 20000;
				
				$from = mktime(0, 0, 0, date($this->arr_input['yearend_from_m']), date($this->arr_input['yearend_from_d']),   date($this->arr_input['yearend_from_y']));
				$to = mktime(0, 0, 0, date($this->arr_input['yearend_to_m']), date($this->arr_input['yearend_to_d']),   date($this->arr_input['yearend_to_y'])-1);
				
				$params_array = array();
				$params_array[] = date('Y-m-d' , $from);
				$params_array[] = date('Y-m-d' , $to);
				
				$list->getList($where , $params_array , $order, 1);
				//$this->results = $orderlist->resultset;
				//$this->pagination = $orderlist->pagination;
				
				
				if($this->arr_input['pdf']){
					foreach($list->resultset as $result){
						$file = $this->obj_db->getFileModel('invoice');
						$file->open($result['invoice_id']);
						$fp = fopen(TMP_PATH . $result['invoice_id'] . '.pdf' , 'w');
						fwrite($fp , $file->getPDFInvoice());
						fclose($fp);
					}
				} else {
					$list->exportCSV();
				}
			
			}
			
		} else {
		
			$this->arr_input['yearend_from_d'] = date('d');
			$this->arr_input['yearend_from_m'] = date('m'); 
			$this->arr_input['yearend_from_y'] = date('Y') - 1;
			$this->arr_input['yearend_to_d'] = date('d');
			$this->arr_input['yearend_to_m'] = date('m'); 
			$this->arr_input['yearend_to_y'] = date('Y');
		
		}
		
		
	}

}

?>