<?php


class InvPaymentFile extends InvFile {
	
	public function __construct($config , $obj_db){
		parent::__construct($config , $obj_db);
	}
	
	public function reconcilePayment() {
	
	}
	
	public function reconcileTo($invoice_id , $amount=0) {
		if($this->id){
			$paymentreconcile = $this->obj_db->getFileModel('paymentreconcile');
			$paymentreconcile->create();
			$paymentreconcile->updateValue('payment_id' , $this->id);
			$paymentreconcile->updateValue('invoice_id' , $invoice_id);
			if($amount && $amount <= $this->filedata['payment_amount']){
				$paymentreconcile->updateValue('paymentreconcile_amount' , $amount);
			} else {
				$paymentreconcile->updateValue('paymentreconcile_amount' , $this->filedata['payment_amount']);
			}
			$paymentreconcile->save();
			return true;
		}
		return false;
	}
	

}


?>