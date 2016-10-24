<?php

class ContactsearchAjaxController extends InvAjaxController{

	public $results;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
	}
	
	protected function setInputValues(){
	
		$this->setInputValue('q');
		
	}
	
	public function doController(){
		
		$query = 'SELECT `contact_id` , 
						 `contact_company` ,
						 `contact_firstname` ,
						 `contact_lastname`
					FROM `contact` 
				   WHERE `contact_company` LIKE ?
				      OR `contact_firstname` LIKE ?
				      OR `contact_lastname` LIKE ?
				   LIMIT 5';
		$params_array = array (
			$this->arr_input['q'] . '%' ,
			$this->arr_input['q'] . '%' ,
			$this->arr_input['q'] . '%'
		);
		
		echo '<p><a href="#" onClick="newContact()" class="add_new_customer">Add New Contact</a></p>';
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			for($i=0; $i<count($this->obj_db->result_set); $i++) {
				$result_row = $this->obj_db->result_set[$i];
				echo '<p>';
				echo '<a href="#" ';
				echo 'onClick="getContactDetails(\'' . $result_row['contact_id'] . '\')"';
				echo '>';
				if($result_row['contact_company']){
					echo $result_row['contact_company'];
				} else {
					echo trim($result_row['contact_firstname'] . ' ' . $result_row['contact_lastname']);
				}
				echo '</a>';
				echo '</p>';
			}
		} else {
			return false;
		}
		
		
	}

}

?>