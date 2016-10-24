<?php


Class InvFileConfig {

	protected $obj_db;
	public $table;					// Table for the config setting
	public $pri_key;				// Pri key field
	public $fields = array();		// List of fields and field settings
	public $joined_tables;
	public $join_statement;
	public $calc_tax_on = array();		//List of fields to calculate tax on
	public $do_created_date;
	public $do_updated_date;
	
	public function __construct($obj_db){
		$this->obj_db = &$obj_db;
		$this->table = '';
		$this->pri_key = '';
	}

	protected function initFromTable(){
		$this->fields = array();
		$this->getTableInfo($this->table);
		if(isset($this->joined_tables) && $this->joined_tables){
			$joined_table_array = explode(',' , $this->joined_tables);
			foreach($joined_table_array as $joined_table){
				$this->getTableInfo(trim(preg_replace("!`!" , '' , $joined_table)));
			}
		}
	}

	protected function getTableInfo($table){
		$query = 'DESCRIBE `' . $table . '`';
		$this->obj_db->query($query);
		while($field = $this->obj_db->fetch()){
			if($field['Key'] == 'PRI'){
				$this->pri_key = $field['Field'];
			} else if ($field['Field'] == 'created') {
				$this->do_created_date = true;
			} else if ($field['Field'] == 'updated') {
				$this->do_updated_date = true;
			} else {
				if(preg_match("/(float)|(double)|(int)/" , $field['Type'])){
					$field['validation'] = 'numeric';
				} else if(preg_match("/date/" , $field['Type'])){
					$field['validation'] = 'datetime';
				} else {
					$field['validation'] = 'string';
				}
				$this->fields[] = $field;
			}
		}
	
	}
	
	protected function getOptions($table , $value_field, $value_caption){
		$options = array();
		$query = 'SELECT ' . $value_field . ' AS value , ' . $value_caption . ' AS caption FROM ' . $table . ' ORDER BY ' . $value_caption;
		$this->obj_db->query($query);
		$options = $this->obj_db->fetchAll();
		return $options;
	}

}


?>