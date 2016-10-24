<?php

class InvDB extends PDO{

   	protected $engine;
    protected $host;
    protected $database;
    protected $user;
    protected $pass;
    
    public $obj_last_result;
    public $result_count = 0;
    public $result_set = array();    

    protected $db;

    public function __construct(){
        $this->engine = 'mysql';
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
    }

    public function connect($db_name){
        $this->database = $db_name;
    	$dsn = $this->engine.':dbname=' . $this->database . ";host=" . $this->host;
    	try {
        	$this->db = parent::__construct( $dsn, $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
        	return true;
        } catch (PDOException $e) {
        	exit;
        }
    }
    
    public function query($query){
    	unset($this->result);
    	$this->result_count = 0;
    	$this->result_set = array();
    	unset($this->obj_last_result);
    	$this->obj_last_result = parent::query($query);
    	if(isset($this->obj_last_result) && is_object($this->obj_last_result)){
    		$this->result_set = $this->obj_last_result->fetchAll(PDO::FETCH_ASSOC);
    		$this->result_count = count($this->result_set);
    		return $this->obj_last_result;
    	} else {
    		//print_r($this->errorInfo());
    		return false;
    	}
    }

    public function doCountQuery($query){
    	$result = parent::query($query);
    	if(isset($result) && is_object($result)){
    		$count_data = $result->fetch(PDO::FETCH_ASSOC);
    		if(isset($count_data['count'])){
    			return $count_data['count'];
    		} else {
    			return 0;
    		}
    	} else {
    		return 0;
    	}
    }
    
    public function prepareAndDoQuery($query , $params_array){
    	$this->prepareQuery($query);
    	$this->bindParamArray($params_array); 
    	if($this->preparedquery->execute()){
    		$this->getResultFromPreparedQuery();
			//var_dump($this->preparedquery);
    		return true;
    	} else {
			//var_dump($this->preparedquery);
    		//print_r($this->preparedquery->errorInfo());
    		return false;
    	}
    }
    
    public function getResultFromPreparedQuery(){
    	$this->result_set = $this->preparedquery->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function bindParamArray($params_array){
    	for($i=0; $i<count($params_array); $i++){
    		$this->bindQueryParam($i+1 , $params_array[$i]);
    	}
    }
    
    public function bindQueryParam($param_name , $param , $type=''){
    	if($type){
    		$this->preparedquery->bindParam($param_name , $param , $type);
    	} else {
    		$this->preparedquery->bindParam($param_name , $param);
    	}
    }
    
    public function prepareQuery($query){
    	$this->preparedquery = parent::prepare($query);
    	return true;
    }

    public function selectedRowCount(){
    	return $this->result_count;
    }

    public function rowCount(){
    	if(is_object($this->obj_last_result)){
    		return $this->obj_last_result->rowCount();
    	} else {
    		return false;
    	}
    }

    public function resetResultSet(){
    	reset ($this->result_set);
    }

    public function fetch(){
    	$row = current($this->result_set);
    	if(is_array($row)){
    		reset($row);
    	}
    	next($this->result_set);
    	return $row;
    }

    public function fetchAll(){
       	reset ($this->result_set);
    	return $this->result_set;
    }
	
	public function getWhereAnd($where , $new_bit){
		if($where){
			$where .= ' AND ' . $new_bit;
		} else {
			$where = 'WHERE ' . $new_bit;
		}
		return $where;
	}
	
	public function getFileModel($type){
		// Attempt to load the config for this file class
		if(is_file(CONFIG_PATH . $type . '.fileconfig.class.php')){
			require_once(CONFIG_PATH . $type . '.fileconfig.class.php');
			$config_classname = 'Inv' . UCfirst($type). 'Config';
			$config = new $config_classname($this);
			// Now attempt to instantiate the file model
			if(is_file(MODEL_PATH . $type . '.file.class.php')){
				require_once(MODEL_PATH . $type . '.file.class.php');
				$file_classname = 'Inv' . UCfirst($type). 'File';
				$model = new $file_classname($config , $this);
				return $model;
			} else {
				// couldn't load the model include
				return false;
			}
		} else {
			// couldn't load the config include for this model
			return false;
		}
	}
	
	public function getListModel($type){
		// Attempt to load the config for this file class
		if(is_file(CONFIG_PATH . $type . '.fileconfig.class.php')){
			require_once(CONFIG_PATH . $type . '.fileconfig.class.php');
			$config_classname = 'Inv' . UCfirst($type). 'Config';
			$config = new $config_classname($this);
			// Now attempt to instantiate the file model
			if(is_file(MODEL_PATH . $type . '.list.class.php')){
				require_once(MODEL_PATH . $type . '.list.class.php');
				$file_classname = 'Inv' . UCfirst($type). 'List';
				$model = new $file_classname($config , $this);
				return $model;
			} else {
				// couldn't load the model include
				return false;
			}
		} else {
			// couldn't load the config include for this model
			return false;
		}
	}


}

?>