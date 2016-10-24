<?php



class InvRepeatinvoiceFile extends InvFile {
	
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
				// Set up the current saved state in the object for building the update statement later
				$this->current_saved_filedata = $this->filedata;
				// Primary key for DB record
				$this->id = $this->obj_db->result_set[0][$this->config->pri_key];
				// Get the line items
				$this->getItems();
				return true;
			} 
		} else {
			//Something went wrong;
		}
	}
	
	public function create(){
		parent::create();
		$this->filedata['repeatinvoice_status'] = 'active';
		$this->filedata['next_date'] = date('Y-m-d');
		$this->filedata['repeat_period'] = 'annually';
		$this->filedata['contact_id'] = '';
		$this->filedata['contact_company'] = '';
		$this->filedata['contact_address1'] = '';
		$this->filedata['contact_address2'] = '';
		$this->filedata['contact_address3'] = '';
		$this->filedata['contact_address4'] = '';
		$this->filedata['contact_address5'] = '';
		$this->filedata['contact_postcode'] = '';
		$this->filedata['country_code'] = '';
		$this->filedata['contact_vatnumber'] = '';
		return true;
	}
	
	public function save(){
		// Make pending one oustanding
		if($this->filedata['repeatinvoice_status'] == 'pending'){
			$this->filedata['repeatinvoice_status'] = 'outstanding';
		}
		if($invoice_id = parent::save()){
			$this->resetItems();
			return $invoice_id;
		} else {
			// Something went wrong.
		}
	}
	
	// Get all cart items
	public function getItems(){
		$this->items = array();
		$query = 'SELECT `' . $this->items_table_name . '`.*  FROM `' . $this->items_table_name . '`';
		$query .= ' WHERE `'. $this->items_table_name . '`.`' . $this->pri_key_name . '` = ?';
		$params_array = array($this->id);
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			if(count($this->obj_db->result_set) > 0){
				// Only open the file if one result and ONE RESULT ONLY is found
				$this->items = $this->obj_db->result_set;
				$this->getTotals();
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function getTotals(){
		$this->fieldata['repeatinvoice_total'] = 0;
		foreach($this->items as $item){
			$this->fieldata['repeatinvoice_total'] += ($item['unit_price'] * $item['qty']);
		}
	}
	
	public function addItem($sku , $description , $qty , $unit_price){
		if($qty > 0){
			$item_row = array();
			$item_row['sku'] = $sku;
			$item_row['description'] = $description;
			$item_row['qty'] = $qty;
			$item_row['unit_price'] = $unit_price;
			$this->items[] = $item_row;
			return true;
		} else {
			return false;
		}
	}
	
	public function removeItem($item_id){
		// Remove Row
		$query = 'DELETE FROM `' . $this->items_table_name . '` WHERE `' . $this->pri_key_name . '` = ? AND `' . $this->item_key_name . '` = ?';
		$params_array = array(
			$this->id ,
			$item_id
		);
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			$this->getItems();
			return true;
		} else {
			return false;
		}
	}
	
	public function resetItems(){
		// Remove Row
		$query = 'DELETE FROM `' . $this->items_table_name . '` WHERE `' . $this->pri_key_name . '` = ?';
		$params_array = array(
			$this->id
		);
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			$query = 'INSERT INTO `' . $this->items_table_name . '` (`' . $this->pri_key_name . '` , `sku` , `qty` , `description` , `unit_price`)
							 VALUES ( ? , ? , ? , ? , ? )';
			for($i=0; $i<count($this->items); $i++){
				// The insert the new row
				$params_array = array(
					$this->id ,
					$this->items[$i]['sku'] ,
					$this->items[$i]['qty'] ,
					$this->items[$i]['description'] ,
					$this->items[$i]['unit_price']
				);
				$this->obj_db->prepareAndDoQuery($query , $params_array);
			}
			$this->getItems();
			return true;
		} else {
			return false;
		}
	}
	
	//update cart details
	public function updateDetail($name , $value){
	
		$this->details[$name] = $value;
	
	}
	
	public function updateFromFormData(){
		parent::updateFromFormData();
		// this to bodged the joined data into the object for display after save purposes
		$this->filedata['contact_company'] = $_POST['invoice_to_company'];
		$this->filedata['contact_address1'] = $_POST['invoice_to_address1'];
		$this->filedata['contact_address2'] = $_POST['invoice_to_address2'];
		$this->filedata['contact_address3'] = $_POST['invoice_to_address3'];
		$this->filedata['contact_address4'] = $_POST['invoice_to_address4'];
		$this->filedata['contact_address5'] = $_POST['invoice_to_address5'];
		$this->filedata['contact_postcode'] = $_POST['invoice_to_postcode'];
		$this->filedata['country_code'] = $_POST['invoice_to_country_code'];
		$this->filedata['contact_vatnumber'] = $_POST['invoice_to_vatnumber'];
		// Blank the items array
		$this->items = array();
		//Do we need to create a contact first
		if($this->is_new && !$_POST['contact_id']){
			$_POST['contact_company'] = $_POST['invoice_to_company'];
			$_POST['contact_address1'] = $_POST['invoice_to_address1'];
			$_POST['contact_address2'] = $_POST['invoice_to_address2'];
			$_POST['contact_address3'] = $_POST['invoice_to_address3'];
			$_POST['contact_address4'] = $_POST['invoice_to_address4'];
			$_POST['contact_address5'] = $_POST['invoice_to_address5'];
			$_POST['contact_postcode'] = $_POST['invoice_to_postcode'];
			$_POST['country_code'] = $_POST['invoice_to_country_code'];
			$_POST['contact_vatnumber'] = $_POST['invoice_to_vatnumber'];
			$this->contact = $this->obj_db->getFileModel('contact');
			$this->contact->create();
			$this->contact->updateFromFormData();
			$this->filedata['contact_id'] = $this->contact->save();
		}
		// Update the rows.
		reset($_POST);
		while(list($key , $value) = each($_POST)){
			if(substr($key , 0 , 8) == 'itemkey_'){
				$line_number = substr($key , 8 , 5);
				if($_POST['itemkey_' . $line_number] && $_POST['item_' . $line_number . '_total'] > 0){
					$this->addItem(
						'' ,
						$_POST['itemkey_' . $line_number] ,
						1 ,
						$_POST['item_' . $line_number . '_total'] 
					);
				}
			}
		}
		$this->getTotals();

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
			
			$this->details[$this->header_table_name . '_status'] = $status;
			return true;
			
		} else {
			return false;
		}
		
	}
	
	public function getGlobalTaxRate(){
		return $this->obj_tax->calcTax(1 , $this->filedata['taxcode_id']);
	
	}
	
	
	public function setNextDate(){
	
		$query = 'UPDATE `' . $this->config->table . '` ';
		$params_array = array();
		switch($this->filedata['repeat_period']) {
			case 'weekly':
				$query .= 'SET `next_date` = DATE_ADD(`next_date` , INTERVAL 1 WEEK)';
				break;
			case 'monthly':
				$query .= 'SET `next_date` = DATE_ADD(`next_date` , INTERVAL 1 MONTH)';
				break;
			case 'quarterly':
				$query .= 'SET `next_date` = DATE_ADD(`next_date` , INTERVAL 3 MONTH)';
				break;
			case 'annually':
				$query .= 'SET `next_date` = DATE_ADD(`next_date` , INTERVAL 1 YEAR)';
				break;
		}
		
		if($query){
			$query .= '  WHERE `' . $this->config->pri_key . '` = ?';
			$params_array[] = $this->id;
			$this->obj_db->prepareAndDoQuery($query , $params_array);
		}
	
	}
	
	
	public function createInvoice($accountdata){
	
		$invoice = $this->obj_db->getFileModel('invoice');
		if($invoice->create($accountdata)) {
			//Update invoice to
			$invoice->updateValue('contact_id' , $this->filedata['contact_id']);
			$invoice->updateValue('invoice_to_company' , $this->filedata['contact_company']);
			$invoice->updateValue('invoice_to_address1' , $this->filedata['contact_address1']);
			$invoice->updateValue('invoice_to_address2' , $this->filedata['contact_address2']);
			$invoice->updateValue('invoice_to_address3' , $this->filedata['contact_address3']);
			$invoice->updateValue('invoice_to_address4' , $this->filedata['contact_address4']);
			$invoice->updateValue('invoice_to_address5' , $this->filedata['contact_address5']);
			$invoice->updateValue('invoice_to_postcode' , $this->filedata['contact_postcode']);
			$invoice->updateValue('invoice_to_country_code' , $this->filedata['country_code']);
			$invoice->updateValue('invoice_to_vatnumber' , $this->filedata['contact_vatnumber']);
			// Update line items
			foreach($this->items as $item){
				$invoice->addItem(	
					$item['sku'] , 
					$item['description'] , 
					$item['qty'] , 
					$item['unit_price'] 
				);
			}
			$invoice->getTotals();
			if($id = $invoice->save()){
				$this->updateValue('last_invoice_id' , $id);
				$this->updateValue('next_date' , $invoice->filedata['invoice_date']);
				$this->save();
				$this->setNextDate();
				return $id;
			} else {
				return false;
			}
		} else {
			return false;
		}
	
	}
	

}


?>