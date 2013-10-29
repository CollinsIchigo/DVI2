<?php
ob_start();
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class auto_sms extends MY_Controller {
 var $flaged="";
	public function index() {
		ini_set("max_execution_time", "500000");


		$user_groups = Emails::getState();
		foreach ($user_groups as $user_group) {

			$national = $user_group["national"];

			//if national is selected
			if ($national == '1') {
				//GET national_id from emails table
				$district_or_region = $national;
				$identifier = "national_officer";

			}

		}

		//current date timestamp
		$today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$from = $today;
		$this -> Calculate_Disbursements($from);
	}//end of index function

	//calculates the disbusrments of all vaccines
	public function Calculate_Disbursements($from) 
	{

		$drugs = Vaccines::getAll();

		foreach ($drugs as $drug) 
		{
			//gets vaccine ID AND NAME
			$ID = $drug['id'];
		   $name = $drug['Name'];

			//Calcutaes stockouts
		$stockouts = Disbursements::getNationalPeriodBalance($ID, $from);
		 // var_dump($stockouts);

			$vnames = Vaccines::getVaccine($ID);
			
			//foreach ($vnames as $vname) 
			//{
			  $flaged = urlencode($name);
			$this->get_sms_level($flaged,$stockouts);
				

		//	}//end of foreach $vnames
	
		}
		

	
	}//end of function	Calculate_Disbursements

	
	
	
	//determines which type of sms to send then gets numbers
	public function get_sms_level($flaged,$stockouts) 
	{
		
		 @$message.="VACCINES+STOCK+OUTS+$flaged"; 	
		//determines which type of sms to send
		$smslevel = Emails::getSmslevel();

		foreach ($smslevel as $levels) 
		{
			$one = $levels['stockout'];
			$two = $levels['consumption'];
			$three = $levels['coldchain'];
		
			if($one == 1) 
			{
				//gets phone number of the record
	 $phones = $levels['number'];
			      
			$this->send_sms($phones,$message,$stockouts);  

			}//end of if one

		
		//$this->send_sms($phones);

		}//end of foreach $smslevel
		
	}//end of function send_sms_level
	
	
	//this sends the sms to all recepients regarding the stocked out vaccine *_*
	public function send_sms($phones,$message,$stockouts)
	{
	if($stockouts <0)
	{	
   echo  @$message.="+AT+NATIONAL+STORE+%0A+*+DVI+-+SMT*".$phones.'<br/>';
  
  //file_get_contents("http://41.215.78.124:13000/cgi-bin/sendsms?username=clinton&password=ch41sms&to=$phones&text=$message");		           
     	
	}
	
	
	}
	


}
