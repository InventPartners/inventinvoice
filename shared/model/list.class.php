<?php


class InvList {

	var $obj_db;
	var $obj_tax;
	var $config;
	var $num_results_per_page;
	var $num_pagination_links;
	
	var $where;
	var $params_array = array();
	var $order;
	var $count_query;
	var $page_from;
	var $page_results_query;
	
	var $resultset;
	var $pagination;
	
	public function __construct($config , $obj_db){
		if($config){
			$this->config = $config;
			$this->obj_db = &$obj_db;
			$this->id = false;
			$this->num_results_per_page =  25;
			$this->num_pagination_links =  10;
			// Instantiate tax class
			require_once(MODEL_PATH . 'tax.class.php');
			$this->obj_tax = new InvTax($this->obj_db);
		} else {
			return false;
		}
	}
	
	public function getList($where , $params_array , $order , $page_from=1){
	
		$this->where = $where;
		$this->params_array = $params_array;
		$this->order = $order;
		
		$this->current_page = intval($page_from);
		if($this->current_page < 1){
			$this->current_page = 1;
		}
	
		// Build the select from
		$select_from = (($this->current_page - 1) * $this->num_results_per_page);
		
		//build queries
		$from = '`' . $this->config->table . '` ';
		if($this->config->joined_tables){
			$from .= ' , ' . $this->config->joined_tables;
		}
		
		if($where){
			$from .= ' ' . $where;
			if($this->config->join_statement){
				$from .= ' AND ' . $this->config->join_statement;
			}
		} else {
			if($this->config->join_statement){
				$from .= ' WHERE ' . $this->config->join_statement;
			}
		}
		
		// The page results query
		$this->page_results_query = 'SELECT * FROM ' . $from;
		if($order){
			$this->page_results_query .= ' ORDER BY ' . $order;
		}
		$this->page_results_query .= ' LIMIT ' . $select_from . ' , ' . $this->num_results_per_page;
		
		// full count of results
		$this->count_query = 'SELECT count(`' . $this->config->table . '`.`' . $this->config->pri_key . '`) AS `count` FROM ' . $from;
		
		// Get the data;
		if($this->getPageList()){
			$this->getPagination();
			return true;
		} else {
			return false;
		}
		
	}
	
	// Open the file
	public function getPageList(){
		if($this->obj_db->prepareAndDoQuery($this->page_results_query , $this->params_array)){
			if(count($this->obj_db->result_set) > 0){
				if(is_array($this->config->calc_tax_on) && count($this->config->calc_tax_on) > 0){
					$this->resultset = array();
					for($i=0; $i<count($this->obj_db->result_set); $i++) {
						$result_row = $this->obj_db->result_set[$i];
						for($c=0; $c<count($this->config->calc_tax_on); $c++) {
							if(isset($result_row[$this->config->calc_tax_on[$c]])) {
								$result_row[$this->config->calc_tax_on[$c] . '_tax'] = $this->obj_tax->calcTax($result_row[$this->config->calc_tax_on[$c]] , 0);
								$result_row[$this->config->calc_tax_on[$c] . '_inc'] = $result_row[$this->config->calc_tax_on[$c]] + $result_row[$this->config->calc_tax_on[$c] . '_tax'];
							} else {
								$result_row[$this->config->calc_tax_on[$c] . '_tax'] = 0;
								$result_row[$this->config->calc_tax_on[$c] . '_inc'] = 0;
							}
						}
						$this->resultset[] = $result_row;
					}
				} else {
					$this->resultset = $this->obj_db->result_set;
				}
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	public function getPagination(){
		
		$this->pagination['result_from'] = (($this->current_page - 1) * $this->num_results_per_page) + 1;
		$this->pagination['result_to'] = $this->pagination['result_from'] + count($this->resultset) - 1;
		$this->pagination['previous'] = false;
		$this->pagination['next'] = false;
		$this->pagination['paging_count_start'] = false;
		$this->pagination['paging_count_end'] = false;
	
		if($this->obj_db->prepareAndDoQuery($this->count_query , $this->params_array)){
			if(count($this->obj_db->result_set) > 0){
				$result_row = $this->obj_db->result_set[0];
				$this->pagination['total_results'] = $result_row['count'];
				// Do we need to show some pagination links
				if($this->pagination['total_results'] > count($this->resultset)){
					$this->pagination['current_page'] = $this->current_page;
					// previous button?
					if($this->current_page > 1){
						$this->pagination['previous'] = $this->current_page - 1;
					} else {
						$this->pagination['previous'] = false;
					}
					// next button?
					if($this->pagination['total_results'] > $this->pagination['result_to']){
						$this->pagination['next'] = $this->current_page + 1;
					} else {
						$this->pagination['next'] = false;
					}
					
					// Individual page links
					// How many could there be?
					$paging_count_start = 1;
					$number_of_page_links = ceil($this->pagination['total_results'] / $this->num_results_per_page);
					// what's our ceiling for links? Do we need to adjust the number of page links value?
					if($number_of_page_links > $this->num_pagination_links){
						if($this->current_page > ceil($this->num_pagination_links / 2)){
							$paging_count_end = $this->current_page + ceil($this->num_pagination_links / 2);
							if($paging_count_end > $number_of_page_links){
								$paging_count_end = $number_of_page_links;
							}
						}
						else{
							$paging_count_end = $this->num_pagination_links;
						}
		
						if($this->current_page > ceil($this->num_pagination_links / 2)){
							$paging_count_start = $paging_count_end - $this->num_pagination_links - 1;
							if($paging_count_start < 1){
								$paging_count_start = 1;
							}
						}
						if($paging_count_start < 1){
							$paging_count_start = 1;
						}
					}
					else{
						$paging_count_start = 1;
						$paging_count_end = $paging_count_start + $number_of_page_links;
					}
					$this->pagination['paging_count_start'] = $paging_count_start;
					$this->pagination['paging_count_end'] = $paging_count_end;
					
				}
				
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	
	}
	
	public function exportCSV($selcols=null){
	
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=yearendreport.csv");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		if(is_array($selcols)){
			$cols = $selcols;
		} else {
			$cols = array();
			foreach($this->config->fields as $field){
				$cols[$field['Field']] = $field['Field'];
			}
		}
		
		//print_r($this->config->fields);
		$i=0;
		foreach($cols as $ckey=>$ccaption){
			if($i>0){
				echo ",";
			}
			echo '"' . $ccaption . '"';
			$i++;
		}
		echo "\n";
		
		if(isset($this->resultset)){
			foreach($this->resultset as $item){
				$i=0;
				foreach($cols as $ckey=>$ccaption){
					if($i>0){
						echo ",";
					}
					echo '"' . $item[$ckey] . '"';
					$i++;
				}
				echo "\n";
			}
		}
		
		exit;
	
	}

}


?>