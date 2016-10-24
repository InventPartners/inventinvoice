<?php


class InvShipping {

	var $obj_db;
	var $shipping_bands = array();
	var $selected_band = array();
	
	public function __construct($obj_db){
		
		$this->obj_db = &$obj_db;
	
		$query = 'SELECT * FROM shippingband ORDER BY shippingband_id';
		if($this->obj_db->prepareAndDoQuery($query , array())){
			for($i=0; $i<count($this->obj_db->result_set); $i++) {
				$this->shipping_bands[$this->obj_db->result_set[$i]['shippingband_id']]['title'] = $this->obj_db->result_set[$i]['shippingband_title'];
				$this->shipping_bands[$this->obj_db->result_set[$i]['shippingband_id']]['product'] = $this->obj_db->result_set[$i]['shippingband_product'];
				$this->shipping_bands[$this->obj_db->result_set[$i]['shippingband_id']]['productaccessory'] = $this->obj_db->result_set[$i]['shippingband_productaccessory'];
				$this->shipping_bands[$this->obj_db->result_set[$i]['shippingband_id']]['sparepart'] = $this->obj_db->result_set[$i]['shippingband_sparepart'];
			}
		}
		
		return true;
	}
	
	public function setShippingBand($band){
		if(isset($this->shipping_bands[$band])){
			$this->selected_band = $this->shipping_bands[$band];
			return true;
		} else {
			$this->selected_band = $this->shipping_bands[1];
			return false;
		}
	}
	
	// Get the cart by the session
	public function calcShipping($rowdata , $total){
		if(isset($this->selected_band[$rowdata['item_type']])){
			if($this->selected_band[$rowdata['item_type']] > $total){
				$total = $this->selected_band[$rowdata['item_type']];
			}
		}
		return $total;
	}	

}


?>