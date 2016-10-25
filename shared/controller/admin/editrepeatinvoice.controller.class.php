<?php

class EditrepeatinvoiceController extends InvController {

	public $product;		// Ponter to product object

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Repeat Invoice';
		$this->view_area = 'invoices';
		
		$this->repeat_options = array();
		$this->repeat_options[] = array('value' => 'weekly' , 'caption' => 'Weekly');
		$this->repeat_options[] = array('value' => 'monthly' , 'caption' => 'Monthly');
		$this->repeat_options[] = array('value' => 'quarterly' , 'caption' => 'Quarterly');
		$this->repeat_options[] = array('value' => 'annually' , 'caption' => 'Annually');
		
	}
	
	protected function setInputValues(){
		$this->setInputValue('save');
		$this->setInputValue('repeat');
	}
	
	public function doController(){
		$this->repeatinvoice = $this->obj_db->getFileModel('repeatinvoice');
		// Creating a new invoice
		if($this->path_info[0] == 'new'){
			$this->repeatinvoice->create();
			$this->submit_action = '/admin/editrepeatinvoice/new/';
			if($this->arr_input['save']){
				$this->repeatinvoice->updateFromFormData();
				if($this->arr_input['save']){
					if($new_id = $this->repeatinvoice->save()){
						redirectRequest('/admin/editrepeatinvoice/' . $new_id);
					} else {
						$this->saved = false;
					}
				}
			}
		} else {
			// Opening an existing invoice
			if($this->repeatinvoice->open($this->path_info[0])){
				$this->submit_action = '/admin/editrepeatinvoice/' . $this->repeatinvoice->id;
				// Are we saving some data
				if($this->arr_input['save']){
					$this->repeatinvoice->updateFromFormData();
					if($this->repeatinvoice->save()){
						$this->saved = true;
					} else {
						$this->saved = false;
					}
					if($this->arr_input['repeat']){
						if($invoice_id = $this->repeatinvoice->createInvoice($this->obj_session->getValue('accountdata'))){
							redirectRequest('/admin/invoice/' . $invoice_id);
						}
					}
				}
			} else {
				$this->show404();
			}
		}
	}

}

?>