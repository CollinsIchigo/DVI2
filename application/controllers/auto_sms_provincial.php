<?php
ob_start();
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class auto_sms_provincial extends MY_Controller 
{
 public function index()
	 {
		//ini_set("max_execution_time", "500000");


	$user_groups = Emails::getStateprovincial();
	
		foreach ($user_groups as $user_group) 
		{

		$provincial = $user_group["provincial"];
      $district_or_region = $provincial;
		//if provincial is selected
			if ($provincial >0) 
			{
				$district_or_region = $provincial;
				 $region=Regions::getRegionName($district_or_region);
       $region_name=urlencode($region);	


	
			
$this -> get_sms_level($region_name,$district_or_region);
		}
			
		}	
	
	}//end of index function

	
	
	//determines which type of sms to send then gets numbers
	public function get_sms_level($region_name,$district_or_region) 
	{
		
		
		//determines which type of sms to send
		$smslevel = Emails::getSmslevel_provincial();

		foreach ($smslevel as $levels) 
		{
			//$one = $levels['stockout'];
			$two = $levels['consumption'];
		//$three = $levels['coldchain'];

		}//end of foreach $smslevel
		
		
			if($two == 1) 
			{
				$number = Emails::getprovincial_number();

		$vartot = array();
foreach ($number as $numbers) {
$vartot[] = $numbers['number'];
}
   
 $phones = implode(",",$vartot);
//$this->getBalances($phones,$district_or_region,$region_name);  
			}//end of if one
			
			
		
	}//end of function send_sms_level
	
	
	
	
//gets the 2 necessary variables to pass to consumption function
	public function getBalances($phones,$district_or_region,$region_name)
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
					
		$messsage=number_format($closing_balance);
		 if($messsage==0) 
		 {
  $name= $vaccine -> Name;
     $region= $region_name;
	 $this->Send_Balanaces($phones,$messsage,$region,$name);
	 
		   }
			
		}
// $this->Send_Balanaces($phones,$messsage,$region,$name);

		}



public function Send_Balanaces($phones,$messsage,$region,$name)
{
	$title="VACCINES+STOCK+OUT+$name+AT+($region)++"; 	
 $footer="+%0A+*+DVI+-+SMT*";
 echo $phones.'</BR>';
echo $title.$messsage.$footer.'</br>';
//$z = file_get_contents("http://41.215.78.124:13000/cgi-bin/sendsms?username=clinton&password=ch41sms&to=$phones&text=$title.$messsage.$footer");
	
}
 


}
	