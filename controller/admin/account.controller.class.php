<?php

class AccountController extends InvController{

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'User';
		$this->view_area = 'account';
		
		$this->countrycodes = $this->obj_countrycodes->getCountryCodes();
	}
	
	protected function setInputValues(){
		$this->setInputValue('save');
	}
	
	public function doController(){
	
		$this->account = $this->obj_db->getFileModel('account');
		$accountdata = $this->obj_session->getValue('accountdata');
		if($this->account->open($accountdata['id'])){
			$this->submit_action = '/admin/account/' . $this->account->id;
			// Are we saving some data
			if($this->arr_input['save']){
				$this->account->updateFromFormData();
				if($this->account->save()){
					$this->saved = true;
				} else {
					$this->saved = false;
				}
			}
			$this->view_title = '';
			if($this->account->filedata['account_company']){
				$this->view_title = $this->account->filedata['account_company'];
				if($this->account->filedata['account_firstname']){
					$this->view_title .= ' - ';
				}
			}
			$this->view_title .= $this->account->filedata['account_firstname'] . ' ' . $this->account->filedata['account_lastname'];
			$this->view_title = trim($this->view_title);
		} else {
			$this->show404();
		}
		
	}

}

?>