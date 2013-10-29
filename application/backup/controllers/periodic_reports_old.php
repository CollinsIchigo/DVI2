<?php

class Periodic_Reports extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> helper('file');
			$config = Array('protocol' => 'smtp', 'smtp_host' => 'ssl://smtp.googlemail.com', 'smtp_port' => 465, 'smtp_user' => 'dvi.kenya@gmail.com', // change it to yours
			'smtp_pass' => 'summaries', // change it to yours
			'mailtype' => 'html', 'charset' => 'iso-8859-1', 'wordwrap' => TRUE);
		$this -> load -> library('email',$config);
$this->email->set_newline("\r\n");
		$this -> load -> helper('directory');

	}

	public function index() {
		$this -> send_reports();
	}

	public function send_reports() {
		//Create the National report and save it!
		$national_data = $this -> create_national_report();
		$name = 'Country_Vaccine_Status_Summary.xls';
		$this -> write_file($name, $national_data);
		//Create the Regional Reports and Save them
		$regions = Regions::getAllRegions();
		foreach ($regions as $region) {
			$region_data = $this -> create_regional_reports($region);
			$name = $region -> name . "_Vaccine_Status_Summary.xls";
			$this -> write_file($name, $region_data);
		}
		//Email all the reports
		$this -> email_reports();
	}

	function email_reports() {		$this -> email -> from('reports@dvi.co.ke', 'Vaccine Summaries');
		$email_recipients = Email_Recipients::getAll();
		//Retrieve all reports in order to attach them to the email
		$files = directory_map('Summaries');
		//Loop through all files and attach them one by one
		foreach ($files as $file) {
			$this -> email -> attach("Summaries/" . $file);
		}
		$this -> email -> subject('Vaccine Status Summaries For Kenya');
		$this -> email -> message('Vaccine Summaries for all Regional Stores and the Central Vaccine Store are attached.');
		//Retrieve all intended recipients of this email
		foreach ($email_recipients as $recipient) {
			$email = $recipient -> Email;
			$this -> email -> cc($email);
			$this -> email -> send();
			echo $this -> email -> print_debugger();
		}

	}

	function write_file($name, $data) {
		if (!write_file("Summaries/" . $name, $data)) {
			echo 'Unable to write the file';
		} else {
			echo 'File written!';
		}
	}

	function create_national_report() {
		$year = date('Y');
		$headers = "Summary Report for Vaccine Status in Kenya\n\t\nDepot: National Store\tReporting Date: " . date("d/m/Y") . "\t\n";
		$data = "Analytical Areas\t";
		$vaccines = Vaccines::getAll();
		$from = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
		//This sets the begining date as the 1st of january of that particular year
		$to = date('U');
		//This sets the end date as the current time when the report is being generated
		//Loop all vaccines and append the vaccine name in the excel sheet content.
		foreach ($vaccines as $vaccine) {
			$data .= $vaccine -> Name . "\t";
		}
		$data .= "\n";
		//New Line!
		//Begin adding data for the areas being analysed!

		$data .= "Annual Needs Coverage\t";
		//Loop all vaccines and append the needs coverage for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$population = Regional_Populations::getNationalPopulation($year);
			$yearly_requirement = $population * $vaccine -> Doses_Required * $vaccine -> Wastage_Factor;
			$vaccine_totals = Disbursements::getNationalReceiptsTotals($vaccine -> id, $from, $to);
			$coverage = ceil(($vaccine_totals / $yearly_requirement) * 100);
			$data .= $coverage . "%\t";
		}
		$data .= "\n";
		//New Line

		$data .= "Stock Availability (Stock at Hand)\t";
		//Loop all vaccines and append the stock at hand for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$stock_at_hand = Disbursements::getNationalPeriodBalance($vaccine -> id, $to);
			$data .= $stock_at_hand . "\t";
		}
		$data .= "\n";
		//New Line

		$data .= "Stock at Hand Forecast (In Months)\t";
		//Loop all vaccines and append the stock at hand forecast for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$population = Regional_Populations::getNationalPopulation($year);
			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
			$stock_at_hand = Disbursements::getNationalPeriodBalance($vaccine -> id, $to);
			$forecast = $stock_at_hand / $monthly_requirement;
			$data .= $forecast . "\t";
		}
		$data .= "\n";
		//New Line

		$data .= "Shipments Expected Dates\t";
		//Loop all vaccines and append the shipments expected for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$plans = Provisional_Plan::getYearlyPlan($year, $vaccine -> id);
			$plans_string = "";
			foreach ($plans as $plan) {
				$plans_string .= $plan -> expected_date . " (" . $plan -> expected_amount . ") ";
			}
			if (strlen($plans_string) < 1) {
				$plans_string = "None";
			}

			$data .= $plans_string . "\t";
		}
		$data .= "\n";
		//New Line

		$data .= "Shipments received Dates\t";
		//Loop all vaccines and append the shipments received for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$receipts = Batches::getYearlyReceipts($year, $vaccine -> id);
			$receipts_string = "";
			foreach ($receipts as $receipt) {
				$receipts_string .= $receipt -> Arrival_Date . " (" . $receipt -> Total . ") ";
			}
			if (strlen($receipts_string) < 1) {
				$receipts_string = "None";
			}

			$data .= $receipts_string . "\t";
		}
		$data .= "\n";
		//New Line
		/*header("Content-type: application/vnd.ms-excel; name='excel'");
		 header("Content-Disposition: filename=Country_Vaccine_Status_Summary.xls");
		 // Fix for crappy IE bug in download.
		 header("Pragma: ");
		 header("Cache-Control: ");*/
		$result = $headers . $data;
		return $result;
	}

	function create_regional_reports($region) {
		$year = date('Y');
		$headers = "Summary Report for Vaccine Status in Kenya\n\t\nDepot: " . $region -> name . "\tReporting Date: " . date("d/m/Y") . "\t\n";
		$data = "Analytical Areas\t";
		$vaccines = Vaccines::getAll();
		$from = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
		//This sets the begining date as the 1st of january of that particular year
		$to = date('U');
		//This sets the end date as the current time when the report is being generated
		//Loop all vaccines and append the vaccine name in the excel sheet content.
		foreach ($vaccines as $vaccine) {
			$data .= $vaccine -> Name . "\t";
		}
		$data .= "\n";
		//New Line!
		//Begin adding data for the areas being analysed!

		$data .= "Annual Needs Coverage\t";
		//Loop all vaccines and append the needs coverage for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$population = Regional_Populations::getRegionalPopulation($region -> id, $year);
			$yearly_requirement = $population * $vaccine -> Doses_Required * $vaccine -> Wastage_Factor;
			$vaccine_totals = Disbursements::getRegionalReceiptsTotals($region -> id, $vaccine -> id, $from, $to);
			$coverage = ceil(($vaccine_totals / $yearly_requirement) * 100);
			$data .= $coverage . "%\t";
		}
		$data .= "\n";
		//New Line

		$data .= "Stock Availability (Stock at Hand)\t";
		//Loop all vaccines and append the stock at hand for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$stock_at_hand = Disbursements::getRegionalPeriodBalance($region -> id, $vaccine -> id, $to);
			$data .= $stock_at_hand . "\t";
		}
		$data .= "\n";
		//New Line

		$data .= "Stock at Hand Forecast (In Months)\t";
		//Loop all vaccines and append the stock at hand forecast for that particular vaccine in that store
		foreach ($vaccines as $vaccine) {
			$population = Regional_Populations::getRegionalPopulation($region -> id, $year);
			$population = str_replace(",", "", $population);
			$monthly_requirement = ceil(($vaccine -> Doses_Required * $population * $vaccine -> Wastage_Factor) / 12);
			$stock_at_hand = Disbursements::getRegionalPeriodBalance($region -> id, $vaccine -> id, $to);
			$forecast = $stock_at_hand / $monthly_requirement;
			$data .= $forecast . "\t";
		}
		$data .= "\n";
		$result = $headers . $data;
		return $result;
	}

	public function update_timestamps() {
		$disbursements = Disbursements::getAll();
		foreach ($disbursements as $disbursement) {
			$current = $disbursement -> Date_Issued;
			$converted = strtotime($current);
			$test = date("d/m/Y", $converted);
			echo $current . " becomes " . $converted . " which is " . $test . "<br>";
			$disbursement -> Date_Issued_Timestamp = $converted;
			$disbursement -> save();
		}
	}

}
