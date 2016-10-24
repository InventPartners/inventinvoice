<?php

require_once(CONTROLLER_PATH . 'admin/csvexport.controller.class.php');

class KashflowinvoicecsvexportController extends CSVExportController{

	public function setQuery(){
		$this->dbquery = '
			SELECT 
				* 
			FROM
				`invoice` ,
				`invoiceitem`
			WHERE
				`invoice`.`invoice_id` = `invoiceitem`.`invoice_id`
			AND 
				`invoice`.`invoice_status` != "void"
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
			'invoice_date' => array(
				'caption' => 'Invoice Date' ,
				'default' => ''
			) ,
			'due_date' => array(
				'caption' => 'Due Date' ,
				'default' => ''
			) ,
			'contact_id' => array(
				'caption' => 'Customer Code' ,
				'default' => ''
			) ,
			'qty' => array(
				'caption' => 'Line-Quantity' ,
				'default' => ''
			) ,
			'description' => array(
				'caption' => 'Line-Description' ,
				'default' => ''
			) ,
			'line_total' => array(
				'caption' => 'Line-Rate' ,
				'default' => ''
			) ,
			'line_total_vat' => array(
				'caption' => 'Line-Vat Amount' ,
				'default' => ''
			) ,
			'vat_rate' => array(
				'caption' => 'Line-Vat Rate' ,
				'default' => 0
			)
		);
	}
	
	public function exportCSV(){
	
		$this->exportHeaders();
		
		foreach($this->obj_db->result_set as $row){
			// Customer ID
			$row['contact_id'] = str_pad($row['contact_id'] , 6 , '0' , STR_PAD_LEFT);
		
			//Some date mangling
			$dateobj = new DateTime($row['invoice_date'] . ' 00:00:00');
			$row['invoice_date'] =  $dateobj->format('d/m/Y');
			$invoicedatets =  $dateobj->format('U');
			$dateobj->add(new DateInterval('P28D'));
			$row['due_date'] =  $dateobj->format('d/m/Y');
			
			// VAT
			$vatregdateobj = new DateTime('2011-12-31 23:59:59');
			$vatregts =  $vatregdateobj->format('U');
			if($invoicedatets <= $vatregts){
				$row['vat_rate'] = 0;
			} else {
				$row['vat_rate'] = 20;
			}
			$row['line_total_vat'] = round((($row['line_total'] * $row['vat_rate']) / 100) , 2);
			
			$this->exportRow($row);
		}
		
		exit;
	
	}

}

?>