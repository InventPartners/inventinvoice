<?php



class InvPurchaseFile extends InvFile {
	
	var $header_table_name;
	
	var $items_table_name;
	var $pri_key_name;
	var $item_key_name;

	var $obj_tax;
	
	var $items = array();
	
	public function __construct($config , $obj_db){
		parent::__construct($config , $obj_db);
	
		$this->header_table_name = $config->table;
		
		$this->items_table_name = $this->header_table_name . 'item';
		$this->pri_key_name = $this->header_table_name . '_id';
		$this->item_key_name = $this->header_table_name . 'item_id';
		
		/// Instantiate tax class
		require_once(MODEL_PATH . 'tax.class.php');
		$this->obj_tax = new InvTax($this->obj_db);
		
		return true;
	
	}
	
	public function open($id){
		$this->is_new = false;
		$query = 'SELECT * FROM `' . $this->config->table . '` ';
		if($this->config->joined_tables){
			$query .= ' , ' . $this->config->joined_tables;
		}
		
		$query .= ' WHERE `' . $this->config->table . '`.`' . $this->config->pri_key . '` = ?';
		if($this->config->join_statement){
			$query .= ' AND ' . $this->config->join_statement;
		}
		$params_array = array($id);
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			if(count($this->obj_db->result_set) >= 1){
				// Only open the file if one result and ONE RESULT ONLY is found
				$this->filedata = $this->obj_db->result_set[0];
				$this->filedata[$this->header_table_name . '_total_inc'] = $this->filedata[$this->header_table_name . '_total'] + $this->filedata[$this->header_table_name . '_tax'];
				$this->current_saved_filedata = $this->filedata;
				$this->id = $this->obj_db->result_set[0][$this->config->pri_key];
				return true;
			} 
		} else {
			//Something went wrong;
		}
	}
	
	public function create($accountdata){
		parent::create();
		$this->filedata['taxcode_id'] = 1;
		$this->filedata['purchase_to_company'] = $accountdata['account_company'];
		$this->filedata['purchase_to_address1'] = $accountdata['account_address1'];
		$this->filedata['purchase_to_address2'] = $accountdata['account_address2'];
		$this->filedata['purchase_to_address3'] = $accountdata['account_address3'];
		$this->filedata['purchase_to_address4'] = $accountdata['account_address4'];
		$this->filedata['purchase_to_address5'] = $accountdata['account_address5'];
		$this->filedata['purchase_to_postcode'] = $accountdata['account_postcode'];
		$this->filedata['purchase_to_country_code'] = $accountdata['country_code'];
		$this->filedata['purchase_to_vatnumber'] = $accountdata['account_vatnumber'];
		$this->filedata['purchase_date'] = date('Y-m-d');
		$this->filedata['purchase_status'] = 'outstanding';
		$this->filedata['purchase_total'] = 0;
		$this->filedata['purchase_tax'] = 0;
	}
	
	public function save(){
		if($purchase_id = parent::save()){
			return $purchase_id;
		} else {
			// Something went wrong.
		}
	}
	
	/*
	public function getTotals(){
		$this->filedata[$this->header_table_name . '_total'] = 0;
		$this->filedata[$this->header_table_name . '_tax'] = 0;
		for($i=0; $i<count($this->items); $i++){
			// Line tax
			$this->items[$i]['line_total_tax'] = $this->obj_tax->calcTax($this->items[$i]['line_total'] , 0);
			// Total balance and total tax
			$this->filedata[$this->header_table_name . '_total'] += $this->items[$i]['line_total'];
			$this->filedata[$this->header_table_name . '_tax'] += $this->items[$i]['line_total_tax'];
			// Check that the running tax total from line items
			// is equal to the tax cacluated from the order total
			// if it doesn't add up, we need to make a line_item tax adjustment
			// this is mathematics as performed by politicians. Tax law.
			$tax_differential = $this->filedata[$this->header_table_name . '_tax'] - $this->obj_tax->calcTax($this->filedata[$this->header_table_name . '_total'] , 0);
			if($tax_differential != 0){
				$this->items[$i]['line_total_tax'] += $tax_differential;
			}
			// Line tax inc
			$this->items[$i]['line_total_inc'] = $this->items[$i]['line_total'] + $this->items[$i]['line_total_tax'];
			// Total shipping
		}
		// Total balance inc
		$this->filedata[$this->header_table_name . '_total_inc'] = $this->filedata[$this->header_table_name . '_total'] + $this->filedata[$this->header_table_name . '_tax'];
		return true;
	}
	
	//update cart details
	public function updateDetail($name , $value){
	
		$this->details[$name] = $value;
	
	}
	*/
	
	public function updateFromFormData(){
		/*
		if(!isset($_POST['purchase_total']) || !$_POST['purchase_total']){
			$this->updateValue('purchase_total' , 0);
		}
		if(!isset($_POST['purchase_tax']) || !$_POST['purchase_tax']){
			$this->updateValue('purchase_tax' , 0);
		}
		*/
		parent::updateFromFormData();
		$this->items = array();
		//Do we need to create a contact first
		if($this->is_new && !$_POST['contact_id']){
			$_POST['contact_company'] = $_POST['purchase_from_company'];
			$_POST['contact_address1'] = $_POST['purchase_from_address1'];
			$_POST['contact_address2'] = $_POST['purchase_from_address2'];
			$_POST['contact_address3'] = $_POST['purchase_from_address3'];
			$_POST['contact_address4'] = $_POST['purchase_from_address4'];
			$_POST['contact_address5'] = $_POST['purchase_from_address5'];
			$_POST['contact_postcode'] = $_POST['purchase_from_postcode'];
			$_POST['country_code'] = $_POST['purchase_from_country_code'];
			$_POST['contact_vatnumber'] = $_POST['purchase_from_vatnumber'];
			$this->contact = $this->obj_db->getFileModel('contact');
			$this->contact->create();
			$this->contact->updateFromFormData();
			$this->filedata['contact_id'] = $this->contact->save();
		}
		return true;
	}
	
	public function setStatus($status){
	
		$query = 'UPDATE `' . $this->header_table_name . '` 
				  SET `' . $this->header_table_name . '_status` = ?
				  WHERE `' . $this->header_table_name . '`.`' . $this->header_table_name . '_id` = ?
				  ';
		$params_array = array();
		$params_array[] = $status;
		$params_array[] = $this->id;
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
		
			// Update status logs
			$query = 'INSERT INTO `' . $this->header_table_name . 'log`
					  ( 
						`' . $this->header_table_name . 'log`.`' . $this->header_table_name . '_id` ,
						`' . $this->header_table_name . 'log`.`' . $this->header_table_name . 'log_status` ,
						`' . $this->header_table_name . 'log`.`' . $this->header_table_name . 'log_date`
					  )
					  VALUES (
						? ,
						? ,
						NOW()
					  )';
			$params_array = array();
			$params_array[] = $this->id;
			$params_array[] = $status;
			$this->obj_db->prepareAndDoQuery($query , $params_array);
		
			// Update payment logs
			if($status == 'paid') {
				$query = 'INSERT INTO `' . $this->header_table_name . 'payment`
						  ( 
							`' . $this->header_table_name . 'payment`.`' . $this->header_table_name . '_id` ,
							`' . $this->header_table_name . 'payment`.`' . $this->header_table_name . 'payment_amount` ,
							`' . $this->header_table_name . 'payment`.`' . $this->header_table_name . 'payment_date`
						  )
						  VALUES (
							? ,
							? ,
							NOW()
						  )';
				$params_array = array();
				$params_array[] = $this->id;
				$params_array[] = $this->filedata['purchase_total'];
				$this->obj_db->prepareAndDoQuery($query , $params_array);
			}
			
			$this->details[$this->header_table_name . '_status'] = $status;
			return true;
			
		} else {
			return false;
		}
		
	}
	
	

}


?>