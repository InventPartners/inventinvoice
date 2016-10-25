<?php

class ContactsController extends InvController{

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Contacts';
		$this->view_area = 'contacts';
	}
	
	protected function setInputValues(){
		$this->setInputValue('q' , true);
		$this->setInputValue('page' , true);
	}
	
	public function doController(){
		$contacts = $this->obj_db->getListModel('contact');
		$where = 'WHERE `contact`.`contact_status` = ?';
		$params_array = array(0 => 'active');
		
		if($this->arr_input['q']){
			$where .= ' AND ( `contact`.`contact_firstname` LIKE ? OR `contact`.`contact_lastname` LIKE ? OR `contact`.`contact_company` LIKE ?) ';
			$params_array[] = '%' . trim($this->arr_input['q']) . '%';
			$params_array[] = '%' . trim($this->arr_input['q']) . '%';
			$params_array[] = '%' . trim($this->arr_input['q']) . '%';
		}
		
		$order = '`contact`.`contact_company` , `contact`.`contact_lastname`';
		$contacts->getList($where , $params_array , $order , $this->arr_input['page']);
		$this->results = $contacts->resultset;
		$this->pagination = $contacts->pagination;
	}

}

?>