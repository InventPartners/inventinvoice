<?php

class EditpurchaseController extends InvController {

	public $product;		// Ponter to product object

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Purchase';
		$this->view_area = 'purchases';
		
		$this->countrycodes = $this->obj_countrycodes->getCountryCodes();
	}
	
	protected function setInputValues(){
		$this->setInputValue('save');
	}
	
	public function doController(){
		$this->purchase = $this->obj_db->getFileModel('purchase');
		// Creating a new product
		if($this->path_info[0] == 'new'){
			$this->purchase->create();
			$this->submit_action = '/admin/editpurchase/new/';
			if($this->arr_input['save']){
				$this->purchase->updateFromFormData();
				if($this->arr_input['save']){
					if($new_id = $this->purchase->save()){
						redirectRequest('/admin/editpurchase/' . $new_id);
					} else {
						$this->saved = false;
					}
				}
			}
		} else {
			if($this->purchase->open($this->path_info[0])){
				$this->submit_action = '/admin/editpurchase/' . $this->purchase->id;
				// Are we saving some data
				if($this->arr_input['save']){
					$this->purchase->updateFromFormData();
					if($this->purchase->save()){
						$this->saved = true;
					} else {
						$this->saved = false;
					}
				}
			} else {
				$this->show404();
			}
		}
	}

}

?>