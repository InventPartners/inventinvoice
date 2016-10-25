<?php

class UserController extends InvController{

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'User';
		$this->view_area = 'account';
	}
	
	protected function setInputValues(){
		$this->setInputValue('update');
		$this->setInputValue('username');
		$this->setInputValue('curpassword');
		$this->setInputValue('newpassword');
		$this->setInputValue('confirmnewpassword');
	}
	
	public function doController(){
	
		$this->username_updated = false;
		$this->password_updated = false;
		$this->password_error = false;
		
		$this->contact = $this->obj_session->obj_db->getFileModel('user');
		$userdata = $this->obj_session->getValue('userdata');
		if($this->contact->open($userdata['user_id'])) {
			$this->submit_action = '/admin/contact/' . $this->contact->filedata['user_id'];
			
			// Username
			if($this->arr_input['update'] == 'username' && $this->arr_input['username'] != $this->contact->filedata['username']) {
				$this->contact->updateValue('username' , $this->arr_input['username']);
				if($this->contact->save()){
					// Username changed - update the session;
					$this->obj_session->setValue('username' , $this->arr_input['username']);
					$this->username_updated = true;
				}
			}
			
			// Password
			if($this->arr_input['update'] == 'password') {
				if($this->obj_session->checkEnteredPassword($this->arr_input['curpassword'])) {
					if($this->arr_input['newpassword'] == $this->arr_input['confirmnewpassword']){
						if($this->contact->updatePassword($this->arr_input['newpassword'])) {
							// Username changed - update the session;
							$this->obj_session->setValue('password' , $this->arr_input['newpassword']);
							$this->password_updated = true;
						}
					} else {
						$this->password_error = 'Your new password and confirm new password did not match. Please try again.';
					}
				} else {
					$this->password_error = 'Current password incorrect. Please try again.';
				}
			}
			
		} else {
			$this->show404();
		}
		
	}

}

?>