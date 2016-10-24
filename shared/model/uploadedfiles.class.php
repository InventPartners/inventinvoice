<?php
/*	Uploaded files
	----------------------------
	
	Class to produce a list of files on the server
	Defaultsto the standard public media folder

*/

class InvUploadedfiles{

	protected $file_system_path;
	protected $web_site_path;

	public function __construct(){
		
		$path = 'media/';
		$this->file_system_path = PUBLIC_SITE_PATH . $path;
		$this->web_site_path = '/' . $path;
	}
	
	public function listFiles(){
		$filelist = array();
		if(is_dir($this->file_system_path)){
			if($dirp = opendir($this->file_system_path)){
				while (($filename = readdir($dirp)) !== false) {
					if($filename != '.' && $filename != '..'){
						$filelist_row = array();
						$filelist_row['filename'] = $filename;
						$filelist_row['web_path'] = $this->web_site_path . $filename;
						$filelist_row['filesize'] = filesize($this->file_system_path . $filename);
						$filelist[] = $filelist_row;
					}
				}
				return $filelist;
			} else {
				return false;
			}
		} else {
				echo 'here';
			return false;
		}
	}

}


?>