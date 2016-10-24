<?php

class DeletecontactController extends InvController {

	public $product;		// Ponter to product object

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 1;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Delete Contact';
		$this->view_area = 'contacts';
	}
	
	protected function setInputValues(){
		$this->setInputValue('confirm');
	}
	
	public function doController(){
		$this->contact = $this->obj_db->getFileModel('contact');
		// Creating a new product
		if(isset($this->path_info[0]) && $this->contact->open($this->path_info[0])){
			$this->view_title = $this->contact->filedata['contact_company'];
			$this->submit_action = '/admin/contact/' . $this->contact->id;
			// Are we saving some data
			if($this->arr_input['confirm']){
				//if($this->contact->delete()){
				$this->contact->updateValue('contact_status' , 'deleted');
				if($this->contact->save()){
					redirectRequest('/admin/contacts/');
				}
			}
		} else {
			$this->show404();
		}
	}
	
	protected function getWhereAnd($where , $new_bit){
		if($where){
			$where .= ' AND ' . $new_bit;
		} else {
			$where = 'WHERE ' . $new_bit;
		}
		return $where;
	}

}

?>