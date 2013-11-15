<?php
ob_start();
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class auto_email extends MY_Controller 
{
	function __construct() 
	{
		parent::__construct();
		$this -> load -> library('pagination');
		//LOADING HELPERS TO BECOME AVAILBALE IN ALL CONSTRUCTOR METHODS
		$this -> load -> helper(array('form', 'url','file'));
		//$this->data="";

	}

	public function index() 
	{
		$from = date('m/d/y', strtotime('-30 days'));
		$to = date("m/d/y");
		$this -> printReport($from, $to);
	
	}

	public function echoTitles() 
	{
		return "<tr>
				<thead>
					<th>Vaccine</th>
					<th>Opening Stock</th>
					<th>Total Receipts</th>
					<th>Total Issued</th>
					<th>Adjustments</th>
					<th>Closing Stock</th>
					<th>MOS Balance</th>
					<th>Date of last physical count</th>
				</thead>
				</tr>
				<tbody>";
	}

	public function printReport($from, $to) 
	{
		@$this -> load -> database();
		@$start_date = $from;
		@$end_date = $to;
		@$national_data="";
		@$national_data .= "
					<style>
						table.data-table 
						{
							table-layout: fixed;
							width: 700px;
							border-collapse:collapse;
							border:1px solid black;
						}
						table.data-table td, th 
						{
							width: 100px;
							border: 1px solid black;
						}
						.leftie{
							text-align: left !important;
						}
						.right{
							text-align: right !important;
						}
						.center{
							text-align: center !important;
						}
					</style>
					<div>";
		@$national_data .= "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '>
						</img>";
		@$national_data .= "<h3 style='text-align:center;'>Vaccine Consumption Summary For National Store</h3>";
		@$national_data .= "<h5 style='text-align:center;'> from: " . $start_date . " to: " . $end_date . "</h5></div>";
		@$national_data .= "<table class='data-table'>";
		@$national_data .= $this -> echoTitles();
		
		@$data_buffer = "";
		@$vaccines = Vaccines::getAll_Minified();
		
		//picks all the provinces from the provinces table
		//they are 9 in total
		@$provinces = Provinces::getAllProvinces();
		$counter=0;
		foreach ($provinces as $province) 
		{
			//@$start_date = $from;
			//@$end_date = $to;
			@$district_or_region = $province["id"];
			//gets the regions from the regions table.
			//there are 8 regions in the country
			//Kakamega is skipped as its ID is 4 and there is no province 4
			@$region_object = Regions::getRegion($district_or_region);
			@$store = $region_object -> name;
			
			//For each of the regions
			@$data_buffer .= "<div>";
			
			@$data_buffer .= "<h3 style='text-align:center;'>Vaccine Consumption Summary For " . $store . "</h3 >";
			@$data_buffer .= "<h5 style='text-align:center;'> from: " . $start_date . " to: " . $end_date . "</h5></div>";
			@$data_buffer .= "<table class='data-table'>";
			@$data_buffer .= $this -> echoTitles();
			foreach ($vaccines as $vaccine)
			{
				//for the National Store Table	
				if($counter==0):
					@$population = Regional_Populations::getNationalPopulation(date('Y'));
					@$opening_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($start_date));
					@$closing_balance = Disbursements::getNationalPeriodBalance($vaccine -> id, strtotime($end_date));
					@$owner = "N0";
					@$sql_consumption = "select (SELECT max(str_to_date(Date_Issued,'%m/%d/%Y'))  
										FROM `disbursements` where Owner = '" . $owner . "' 
										and str_to_date(Date_Issued,'%m/%d/%Y') 
										between str_to_date('" . $start_date . "','%m/%d/%Y') 
										and str_to_date('" . $end_date . "','%m/%d/%Y') 
										and Vaccine_Id = '" . $vaccine -> id . "' 
										and total_stock_balance>0)as last_stock_count,
										(SELECT sum(Quantity)FROM `disbursements` 
										where Issued_By_Region = '" . $district_or_region . "' 
										and Owner = '" . $owner . "' 
										and str_to_date(Date_Issued,'%m/%d/%Y') 
										between str_to_date('" . $start_date . "','%m/%d/%Y') 
										and str_to_date('" . $end_date . "','%m/%d/%Y') 
										and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,
										(SELECT sum(Quantity) FROM `disbursements` 
										where Issued_To_Region = '" . $district_or_region . "' 
										and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') 
										between str_to_date('" . $start_date . "','%m/%d/%Y')
										and str_to_date('" . $end_date . "','%m/%d/%Y') 
										and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
					
					@$query = $this -> db -> query($sql_consumption);
					@$vaccine_data = $query -> row();
					@$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
					@$national_data .= "<tr>
											<td>" . $vaccine -> Name . "</td>
											<td>" . number_format($opening_balance + 0) . "</td>
											<td>" . number_format($vaccine_data -> total_received + 0) . "</td>
											<td>" . number_format($vaccine_data -> total_issued + 0) . "</td>
											<td>" . number_format(($closing_balance - ($opening_balance + $vaccine_data -> total_received - $vaccine_data -> total_issued)) + 0) . "</td>
											<td>" . number_format($closing_balance + 0) . "</td>
											<td>" . number_format(($closing_balance / $monthly_requirement), 1) . "</td>
											<td>" . $vaccine_data -> last_stock_count . "</td>
										</tr>";
					@$owner = "";
							
				endif;
					
					@$population = Regional_Populations::getRegionalPopulation($district_or_region, date('Y'));
					@$opening_balance = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, strtotime($start_date));
					@$closing_balance = Disbursements::getRegionalPeriodBalance($district_or_region, $vaccine -> id, strtotime($end_date));
					@$owner = "R" . $district_or_region;
					@$sql_consumption = "select (SELECT max(str_to_date(Date_Issued,'%m/%d/%Y'))  
											FROM `disbursements` where Owner = '" . $owner . "' 
											and str_to_date(Date_Issued,'%m/%d/%Y') 
											between str_to_date('" . $start_date . "','%m/%d/%Y') 
												and str_to_date('" . $end_date . "','%m/%d/%Y') 
												and Vaccine_Id = '" . $vaccine -> id . "' 
												and total_stock_balance>0)as last_stock_count,
												(SELECT sum(Quantity)FROM `disbursements` 
												where Issued_By_Region = '" . $district_or_region . "' 
												and Owner = '" . $owner . "' 
												and str_to_date(Date_Issued,'%m/%d/%Y') 
												between str_to_date('" . $start_date . "','%m/%d/%Y') 
												and str_to_date('" . $end_date . "','%m/%d/%Y') 
												and Vaccine_Id = '" . $vaccine -> id . "')as total_issued,
												(SELECT sum(Quantity) FROM `disbursements` 
												where Issued_To_Region = '" . $district_or_region . "' 
												and Owner = '" . $owner . "' and str_to_date(Date_Issued,'%m/%d/%Y') 
												between str_to_date('" . $start_date . "','%m/%d/%Y')
												and str_to_date('" . $end_date . "','%m/%d/%Y') 
												and Vaccine_Id = '" . $vaccine -> id . "')as total_received";
												
					@$query = $this -> db -> query($sql_consumption);
					@$vaccine_data = $query -> row();
					@$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
					@$data_buffer .= "<tr>
										<td>" . $vaccine -> Name . "</td>
										<td>" . number_format($opening_balance + 0) . "</td>
										<td>" . number_format($vaccine_data -> total_received + 0) . "</td>
										<td>" . number_format($vaccine_data -> total_issued + 0) . "</td>
										<td>" . number_format(($closing_balance - ($opening_balance + $vaccine_data -> total_received - $vaccine_data -> total_issued)) + 0) . "</td>
										<td>" . number_format($closing_balance + 0) . "</td>
										<td>" . number_format(($closing_balance / $monthly_requirement), 1) . "</td>
										<td>" . $vaccine_data -> last_stock_count . "</td>
									</tr>";
					
			}//end of foreach vaccines
			
			$counter=$counter+1;
			@$data_buffer .= "</tbody></table>";
			@$district_or_region="";
		}//end of foreach provinces
		@$this -> generatePDF($national_data."</tbody></table>".$data_buffer, $start_date, $end_date, $store, $district_or_region, $store);
	}//end of function

	function generatePDF($data, $start_date, $end_date, $store, $district_or_region, $store) 
	{
		$logo = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '>
						</img>";
		$this -> load -> library('mpdf');
		$this->load->helper('file');
		$this -> mpdf = new mPDF('', 'A4-L',0,"",15,15,16,16,9,9,"");
		$this -> mpdf -> SetTitle('Vaccine Consumption');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> WriteHTML($data);
		//$this -> mpdf -> Output($report_name, 'I');
		$report_name = "Vaccine Consumption for $store.pdf";
		$path = $_SERVER["DOCUMENT_ROOT"];
		$handler = $path . "/DVI/application/pdf/" . $report_name;
		write_file($handler,$this -> mpdf -> Output($report_name, 'S'));
		$this -> email_sender($report_name, $start_date, $end_date, $district_or_region, $vals,$store);
		
	}

	//function that does the actual sending and design of the pdf
	function email_sender($report_name, $start_date, $end_date, $district_or_region, $vals,$store) 
	{
		//setting the connection variables
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'ssl://smtp.googlemail.com';
		$config['smtp_port'] = 465;
		$config['smtp_user'] = stripslashes('D.V.I.VaccinesKenya@gmail.com');
		$config['smtp_pass'] = stripslashes('projectDVI');
		ini_set("SMTP", "ssl://smtp.gmail.com");
		ini_set("smtp_port", "465");
		ini_set("max_execution_time", "50000");
		
		$emails = Emails::getEmails();
	
		//pulling emails from the DB
		$this -> load -> library('email', $config);
		$path = $_SERVER["DOCUMENT_ROOT"];
		$file = $path . "/DVI/application/pdf/" . $report_name;
		//puts the path where the pdf's are stored
		
		foreach ($emails as $email) 
		{
            $this -> email -> attach($file);
			$address = $email['email'];
			$this -> email -> set_newline("\r\n");
			$this -> email -> from('markarski@gmail.com', "DVI MAILER");
			//user variable displays current user logged in from sessions
			$this -> email -> to("$address");
			$this -> email -> subject('MONTHLY REPORT FOR '."$store");
			$this -> email -> message('Please find the Report Attached for '."$store".' Period of ' . "$start_date" . ' to ' . "$end_date");

			//success message else show the error
			if ($this -> email -> send()) 
			{
				echo 'Your email was sent, successfully to ' . $address . '<br/>';
				//unlink($file);
				$this -> email -> clear(TRUE);

			} else 
			{
				show_error($this -> email -> print_debugger());
			}

		}
		ob_end_flush();
		unlink($file);
		//delete the attachment after sending to avoid clog up of pdf's
	}

}
