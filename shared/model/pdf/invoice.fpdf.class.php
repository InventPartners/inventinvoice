<?php

/* Generate an invoice using FDPF and FPDI

*/

require_once(MODEL_PATH . 'pdf/fpdf16/fpdf.php');
require_once(MODEL_PATH . 'pdf/FPDI_1.3.2/fpdi.php');

class PDFInvoice {

	private $pdf;
	private $font;
	private $curr_line_pos = 0;
	private $tax_invoice = false;
	private $invoice_headers;

	public function __construct(){
	}
	
	public function writeInvoice($invoice_headers , $items) {
	
		$this->invoice_headers = $invoice_headers;

		// VAT invoice?
		if($this->invoice_headers['taxcode_id'] > 1){
			$this->tax_invoice = true;
		} else {
			$this->tax_invoice = false;
		}
	
	 	$this->pdf = new FPDI('Portrait' , 'pt' , 'A4');
	 	$this->pdf->SetCreator("Invent Partners Invoicing Software" , true);
    	$this->pdf->SetAuthor("Invent Partners Invoicing Software" , true);
    	$this->pdf->SetTitle("Invoice" , true);
    	$this->addPage();
		
		// Line items
		foreach ($items as $item){
			$this->writeLine(
				$item['qty'] ,
				$item['description'] ,
				$item['unit_price'] ,
				$item['line_total_tax'] ,
				'20%' , 
				$item['line_total']
			);
		}
		
		//totals
		$this->pdf->SetFont("Helvetica" , '' , 10);
		$this->writeTotals(
			$this->invoice_headers['invoice_total'] ,
			$this->invoice_headers['invoice_tax'] ,
			$this->invoice_headers['invoice_total'] +	$invoice_headers['invoice_tax']
		);
	}

	public function addPage(){
    	$this->pdf->AddPage('Portrait' , 'A4');
    	$this->billing_from_line_pos = 773;
    	$this->billing_contact_line_pos = 717;
    	$this->curr_line_pos = 560;
    	$this->loadTemplate();
    	$this->pdf->SetMargins(0,0);
    	
    	// Headers
		$this->doInvoiceHeaders($this->invoice_headers);
		$this->pdf->SetFont("Helvetica" , '' , 10);
		
	}
	
	public function loadTemplate(){
		if($this->tax_invoice){
			// VAT Invoice template
			$source_file = VIEW_PATH . 'vat_invoice_template.pdf';
		} else {
			// Non VAT Invoice template
			$source_file = VIEW_PATH . 'no_vat_invoice_template.pdf';
		}
		$this->pdf->setSourceFile($source_file , '');
		$template_page = $this->pdf->importPage(1);
		$this->pdf->usetemplate($template_page , 0 , 0);
	}
	
	public function doInvoiceHeaders($invoice_headers)	{
		
		$this->pdf->SetFont("Helvetica" , '' , 10);
		
		/// Invoice from?
		if($invoice_headers['invoice_from_company']){
			$this->writeBillingFromLine($invoice_headers['invoice_from_company']);
		}
		for($i=1; $i<=5; $i++){
			if($invoice_headers['invoice_from_address' . $i]){
				$this->writeBillingFromLine($invoice_headers['invoice_from_address' . $i]);
			}
		}
		if($invoice_headers['invoice_from_postcode']){
			$this->writeBillingFromLine($invoice_headers['invoice_from_postcode']);
		}
		
		$this->pdf->SetFont("Helvetica" , '' , 12);
		
		// Vat Number and date, etc:
		if($this->tax_invoice){
			// VAT invoices show a VAT number
			$this->writeVATNumber($invoice_headers['invoice_from_vatnumber']);
		}
		$this->writeInvoiceNumber($invoice_headers['invoice_id']);
		$this->writeInvoiceDate($invoice_headers['invoice_date']);
		
		
		/// Invoice to?
		if($invoice_headers['invoice_to_company']){
			$this->writeBillingContactLine($invoice_headers['invoice_to_company']);
		}
		for($i=1; $i<=5; $i++){
			if($invoice_headers['invoice_to_address' . $i]){
				$this->writeBillingContactLine($invoice_headers['invoice_to_address' . $i]);
			}
		}
		if($invoice_headers['invoice_to_postcode']){
			$this->writeBillingContactLine($invoice_headers['invoice_to_postcode']);
		}

	}

	public function writeBillingFromLine($string){
		$string = $this->stringEncode($string);
    	$this->pdf->SetXY(436, -$this->billing_from_line_pos);
    	$this->pdf->Cell(100 , 0 , $string , 0 , 0 , 'R');
    	$this->billing_from_line_pos -= 10;
	}

	public function writeBillingContactLine($string){
		$string = $this->stringEncode($string);
    	$this->pdf->SetXY(58, -$this->billing_contact_line_pos);
    	$this->pdf->Write(0 , $string);
    	$this->billing_contact_line_pos -= 12;
	}

	public function writeVATNumber($string){
		$string = $this->stringEncode($string);
    	$this->pdf->SetXY(390, -660);
    	$this->pdf->Write(0 , $string);
	}

	public function writeInvoiceNumber($string){
		$string = $this->stringEncode($string);
    	$this->pdf->SetXY(390, -646);
    	$this->pdf->Write(0 , $string);
	}

	public function writeInvoiceDate($string){
		$string = $this->stringEncode($string);
    	$this->pdf->SetXY(390, -632);
    	$this->pdf->Write(0 , sqlDateTimeToDisplayDate($string));
	}

	public function writeTotals($price_ex , $tax , $price_inc){
		if($price_ex != '---'){
			$price_ex = $this->stringEncode(number_format($price_ex , 2));
		} 
		if($tax != '---'){
			$tax = $this->stringEncode(number_format($tax , 2));
		}
		if($price_inc != '---'){
			$price_inc = $this->stringEncode(number_format($price_inc , 2));
		}
		
		if($this->tax_invoice){
    		// VAT invoices
    		$this->pdf->SetXY(475, -147);
    		$this->pdf->Cell(60 , 0 , $price_ex , 0 , 0 , 'R');
    		$this->pdf->SetXY(475, -122);
    		$this->pdf->Cell(60 , 0 , $tax , 0 , 0 , 'R');
			$this->pdf->SetFont("Helvetica" , 'B' , 10);
    		$this->pdf->SetXY(475, -97);
    		$this->pdf->Cell(60 , 0 , $price_inc , 0 , 0 , 'R');
    	} else {
    		// Non VAT invoices
    		$this->pdf->SetXY(475, -147);
    		$this->pdf->Cell(60 , 0 , $price_inc , 0 , 0 , 'R');
    	}
	}

	public function writeLine($qty , $description , $unit_price , $tax , $tax_rate , $line_total){
		//echo $description . $this->curr_line_pos;
		$qty = $this->stringEncode($qty);
		$description = $this->stringEncode($description);
		$unit_price = $this->stringEncode(number_format($unit_price , 2));
		$tax = $this->stringEncode(number_format($tax , 2));
		$tax_rate = $this->stringEncode($tax_rate);
		$line_total = $this->stringEncode(number_format($line_total , 2));
		
    	$this->pdf->SetXY(60 , -$this->curr_line_pos);
    	$this->pdf->Cell(20 , 0 , $qty , 0 , 0 , 'L');
    	$this->pdf->SetXY(80, -$this->curr_line_pos);
    	$this->pdf->Cell(350 , 0 , $description , 0 , 0 , 'L');
		
		if($this->tax_invoice){
			// VAT Invoices
			$this->pdf->SetXY(320, -$this->curr_line_pos);
			$this->pdf->Cell(60 , 0 , $unit_price , 0 , 0 , 'R');
			$this->pdf->SetXY(406, -$this->curr_line_pos);
			$this->pdf->Cell(30 , 0 , $tax , 0 , 0 , 'R');
			$this->pdf->SetXY(445, -$this->curr_line_pos);
			$this->pdf->Cell(30 , 0 , $tax_rate , 0 , 0 , 'R');
    	} else {
    		// Non VAT Invoices
			$this->pdf->SetXY(406, -$this->curr_line_pos);
			$this->pdf->Cell(60 , 0 , $unit_price , 0 , 0 , 'R');
    	}
    	
		$this->pdf->SetXY(475, -$this->curr_line_pos);
		$this->pdf->Cell(60 , 0 , $line_total , 0 , 0 , 'R');
		
		$this->curr_line_pos -= 20;
		if($this->curr_line_pos <= 160){
			$this->writeTotals(
				'---' ,
				'---' ,
				'---'
			);
			$this->addPage();
		}
	}

	public function sendOutput(){
		$this->pdf->Output('invoice.pdf','I');
	}

	public function getOutput(){
		return $this->pdf->Output('invoice.pdf','S');
	}

	protected function stringEncode($string){
		return iconv('UTF-8' , 'ISO-8859-1//IGNORE' , $string);
	}

}


?>