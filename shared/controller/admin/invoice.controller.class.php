<?php

class InvoiceController extends InvController {

	public $product;		// Ponter to product object

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		$this->max_path_length = 2;
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Invoice';
		$this->view_area = 'invoices';
	}
	
	protected function setInputValues(){
		$this->setInputValue('save');
		$this->setInputValue('repeat');
		$this->setInputValue('email');
	}
	
	public function doController(){
		$this->invoice = $this->obj_db->getFileModel('invoice');
		if($this->invoice->open($this->path_info[0])){
			$this->reconcile_log = $this->invoice->getPaymentReconcileLog();
			if($this->arr_input['repeat']){
				if($repeatinvoice_id = $this->invoice->repeatInvoice()){
					redirectRequest('/admin/editrepeatinvoice/' . $repeatinvoice_id);
				}
			}
			// Are we opening a PDF version?
			if(isset($this->path_info[1])){
				$this->showPDF();
			}
			// Are we emailing this invoice?
			if($this->arr_input['email']){
				$this->invoice->emailInvoice();
			}
		} else {
			$this->show404();
		}
	}
	
	protected function showPDF(){
	
		/*
		require_once(MODEL_PATH . 'pdf/invoice.fpdf.class.php');
		$pdf = new PDFInvoice();
		//$pdf->loadTemplate();
		//print_r($this->invoice->items);
		$pdf->writeInvoice(
			$this->invoice->filedata, 
			$this->invoice->items
		);
		$output = $pdf->getOutput();
		*/
		$output = $this->invoice->getPDFInvoice();
		
		//echo $output;
		//exit;
		
		header("Pragma: ");
		header("Content-type: application/pdf");
		//$len = strlen($output);
		//header("Content-Length: $len");
		header("Content-Disposition: inline; filename=" . $this->invoice->id . $this->stock->id . ".pdf");
		print $output;
		
		exit;
	
	}

}

?>