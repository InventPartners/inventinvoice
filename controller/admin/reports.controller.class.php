<?php

class ReportsController extends InvController{

	public $manufacturers;

	public function __construct($obj_db , $obj_session , $view_template , $path_info){
		parent::__construct($obj_db , $obj_session , $view_template , $path_info);
		$this->view_title = 'Reports';
		$this->view_area = 'reports';
	}
	
	protected function setInputValues(){
		$this->setInputValue('page' , true);
	}
	
	public function doController(){
	
		$ordersreportquery = 'SELECT DATE(`invoice_date`) as `date` , SUM(`invoice_total`) AS total 
			FROM `invoice`
			WHERE `invoice_date` <= NOW()
			AND DATE_ADD(`invoice_date` , INTERVAL 1 MONTH) > NOW()
			AND `invoice_status` != "void"
			GROUP BY DAY(`invoice_date`)
			ORDER BY `invoice_date`';
	
		$this->plots = array();
		$lastmonth = mktime(0, 0, 0, date("m")-1, date("d"),   date("Y"));
		$this->dates = $this->findAllDates(date('Y-m-d' , $lastmonth) , date('Y-m-d'));
		if($this->obj_db->prepareAndDoQuery($ordersreportquery , array())){
			$data = array();
			while($row = $this->obj_db->fetch()){
				$data[$row['date']] = $row['total'];
			}
			for($i=0; $i<count($this->dates); $i++){
				$plotpoint = array();
				$plotpoint['caption'] = $this->dates[$i]['jqplotdate'];
				if(isset($data[$this->dates[$i]['isodate']])){
					$plotpoint['value'] = $data[$this->dates[$i]['isodate']];
					$this->plots[] = $plotpoint;
				} else {
					if($i==0){
						$plotpoint['value'] = 0;
						$this->plots[] = $plotpoint;
					} else {
						//$plotpoint['value'] = '';
					}
				}
			}
		}
		
		
	
		$purchasereportquery = 'SELECT DATE(`purchase_date`) as `date` , SUM(`purchase_total`) AS total 
			FROM `purchase`
			WHERE `purchase_date` <= NOW()
			AND DATE_ADD(`purchase_date` , INTERVAL 1 MONTH) > NOW()
			AND `purchase_status` != "void"
			GROUP BY DAY(`purchase_date`)
			ORDER BY `purchase_date`';
	
		$this->plots2 = array();
		if($this->obj_db->prepareAndDoQuery($purchasereportquery , array())){
			$data = array();
			while($row = $this->obj_db->fetch()){
				$data[$row['date']] = $row['total'];
			}
			for($i=0; $i<count($this->dates); $i++){
				$plotpoint = array();
				$plotpoint['caption'] = $this->dates[$i]['jqplotdate'];
				if(isset($data[$this->dates[$i]['isodate']])){
					$plotpoint['value'] = $data[$this->dates[$i]['isodate']];
					$this->plots2[] = $plotpoint;
				} else {
					if($i==0){
						$plotpoint['value'] = 0;
						$this->plots2[] = $plotpoint;
					} else {
						//$plotpoint['value'] = '';
					}
				}
			}
		}
		
		
		// Year data
	
		$ordersreportquery = 'SELECT MONTH(`invoice_date`) as `date` , SUM(`invoice_total`) AS total 
			FROM `invoice`
			WHERE `invoice_date` <= NOW()
			AND DATE_ADD(`invoice_date` , INTERVAL 1 YEAR) > NOW()
			AND `invoice_status` != "void"
			GROUP BY MONTH(`invoice_date`)
			ORDER BY `invoice_date`';
	
		$this->plots3 = array();
		$lastyear = mktime(0, 0, 0, date("m"), date("d"),   date("Y")-1);
		$this->months = $this->findAllMonths(date("m") , date("Y"));
		if($this->obj_db->prepareAndDoQuery($ordersreportquery , array())){
			$data = array();
			while($row = $this->obj_db->fetch()){
				$data[intval($row['date'])] = $row['total'];
			}
			for($i=0; $i<count($this->months); $i++){
				$plotpoint = array();
				$plotpoint['caption'] = $this->months[$i]['jqplotdate'];
				if(isset($data[intval($this->months[$i]['month'])])){
					$plotpoint['value'] = $data[intval($this->months[$i]['month'])];
					$this->plots3[] = $plotpoint;
				} else {
					if($i==0){
						$plotpoint['value'] = 0;
						$this->plots3[] = $plotpoint;
					} else {
						//$plotpoint['value'] = '';
					}
				}
			}
		}
		
		
	
		$purchasereportquery = 'SELECT MONTH(`purchase_date`) as `date` , SUM(`purchase_total`) AS total 
			FROM `purchase`
			WHERE `purchase_date` <= NOW()
			AND DATE_ADD(`purchase_date` , INTERVAL 1 YEAR) > NOW()
			AND `purchase_status` != "void"
			GROUP BY MONTH(`purchase_date`)
			ORDER BY `purchase_date`';
	
		$this->plots4 = array();
		if($this->obj_db->prepareAndDoQuery($purchasereportquery , array())){
			$data = array();
			while($row = $this->obj_db->fetch()){
				$data[intval($row['date'])] = $row['total'];
			}
			for($i=0; $i<count($this->months); $i++){
				$plotpoint = array();
				$plotpoint['caption'] = $this->months[$i]['jqplotdate'];
				if(isset($data[intval($this->months[$i]['month'])])){
					$plotpoint['value'] = $data[intval($this->months[$i]['month'])];
					$this->plots4[] = $plotpoint;
				} else {
					if($i==0){
						$plotpoint['value'] = 0;
						$this->plots4[] = $plotpoint;
					} else {
						//$plotpoint['value'] = '';
					}
				}
			}
		}
		
		
		
	}
	
	protected function findAllDates($date_from , $date_to){
	
		list($y_from , $m_from , $d_from) = explode('-' , $date_from);
		list($y_to , $m_to , $d_to) = explode('-' , $date_to);

		$start_d = $d_from;
		$start_y = $y_from;
		$start_m = $m_from;
		$end_y = $y_to;
		$end_day_time = mktime(0 , 0 , 0 , $m_to , $d_to , $y_to);
		$all_dates = array();
		$done = false;
		for($y=intval($start_y); $y<=intval($end_y); $y++){
			for($m=$start_m; $m<=12; $m++){
				if($done){
					break;
				}
				$days_in_month = cal_days_in_month(CAL_GREGORIAN, $m, $y);
				for($d=$start_d; $d<=$days_in_month; $d++){
					$row = array();
					$row['year'] = $y;
					$row['month'] = str_pad($m , 2 , '0' , STR_PAD_LEFT);
					$row['day'] = str_pad($d , 2 , '0' , STR_PAD_LEFT);
					$row['isodate'] = $y . '-' . str_pad($m , 2 , '0' , STR_PAD_LEFT) . '-' . str_pad($d , 2 , '0' , STR_PAD_LEFT);
					$row['jqplotdate'] = $m . '/' . $d . '/' . $y;

					$all_dates[] = $row;
					$current_day_time = mktime(0 , 0 , 0 , $m , $d , $y);
					//if($d==(intval($value['date_to_d']) - 1) && $m==intval($value['date_to_m']) && $y==intval($value['date_to_y'])){
					if($current_day_time == $end_day_time){
						$done = true;
						return $all_dates;
						break;
					}
				}
				$start_d = 1;
			}	
			$start_m = 1;
		}		
	
	}
	
	protected function findAllMonths($month , $year){

		$all_dates = array();
	
		$start = $month - 11;
		for($i=$start; $i<=$month; $i++){
			$cur_month = $i;
			$cur_year = $year;
			if($i < 1){
				$cur_month = 12 + $i;
				$cur_year = $year - 1;
			}
			$row = array();
			$row['year'] = $year;
			$row['month'] = str_pad($cur_month , 2 , '0' , STR_PAD_LEFT);
			$row['day'] = '01';
			$row['isodate'] = $cur_year . '-' . str_pad($cur_month , 2 , '0' , STR_PAD_LEFT) . '-01';
			$row['jqplotdate'] = $cur_month  . '/1/' . $cur_year;

			$all_dates[] = $row;			
			
		}
				
		return $all_dates;
	
	}

}

?>