<?php

class InvSession{

	var $obj_db;

	public function __construct($obj_db , $obj_db){
		$this->obj_db = &$obj_db;
		if($this->getValue('username') && $this->getValue('password')){
			$this->login($this->getValue('username') , $this->getValue('password'));
		}
		return true;
	}

    public function setValue($key , $value) {
        global $_SESSION;
        $_SESSION[$key] = $value;
        if($key == 'username') {
            $this->username = $value;
        } else if($key == 'password') {
            $this->password = $value;
        }
        return true;
    }

    public function getValue($key) {
        global $_SESSION;
        if(isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return false;
        }
    }
    
    public function userPermitted($auth_path){
    	if($auth_path == '/'){
    		return true;
    	} else if ($auth_path == '/admin/'){
    		if($this->getValue('is_logged_in') == 1 && $this->getValue('is_admin') == 1){
    			return true;
    		} else {
    			return false;
    		}
    	} else {
    		return false;
    	}
    }
    
    public function login($username , $password){
    	$query = 'SELECT * FROM `user` , `user_account` 
    			  WHERE `user`.`user_id` = `user_account`.`user_id`
    			  AND `user`.`username` = ? 
    			  AND `user`.`password` = PASSWORD(?)
    			  AND `user`.`status` = "active"
    			  AND `user_account`.`status` = "active"';
    	$params_array = array($username , $password);
    	if($this->obj_db->prepareAndDoQuery($query , $params_array)){
    		if(count($this->obj_db->result_set) == 1){
				$this->setValue('userdata' , $this->obj_db->result_set[0]);
				$this->setValue('username' , $username);
				$this->setValue('password' , $password);
				$this->setValue('is_logged_in' , 1);
				$this->setvalue('is_admin' , $this->obj_db->result_set[0]['is_admin']);
				return true;
    		} else {
    			$this->logout();
    			return false;
    		}
    	} else {
    		$this->logout();
			return false;
    	}
    }
    
    public function logout(){
    	
    	$this->setValue('userdata' , array());
    	$this->setValue('accountdata' , array());
		$this->setValue('username' , '');
		$this->setValue('password' , '');
		$this->setValue('account' , '');
    	$this->setValue('is_logged_in' , 0);
    	$this->setvalue('is_admin' , 0);
			
		return true;
		
    }
    
    public function checkEnteredPassword($password){
    
    	$query = 'SELECT * FROM `user` , `user_account` 
    			  WHERE `user`.`user_id` = `user_account`.`user_id`
    			  AND `user`.`username` = ? 
    			  AND `user`.`password` = PASSWORD(?)
    			  AND `user`.`status` = "active"';
    	$params_array = array($this->getValue('username') , $password);
    	if($this->obj_db->prepareAndDoQuery($query , $params_array)){
    		if(count($this->obj_db->result_set) == 1){
    			return true;
    		} else {
    			return false;
    		}
    	} else {
			return false;
    	}
    
    }
    
    protected function loadAccountsDB($account){
    	
    	$query = 'SELECT * FROM account';
    	$params_array = array();
    	if($this->obj_db->prepareAndDoQuery($query , $params_array)){
    		if(count($this->obj_db->result_set) == 1){
    			$this->setValue('accountdata' , $this->obj_db->result_set[0]);
    			return true;
    		} else {
    			$this->logout();
    			return false;
    		}
    	} else {
    		$this->logout();
			return false;
    	}
    	
    }

}

?>