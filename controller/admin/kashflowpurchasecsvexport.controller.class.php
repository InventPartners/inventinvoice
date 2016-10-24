<?php

require_once(CONTROLLER_PATH . 'admin/csvexport.controller.class.php');

class KashflowpurchasecsvexportController extends CSVExportController{

	public function setQuery(){
		$this->dbquery = '
			SELECT 
				* 
			FROM
				`purchase`
			WHERE 
				`purchase`.`purchase_status` != "void"
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
			'purchase_date' => array(
				'caption' => 'Receipt Date' ,
				'default' => ''
			) ,
			'due_date' => array(
				'caption' => 'Due Date' ,
				'default' => ''
			) ,
			'contact_id' => array(
				'caption' => 'Supplier Code' ,
				'default' => ''
			) ,
			'qty' => array(
				'caption' => 'Line-Quantity' ,
				'default' => '1'
			) ,
			'purchase_total' => array(
				'caption' => 'Line-Rate' ,
				'default' => ''
			) ,
			'purchase_tax' => array(
				'caption' => 'Line-Vat Amount' ,
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
			// Customer ID
			$row['contact_id'] = str_pad($row['contact_id'] , 6 , '0' , STR_PAD_LEFT);
		
			//Some date mangling
			$dateobj = new DateTime($row['purchase_date'] . ' 00:00:00');
			$row['purchase_date'] =  $dateobj->format('d/m/Y');
			$invoicedatets =  $dateobj->format('U');
			$dateobj->add(new DateInterval('P28D'));
			$row['due_date'] =  $dateobj->format('d/m/Y');
			
			$this->exportRow($row);
		}
		
		exit;
	
	}

}

?>