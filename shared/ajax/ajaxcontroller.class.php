<?php

class InvAjaxController extends InvController{

	public $output;
	
	protected function sendHeaders(){
		
	}
	
	protected function displayView(){
		echo $this->output;
	}

}

?>