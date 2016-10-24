<?php

require_once(CONTROLLER_PATH . 'admin/csvexport.controller.class.php');

class KashflowinvoicepaymentcsvexportController extends CSVExportController{

	public function setQuery(){
		/*
		$this->dbquery = '
			SELECT 
				* 
			FROM
				`invoice` ,
				`invoicelog`
			WHERE 
				`invoice`.`invoice_id` = `invoicelog`.`invoice_id`
			AND
				`invoice`.`invoice_status` = "paid"
			AND
				`invoicelog`.`invoicelog_status` = "paid"
			GROUP BY
				`invoice`.`invoice_id`
			ORDER BY
				`invoicelog`.`invoicelog_date` DESC
		';
		*/
		$this->dbquery = '
			SELECT 
				* 
			FROM
				`invoice`
			WHERE 
				`invoice`.`invoice_status` = "paid"
		';
	}
	
	public function setParams(){
		$this->params = array();
	}
	
	public function setCols(){
		$this->cols = array(
			'invoice_id' => array(
				'caption' => 'Invoice Number' ,
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
		
		
			//Some date mangling
			$dateobj = new DateTime($row['updated']);
			$row['paid_date'] =  $dateobj->format('d/m/Y');
			
			// Total
			$row['total_inc'] = $row['invoice_total'] + $row['invoice_tax'];
			
			$this->exportRow($row);
		}
		
		exit;
	
	}

}

?>