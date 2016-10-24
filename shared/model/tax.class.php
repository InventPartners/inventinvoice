<?php


class InvTax {

	var $tax_code;
	var $tax_calcs;
	
	public function __construct($obj_db){
		$this->tax_code = 'default';
		// default tax calculation
		$this->tax_calcs = array();
		$this->tax_calcs[2]['operator'] = 'mul';
		$this->tax_calcs[2]['amount'] = 0.2; 
		return true;
	}
	
	public function setTaxCode($tax_code){
		return false;
	}
	
	// Get the cart by the session
	public function calcTax($value , $taxcode_id){
		if(isset($this->tax_calcs[$taxcode_id])){
			if(isset($this->tax_calcs[$taxcode_id]['operator']) && isset($this->tax_calcs[$taxcode_id]['amount'])){
				if ($this->tax_calcs[$taxcode_id]['operator'] == 'mul') {
					$tax = bcmul(strval($value) , strval($this->tax_calcs[$taxcode_id]['amount']) , 2);
				} else if ($this->tax_calcs[$taxcode_id]['operator'] == 'add') {
					$tax = bcadd(strval($value) , strval($this->tax_calcs[$taxcode_id]['amount']) , 2);
				} 
				$tax = floatval($tax);
				return $tax;			
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}	

}


?>