<?php



class InvInvoiceFile extends InvFile {
	
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
		$account = $this->obj_db->getFileModel('account');
		$account->open(1);
		$accountdata = $account->filedata;
		$this->filedata['taxcode_id'] = $accountdata['taxcode_id'];
		$this->filedata['invoice_from_company'] = $accountdata['account_company'];
		$this->filedata['invoice_from_address1'] = $accountdata['account_address1'];
		$this->filedata['invoice_from_address2'] = $accountdata['account_address2'];
		$this->filedata['invoice_from_address3'] = $accountdata['account_address3'];
		$this->filedata['invoice_from_address4'] = $accountdata['account_address4'];
		$this->filedata['invoice_from_address5'] = $accountdata['account_address5'];
		$this->filedata['invoice_from_postcode'] = $accountdata['account_postcode'];
		$this->filedata['invoice_from_country_code'] = $accountdata['country_code'];
		$this->filedata['invoice_from_vatnumber'] = $accountdata['account_vatnumber'];
		$this->filedata['invoice_date'] = date('Y-m-d');
		$this->filedata['invoice_status'] = 'pending';
		return true;
	}

	// Copy this invoice to a new ID
	public function copy(){
		$this->filedata['invoice_date'] = date('Y-m-d');
		return parent::copy();
	}
	
	public function save(){
		// Make pending one oustanding
		if($this->filedata['invoice_status'] == 'pending'){
			$this->filedata['invoice_status'] = 'outstanding';
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
		$taxable_balance = 0;
		$this->filedata[$this->header_table_name . '_total'] = 0;
		$this->filedata[$this->header_table_name . '_tax'] = 0;
		for($i=0; $i<count($this->items); $i++){
			// Line tax
			$this->items[$i]['line_total_tax'] = $this->obj_tax->calcTax($this->items[$i]['line_total'] , $this->items[$i]['taxcode_id']);
			// Only show taxable items against the taxable balance
			if($this->items[$i]['line_total_tax']){
				$taxable_balance += $this->items[$i]['line_total'];
			}
			// Total balance and total tax
			$this->filedata[$this->header_table_name . '_total'] += $this->items[$i]['line_total'];
			$this->filedata[$this->header_table_name . '_tax'] += $this->items[$i]['line_total_tax'];
			// Check that the running tax total from line items
			// is equal to the tax cacluated from the order total
			// if it doesn't add up, we need to make a line_item tax adjustment
			// this is mathematics as performed by politicians. Tax law.
			$tax_differential = $this->filedata[$this->header_table_name . '_tax'] - $this->obj_tax->calcTax($taxable_balance , $this->items[$i]['taxcode_id']);
			if($tax_differential != 0){
				//$this->items[$i]['line_total_tax'] += $tax_differential;
			}
			// Line tax inc
			$this->items[$i]['line_total_inc'] = $this->items[$i]['line_total'] + $this->items[$i]['line_total_tax'];
			// Total shipping
		}
		// Total balance inc
		$this->filedata[$this->header_table_name . '_total_inc'] = $this->filedata[$this->header_table_name . '_total'] + $this->filedata[$this->header_table_name . '_tax'];
		return true;
	}
	
	public function addItem($sku , $description , $qty , $unit_price , $taxcode_id = false){
		if($qty > 0){
			$item_row = array();
			$item_row['sku'] = $sku;
			$item_row['description'] = $description;
			$item_row['qty'] = $qty;
			$item_row['unit_price'] = $unit_price;
			$item_row['line_total'] = bcmul($item_row['qty'] , $item_row['unit_price'] , 2);
			if($taxcode_id){
				$item_row['taxcode_id'] = $taxcode_id;
			} else {
				$item_row['taxcode_id'] = $this->filedata['taxcode_id'];
			}
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
			$query = 'INSERT INTO `' . $this->items_table_name . '` (`' . $this->pri_key_name . '` , `sku` , `qty` , `description` , `unit_price` , `taxcode_id` , `line_total`)
							 VALUES ( ? , ? , ? , ? , ? , ? , ? )';
			for($i=0; $i<count($this->items); $i++){
				// The insert the new row
				$params_array = array(
					$this->id ,
					$this->items[$i]['sku'] ,
					$this->items[$i]['qty'] ,
					$this->items[$i]['description'] ,
					$this->items[$i]['unit_price'] ,
					$this->items[$i]['taxcode_id'] ,
					bcmul($this->items[$i]['unit_price'] , $this->items[$i]['qty'] , 2)
				);
				$this->obj_db->prepareAndDoQuery($query , $params_array);
			}
			$this->getItems();
			return true;
		} else {
			return false;
		}
	}
	
	public function updateFromFormData(){
		parent::updateFromFormData();
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
				if($_POST['itemkey_' . $line_number] && $_POST['item_' . $line_number . '_total'] != 0){
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
		
			if($status == 'paid'){
				$this->getTotals();
				$reconcilelog = $this->getPaymentReconcileLog();
				$balance = $this->filedata[$this->header_table_name . '_total_inc'] - $reconcilelog['amount'];
				$this->payment = $this->obj_db->getFileModel('payment');
				$this->payment->create();
				$this->payment->updateValue('contact_id' , $this->filedata['contact_id']);
				$this->payment->updateValue('payment_amount' , $balance);
				$this->payment->save();
				$this->payment->reconcileTo($this->id);
			}
			if($status == 'outstanding' || $status == 'void'){
				$this->clearPaymentReconcileLog();
			}		
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
				$params_array[] = $this->filedata['invoice_total'];
				$this->obj_db->prepareAndDoQuery($query , $params_array);
			}
			
			$this->details[$this->header_table_name . '_status'] = $status;
			return true;
			
		} else {
			return false;
		}
		
		
	}
	
	public function getGlobalTaxRate(){
		return $this->obj_tax->calcTax(1 , $this->filedata['taxcode_id']);
	
	}
	
	public function getPaymentReconcileLog(){
		$reconcile = array(
			'amount' =>  0,
			'log' => array()
		);
		if($this->id){
			$paymentreconcile = $this->obj_db->getListModel('paymentreconcile');
			$where = ' WHERE `paymentreconcile`.`invoice_id` = ' . $this->id;
			$order = '`paymentreconcile`.`updated`';
			$paymentreconcile->getList($where , array() , $order , 1);
			if(isset($paymentreconcile->resultset)){
				foreach($paymentreconcile->resultset as $item){
					$reconcile['amount'] += $item['paymentreconcile_amount'];
					$reconcile['log'][] = $item;
				}
			}
		}
		return $reconcile;
		
	}
	
	public function clearPaymentReconcileLog(){
	
		if($this->id){
			// What we'll do here is:
			// [1] Completely remove all payment records entirely allocated to this invoice
			$query = 'DELETE `payment` , 
							 `paymentreconcile` 
						FROM `payment` , 
							 `paymentreconcile` 
						WHERE `paymentreconcile`.`invoice_id` = ' . $this->id . ' 
						AND `paymentreconcile`.`payment_id` = `payment`.`payment_id`
						AND `paymentreconcile`.`paymentreconcile_amount` = `payment`.`payment_amount`';
			$this->obj_db->prepareAndDoQuery($query , array());
		
			// [2] Remove all paymentreconcile entries allocated to this invoice
			$query = 'DELETE FROM `paymentreconcile` 
						WHERE `paymentreconcile`.`invoice_id` = ' . $this->id;
			$this->obj_db->prepareAndDoQuery($query , array());
		}
		return true;
		
	}
	
	
	public function repeatInvoice(){
	
		$repeatinvoice = $this->obj_db->getFileModel('repeatinvoice');
		if($repeatinvoice->create()) {
			//Update invoice to
			$repeatinvoice->updateValue('contact_id' , $this->filedata['contact_id']);
			$repeatinvoice->updateValue('last_invoice_id' , $this->id);
			$repeatinvoice->updateValue('next_date' , $this->filedata['invoice_date']);
			// Update line items
			foreach($this->items as $item){
				$repeatinvoice->addItem(	
					$item['sku'] , 
					$item['description'] , 
					$item['qty'] , 
					$item['unit_price']
				);
			}
			$repeatinvoice->getTotals();
			if($id = $repeatinvoice->save()){
				$repeatinvoice->setNextDate();
				return $id;
			} else {
				return false;
			}
		} else {
			return false;
		}
	
	}
	
	
	public function getPDFInvoice(){
	
		require_once(MODEL_PATH . 'pdf/invoice.fpdf.class.php');
		$pdf = new PDFInvoice();
		//$pdf->loadTemplate();
		//print_r($this->invoice->items);
		$pdf->writeInvoice(
			$this->filedata, 
			$this->items
		);
		$pdf_invoice = $pdf->getOutput();
	
		return $pdf_invoice;
	
	}
	
	
	public function emailInvoice(){
	
		$pdf_invoice = $this->getPDFInvoice();
		
		$subject_line = 'Your Invoice';
		$headers = '';
		$email = 'matt@inventpartners.com';
	
		$mime_boundary="==Multipart_Boundary_x".md5(mt_rand())."x";
		$headers .= "MIME-Version: 1.0\n" .
				   "Content-Type: multipart/mixed;\n" .
				   " boundary=\"" . $mime_boundary . "\"";



		// Text bit
		$message_text = 'Your invoice is attached';
		
		// Boundaries and message body
		$message = "This is a multi-part message in MIME format.\n\n" .
				 "--{$mime_boundary}\n" .
				 "Content-Type: text/plain; charset=\"iso-8859-1\"\n" .
				 "Content-Transfer-Encoding: 7bit\n\n" .
				 $message_text . "\n\n";
		// Attachment
		$message .= "--{$mime_boundary}\n" .
							"Content-Type: pdf;\n" .
							" name=\"invoice_" . $this->filedata['invoice_id'] . ".pdf\"\n" .
							"Content-Disposition: attachment;\n" .
							" filename=\"invoice_" . $this->filedata['invoice_id'] . ".pdf\"\n" .
							"Content-Transfer-Encoding: base64\n\n" .
							chunk_split(base64_encode($pdf_invoice)) . "\n\n";

		// Closure after attachment
		$message .= "--{$mime_boundary}--\n";

		mail($email , $subject_line , $message , $headers);

		return true;

	
	}
	
	

}


?>