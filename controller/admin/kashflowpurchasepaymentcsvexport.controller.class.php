<?php

require_once(CONTROLLER_PATH . 'admin/csvexport.controller.class.php');

class KashflowpurchasepaymentcsvexportController extends CSVExportController{

	public function setQuery(){

		$this->dbquery = '
			SELECT 
				* 
			FROM
				`purchase`
			WHERE 
				`purchase`.`purchase_status` = "paid"
		';
	}
	
	public function setParams(){
		$this->params = array();
	}
	
	public function setCols(){
		$this->cols = array(
			'purchase_reference' => array(
				'caption' => 'Receipt Number' ,
				'default' => ''
			) ,
			'paid_date' => array(
				'caption' => 'Paid Date' ,
				'default' => ''
			) ,
			'total_inc' => array(
				'caption' => 'Paid Amount' ,
				'default' => ''
			)
		);
	}
	
	public function exportCSV(){
	
		$this->exportHeaders();
		
		foreach($this->obj_db->result_set as $row){
		
			if(!$row['purchase_reference']){
				$row['purchase_reference'] = 'INV-' . $row['purchase_id'];
			}
			//Some date mangling
			$dateobj = new DateTime($row['updated']);
			$row['paid_date'] =  $dateobj->format('d/m/Y');
			
			// Total
			$row['total_inc'] = $row['purchase_total'] + $row['purchase_tax'];
			
			$this->exportRow($row);
		}
		
		exit;
	
	}

}

?>