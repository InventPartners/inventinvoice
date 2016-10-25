<?php

class DefaultController extends InvController {

	public $product;		// Ponter to product object

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Product';
		$this->view_area = 'default';
	}
	
	protected function setInputValues(){
		
	}
	
	public function doController(){
	
		$repeatinvoices = $this->obj_db->getListModel('repeatinvoice');
		$repeatinvoices->getRepeatDueInvoices(true);
		$result_row = $this->obj_db->result_set[0];
		$this->repeatinvoicesdue = $result_row['count'];
		
	}

}

?>