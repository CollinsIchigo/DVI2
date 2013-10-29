<?php
ob_start();
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class vaccine_consumption_provincial extends MY_Controller {

	public function index()
	 {
		ini_set("max_execution_time", "500000");


	$user_groups = Emails::getStateprovincial();
		foreach ($user_groups as $user_group) {

		$provincial = $user_group["provincial"];
 $district_or_region = $provincial;
		//if provincial is selected
			if ($provincial >0) 
			{
				 $region=Regions::getRegionName($district_or_region);
            $region_name=urlencode($region);	
	$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$from = $today;
	
	
			
 $this -> get_sms_level($region_name,$from,$district_or_region);
		}
		}	
	
	}//end of index function

	
	
	//determines which type of sms to send then gets numbers
	public function get_sms_level($region_name,$from,$district_or_region) 
	{
		
		//determines which type of sms to send
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
		 $this->getBalances($phones,$from,$district_or_region,$region_name);       
				

			}//end of if one
	
		}//end of foreach $smslevel
				
		
	}//end of function send_sms_level
	
	
	
	
//gets the 2 necessary variables to pass to consumption function
	public function getBalances($phones,$from,$district_or_region,$region_name)
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
	$region_object = Regions::getRegion($district_or_region);

					$store = $region_object -> name;
					$population = Regional_Populations::getRegionalPopulation($district_or_region, date('Y'));
					$opening_balance = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, strtotime($start_date));
					$closing_balance = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, strtotime($end_date));
					$owner = "R" . $district_or_region;
					$sql_consumption = "select (SELECT date_format(max(str_to_date(Date_Issued,'%m/%d/%Y')),'%d-%b-%Y')  FROM `disbursements` where Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "' and total_stock_balance>0)as last_stock_count,(SELECT sum(Quantity)FROM `disbursements` where Issued_By_Region = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
str_to_date('" . $end_date . "','%m/%d/%Y') and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,(SELECT sum(Quantity) FROM `disbursements` where Issued_To_Region = '" . $district_or_region . "' and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') between str_to_date('" . $start_date . "','%m/%d/%Y') and
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
 $this->Send_Balanaces($phones,$messsage,$region_name);

	
}



public function Send_Balanaces($phones,$messsage,$region_name)
{
	 $title="VACCINES+STOCK+BALANCES+(IN+DOSES+-+MOS+AT+$region_name)++"; 	
 $footer="+%0A+*+DVI+-+SMT*";
echo $title.$messsage.'</br>';
//$z = file_get_contents("http://41.215.78.124:13000/cgi-bin/sendsms?username=clinton&password=ch41sms&to=$phones&text=$title.$messsage.$footer");

}

}
