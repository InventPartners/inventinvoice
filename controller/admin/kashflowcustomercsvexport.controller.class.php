<?php

require_once(CONTROLLER_PATH . 'admin/csvexport.controller.class.php');

class KashflowcustomercsvexportController extends CSVExportController{

	public function setQuery(){
		$this->dbquery = '
			SELECT 
				* 
			FROM
				`contact`
			WHERE
				`contact`.`contact_status` != "deleted"
		';
	}
	
	public function setParams(){
		$this->params = array();
	}
	
	public function setCols(){
	
		$this->cols = array(
			'contact_id' => array(
				'caption' => 'Code' ,
				'default' => '' ,
			) , 
			'contact_company' => array(
				'caption' => 'Name' ,
				'default' => '' ,
			) , 
			'contact_name' => array(
				'caption' => 'Contact Name' ,
				'default' => '' ,
			) , 
			'contact_tel' => array(
				'caption' => 'Telephone' ,
				'default' => '' ,
			) , 
			'contact_email' => array(
				'caption' => 'Email' ,
				'default' => '' ,
			) , 
			'contact_address1' => array(
				'caption' => 'Address 1' ,
				'default' => '' ,
			) , 
			'contact_address2' => array(
				'caption' => 'Address 2' ,
				'default' => '' ,
			) , 
			'contact_address3' => array(
				'caption' => 'Address 3' ,
				'default' => '' ,
			) , 
			'contact_address4' => array(
				'caption' => 'Address 4' ,
				'default' => '' ,
			) , 
			'contact_postcode' => array(
				'caption' => 'Postcode' ,
				'default' => '' ,
			) , 
			'ec' => array(
				'caption' => 'EC' ,
				'default' => '1' ,
			) , 
			'created' => array(
				'caption' => 'Created' ,
				'default' => '' ,
			) , 
			'contact_vatnumber' => array(
				'caption' => 'VAT Number' ,
				'default' => '' ,
			)
		);
	}
	
	public function exportCSV(){
	
		$this->exportHeaders();
		
		foreach($this->obj_db->result_set as $row){
			// Customer ID
			$row['contact_id'] = str_pad($row['contact_id'] , 6 , '0' , STR_PAD_LEFT);
		
			//Some address mangling
			if(!$row['contact_address4'] && $row['contact_address5']){
				$row['contact_address4'] = $row['contact_address5'];
			}
			if(!$row['contact_company']){
				$row['contact_company'] = $row['contact_firstname'] . ' ' . $row['contact_lastname'];
			}
			$row['contact_name'] = $row['contact_firstname'] . ' ' . $row['contact_lastname'];
			
			$this->exportRow($row);			
		}
		
		exit;
	
	}

}

?>