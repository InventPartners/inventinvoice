<?php


class InvSalesOrder extends InvCart {

	var $header_table_name = 'salesorder';
	
	public function __construct($obj_db , $obj_session){
		parent::__construct($obj_db , $obj_session);
		$this->checkCreateOrder();
		return true;
	}
	
	// Do we need to create a new sales order record
	public function checkCreateOrder(){
		if(!$this->id){
			$this->createOrder();
		}
	}
	
	// Create a new sales order record from a cart
	public function createOrder(){

		$query = 'INSERT INTO `salesorder` (
				`cart_id` ,
				`firstname` ,
				`lastname` ,
				`email` ,
				`tel` ,
				`address1` ,
				`address2` ,
				`address3` ,
				`county` ,
				`postcode` ,
				`country` ,
				`shippingband_id` ,
				`shippinginstructions` ,
				`howfound`
			)
			SELECT 
				`cart_id` ,
				`firstname` ,
				`lastname` ,
				`email` ,
				`tel` ,
				`address1` ,
				`address2` ,
				`address3` ,
				`county` ,
				`postcode` ,
				`country` ,
				`shippingband_id` ,
				`shippinginstructions` ,
				`howfound`
			FROM `cart`
			WHERE `cart`.`cart_id` = ?
		';
		$params_array = array($this->obj_session->getValue('cart_id'));
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			$this->id = $this->obj_db->lastInsertId();
			$this->obj_session->setValue($this->pri_key_name , $this->id);
			$query = 'INSERT INTO `salesorderitem` (
					`salesorder_id` ,
					`item_type` ,
					`item_id` ,
					`sku` ,
					`qty` ,
					`description` ,
					`unit_price` ,
					`line_total`
				)
				SELECT
					' . $this->id . ' ,
					`item_type` ,
					`item_id` ,
					`sku` ,
					`qty` ,
					`description` ,
					`unit_price` ,
					`line_total`
				FROM `cartitem`
				WHERE `cartitem`.`cart_id` = ?			
			';
			if($this->obj_db->prepareAndDoQuery($query , $params_array)){
				$this->getCart();
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}	
	}
	
	public function userCompleteOrder($payment_method){
		
		if($this->details['paymentstatus'] != 'completed'){
	
			$this->details['order_datetime'] = date('Y-m-d h:i:s');
	
			// Mark salesorder as completed and non-active
			$query = 'UPDATE `salesorder` 
					  SET `paymentstatus` = ? , 
						  `paymentmethod` = ? ,
						  `order_datetime` = ? ,
						  `active` = 0
					  WHERE `salesorder`.`salesorder_id` = ?
					  ';
			$params_array = array();
			$params_array[] = 'completed';
			$params_array[] = $payment_method;
			$params_array[] = $this->details['order_datetime'];
			$params_array[] = $this->id;
			if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			
				// Mark asscoiated cart as non - active
				$query = 'UPDATE `cart` 
					  		SET `active` = 0
					  WHERE `cart`.`cart_id` = ?';
				$params_array = array();
				$params_array[] = $this->details['cart_id'];
				$this->obj_db->prepareAndDoQuery($query , $params_array);
				
				// Update the session
				//$this->obj_session->setValue('lastcompletedorder' , $this->id);
				//$this->obj_session->setValue('cart_id' , '');
				//$this->obj_session->setValue('order_id' , '');
				
				return true;
				
			} else {
				return false;
			}
		
		}
		
	}
	
	public function userCancelOrder($payment_method){
	
		$query = 'UPDATE `salesorder` 
				  SET `paymentstatus` = ? , 
				      `paymentmethod` = ? ,
				      `order_datetime` = NOW() ,
				      `active` = 0
				  WHERE `salesorder`.`salesorder_id` = ?
				  ';
		$params_array = array();
		$params_array[] = 'cancelled';
		$params_array[] = $payment_method;
		$params_array[] = $this->id;
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			
			// Update the session
			//$this->obj_session->setValue('cart_id' , '');
			//$this->obj_session->setValue('order_id' , '');
			
			return true;
			
		} else {
			return false;
		}
		
	}
	
	public function setProcessingStatus($status){
	
		$query = 'UPDATE `salesorder` 
				  SET `processingstatus` = ?
				  WHERE `salesorder`.`salesorder_id` = ?
				  ';
		$params_array = array();
		$params_array[] = $status;
		$params_array[] = $this->id;
		if($this->obj_db->prepareAndDoQuery($query , $params_array)){
			
			$this->details['processingstatus'] = $status;
			return true;
			
		} else {
			return false;
		}
		
	}

}


?>