<?php

class ContactController extends InvController{

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Contact';
		$this->view_area = 'contacts';
		
		$this->countrycodes = $this->obj_countrycodes->getCountryCodes();
	}
	
	protected function setInputValues(){
		$this->setInputValue('save');
	}
	
	public function doController(){
		$this->contact = $this->obj_db->getFileModel('contact');
		// Creating a new product
		if($this->path_info[0] == 'new'){
			$this->contact->create();
			$this->submit_action = '/admin/contact/new/';
			$this->contact->updateFromFormData();
			if($this->arr_input['save']){
				if($new_id = $this->contact->save()){
					redirectRequest('/admin/contact/' . $new_id);
				} else {
					$this->saved = false;
				}
			}
		} else {
			if($this->contact->open($this->path_info[0])){
				$this->submit_action = '/admin/contact/' . $this->contact->id;
				// Are we saving some data
				if($this->arr_input['save']){
					$this->contact->updateFromFormData();
					if($this->contact->save()){
						$this->saved = true;
					} else {
						$this->saved = false;
					}
				}
				$this->view_title = '';
				if($this->contact->filedata['contact_company']){
					$this->view_title = $this->contact->filedata['contact_company'];
					if($this->contact->filedata['contact_firstname']){
						$this->view_title .= ' - ';
					}
				}
				$this->view_title .= $this->contact->filedata['contact_firstname'] . ' ' . $this->contact->filedata['contact_lastname'];
				$this->view_title = trim($this->view_title);
			} else {
				$this->show404();
			}
		}
		
	}

}

?>