<?php
/*		Upload Class
		-------------------------
		
		Manages uploads to the system

*/

Class InvUpload {

	var $tmp_file;
	var $target_filename;
	var $file_system_path;
	var $file_web_path;

	public function __construct(){
	
	}
	
	public function processNewUpload($input_fieldname , $destination_dir , $destination_filename=''){
		// Does the uploaded file exist?
		global $_FILES;
		if(isset($_FILES[$input_fieldname]['tmp_name']) && is_file($_FILES[$input_fieldname]['tmp_name'])){
			// Does the destination directory exist?
			if(is_dir($destination_dir)){
				// Find out where we're moving the file.
				$this->temp_file = $_FILES[$input_fieldname]['tmp_name'];
				if($destination_filename){
					$this->target_filename = $destination_filename;
				} else {
					$this->target_filename = $_FILES[$input_fieldname]['name'];
				}
				// Move the file to the destination
				if(move_uploaded_file($this->temp_file , $destination_dir . $this->target_filename)){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function getFileExtension($input_fieldname){
		global $_FILES;
		if(isset($_FILES[$input_fieldname]['name']) && is_file($_FILES[$input_fieldname]['name'])){
			$extension_array = array_reverse(str_split('.' , $_FILES[$input_fieldname]['name']));
			if(isset($extension_array[0]) && $extension_array[0]){
				return $extension_array[0];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

}

?>