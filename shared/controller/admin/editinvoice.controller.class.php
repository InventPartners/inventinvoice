<?php

class EditinvoiceController extends InvController {

	public $product;		// Ponter to product object

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Invoice';
		$this->view_area = 'invoices';
		
		$this->countrycodes = $this->obj_countrycodes->getCountryCodes();
	}
	
	protected function setInputValues(){
		$this->setInputValue('save');
		$this->setInputValue('copy');
		$this->setInputValue('repeat');
		$this->setInputValue('pdf');
	}
	
	public function doController(){
		$this->invoice = $this->obj_db->getFileModel('invoice');
		// Creating a new invoice
		if($this->path_info[0] == 'new'){
			$this->invoice->create();
			$this->submit_action = '/admin/editinvoice/new/';
			if($this->arr_input['save']){
				$this->invoice->updateFromFormData();
				if($this->arr_input['save']){
					if($new_id = $this->invoice->save()){
						redirectRequest('/admin/editinvoice/' . $new_id);
					} else {
						$this->saved = false;
					}
				}
			}
		} else {
			// Opening an existing invoice
			if($this->invoice->open($this->path_info[0])){
				// Are we copying it?
				if($this->arr_input['copy']){
					if($this->invoice->copy()){
						// Copy suceeded
					} else {
						// Copy failed
					}
				}
				$this->submit_action = '/admin/editinvoice/' . $this->invoice->id;
				// Are we saving some data
				if($this->arr_input['save']){
					$this->invoice->updateFromFormData();
					if($this->invoice->save()){
						$this->saved = true;
					} else {
						$this->saved = false;
					}
					if($this->arr_input['repeat']){
						if($repeatinvoice_id = $this->invoice->repeatInvoice()){
							redirectRequest('/admin/editrepeatinvoice/' . $repeatinvoice_id);
						}
					}
					if($this->arr_input['pdf']){
						redirectRequest('/admin/invoice/' . $this->invoice->id . '/' . $this->invoice->id . '.pdf');
					}
				}
			} else {
				$this->show404();
			}
		}
	}

}

?>