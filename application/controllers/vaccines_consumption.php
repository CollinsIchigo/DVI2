<?php
ob_start();
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class vaccines_consumption extends MY_Controller {
 var $flaged="";
	public function index() {
		//ini_set("max_execution_time", "500000");


		$user_groups = Emails::getState();
		foreach ($user_groups as $user_group) {

			$national = $user_group["national"];

			//if national is selected
			if ($national == '1') {
			
$this -> get_sms_level();
			}

		}

		
	}//end of index function

	
	
	
	//determines which type of sms to send then gets numbers
	public function get_sms_level() 
	{
		
	
		$smslevel = Emails::getSmslevel();

		foreach ($smslevel as $levels) 
		{
			$one = $levels['stockout'];
			$two = $levels['consumption'];
			$three = $levels['coldchain'];
	
		
			if($two == 1) 
			{
//gets phone number of the record
$phones = $levels['number'];
		$this->getBalances($phones);
			}//end of if one
			
		//$this->getBalances($phones);
		}//end of foreach $smslevel
				
		
	}//end of function send_sms_level
		
	
	
//gets the 2 necessary variables to pass to consumption function
	public function getBalances($phones)
 {		
		
		
		$this -> load -> database();
		$start_date = "";
		$data_buffer = "";
		$number = "";
		@$start_date ==  date('m/d/y', strtotime('-30 days'));
		@$end_date = date('m/d/y');

		$population = 0;
		$opening_balance = 0;
		$closing_balance = 0;
		$sql_consumption = "";
	
		$vaccines = Vaccines::getAll_Minified();
		
		//gets consumption as per every vaccine
		foreach ($vaccines as $vaccine) 
		{
	
                   $population = Regional_Populations::getNationalPopulation(date('Y'));
					$opening_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($start_date));
					$closing_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($end_date));
					$sql_consumption = "select (SELECT max(str_to_date(Date_Issued,'%m/%d/%Y'))  FROM `disbursements` where Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "' and total_stock_balance>0)as last_stock_count,(SELECT sum(Quantity)FROM `disbursements` where Issued_By_National = '0' and Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
                    str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,(SELECT sum(Quantity) FROM `disbursements` where Issued_To_National = '0' and Owner = 'N0' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
                    str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
					$query = $this -> db -> query($sql_consumption);
					$vaccine_data = $query -> row();
					$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12); 
			$space1=urldecode("++++");
		$space2=urldecode("++");
			$newline=urldecode("%0A");
	
		 $messsage=urlencode($data_buffer .= $newline. $vaccine -> Name.$space2. number_format($closing_balance + 0) .$space1 . number_format(($closing_balance / $monthly_requirement), 1).'MOS');

			//end of foreach vaccine
		}
$this->Send_Balanaces($phones,$messsage);

	
		}


public function Send_Balanaces($phones,$messsage)
{
	 $title="VACCINES+STOCK+BALANCES+(IN+DOSES+-+MOS+AT+NATIONAL+STORE+-+)++"; 	
 $footer="+%0A+*+DVI+-+SMT*";
 ECHO $P=urldecode($title.'</br>'.$messsage.'</br>'.$footer.'</br>');
 //$z = file_get_contents("http://41.215.78.124:13000/cgi-bin/sendsms?username=clinton&password=ch41sms&to=$phones&text=$title.$messsage.$footer");
	
}

}
