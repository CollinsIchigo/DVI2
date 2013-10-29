<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class District_Stock_Out extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$this -> view_stock_out_interface();
	}

	public function view_stock_out_interface($offset = 0) {
		$items_per_page = 20;
		$number_of_users = Stock_Out_Recipient::getTotalNumber();
		$users = Stock_Out_Recipient::getPagedUsers($offset, $items_per_page);
		if ($number_of_users > $items_per_page) {
			$config['base_url'] = base_url() . "district_stock_out/view_stock_out_interface/";
			$config['total_rows'] = $number_of_users;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['users'] = $users;
		$data['title'] = "District Stock Outs";
		$data['quick_link'] = "district_stock_out";
		$data['report'] = "district_stock_out_view";
		$this -> base_params($data);
	}

	public function edit_recipient($id) {
		$user = Stock_Out_Recipient::getRecipient($id);
		$data['user'] = $user;
		$this -> add_recipient($data);
	}

	public function add_recipient($data = null) {
		$data['title'] = "District Stock Outs";
		$data['quick_link'] = "district_stock_out";
		$data['districts'] = Districts::getAllDistricts();
		$data['report'] = "add_stock_out_recipient_view";
		$this -> base_params($data);
	}

	public function save() {
		$user_id = $this -> input -> post("user_id");
		$valid = false;
		if ($user_id > 0) {
			//The user is editing! Modify the validation
			$user = Stock_Out_Recipient::getRecipient($user_id);
			$valid = $this -> _submit_validate($user);
		} else {
			$valid = $this -> _submit_validate();
			$user = new Stock_Out_Recipient();
		}
		if ($valid) {
			$name = $this -> input -> post("name");
			$email = $this -> input -> post("email");
			$district = $this -> input -> post("district");
			$user -> Full_Name = $name;
			$user -> Email = $email;
			$user -> Disabled = '0';
			$user -> District = $district;
			$user -> save();

			redirect("district_stock_out");
		} else {
			$this -> add_recipient();
		}
	}

	private function _submit_validate($user = false) {
		// validation rules
		$this -> form_validation -> set_rules('name', 'Full Name', 'trim|required|min_length[2]|max_length[50]');
		$this -> form_validation -> set_rules('email', 'Email Address', 'trim|required|min_length[6]|max_length[50]');
		$this -> form_validation -> set_rules('district', 'District', 'trim|required|min_length[1]|max_length[50]');
		$temp_validation = $this -> form_validation -> run();
		if ($temp_validation) {
			$this -> form_validation -> set_rules('email', 'Email Address', 'trim|required|callback_unique_email');
			return $this -> form_validation -> run();
		} else {
			return $temp_validation;
		}
	}

	public function unique_email($usr) {
		$email = $this -> input -> post("email");
		$district = $this -> input -> post("district");
		$user = Stock_Out_Recipient::getRecipient($this -> input -> post("user_id"));
		$exists = Stock_Out_Recipient::mappingExists($email, $district);
		if ($exists) {
			$user_id = $this -> input -> post("user_id");
			if ($user_id > 0 && $district == $user -> District && $email == $user -> Email) {
				return TRUE;
			} else {
				$this -> form_validation -> set_message('unique_email', 'This Email address is already receiving reports for this district. Please try again.');
				return FALSE;
			}

		} else {
			return TRUE;
		}

	}

	public function change_availability($code, $availability) {
		$user = Stock_Out_Recipient::getRecipient($code);
		$user -> Disabled = $availability;
		$user -> save();
		redirect("district_stock_out");
	}

	private function base_params($data) {
		$data['link'] = "report_management";
		$data['content_view'] = "reports_view";

		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$this -> load -> view('template', $data);
	}

	public function send_emails() {

		$this -> load -> database();
		$period = 'Jun-12';
		//First, retrieve all the districts that have recipients
		$sql = "select distinct district, d.name as district_name from stock_out_recipient s left join districts d on district = d.id where s.disabled = '0'";
		$query = $this -> db -> query($sql);
		$district_array = $query -> result_array();
		//get the total number of vaccines that report dhis data
		$vaccine_number = Vaccines::getTotalDHIS();
		$vaccine_number++;
		//Get the vaccines wiith dhis data
		$vaccines = Vaccines::getDHIS();
		$months = array();
		foreach ($district_array as $district) {
		echo "asd";
			$data_buffer = "
			<style>
			table.data-table {
			table-layout: fixed;
			width: 1000px;
			border-collapse:collapse;
			border:1px solid black;
			margin-top:20px;
			}
			table.data-table td, th {
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
			";
			$data_buffer .= "<table class='data-table'>";
			$data_buffer .= $this -> echoTitles();
			$data_buffer .= "<tr>";
			//loop through all the months in the season
			for ($x = 1; $x <= 12; $x++) {
				//get the month name
				$text = "2012-" . $x . "-1";
				$period = date("M-y", strtotime($text));
				$months[$x] = $period;
				$data_buffer .= "<td>" . $period . "</td>";
			}
			$data_buffer .= "</tr>";
			//Loop through the returned districts, generating a report for each
			$district_name = $district['district_name'];
			$district = $district['district'];

			//get all facilities for this district
			$facilities = Facilities::getDistrictFacilities($district);
			//loop through all the facilities to return immunization data
			foreach ($facilities as $facility) {
				$data_buffer .= "<tr><td rowspan='$vaccine_number'>" . $facility -> name . "</td>";
				$data_buffer .= "<td>Children Immunized</td>";
				echo $facility->name;
				//get the total number of children immunized in this facility
				/*foreach ($months as $month) {
					$sql_children = "select fully_immunized_children from dhis_data where facility_code = '$facility->facilitycode' and reporting_period = '$month'";
					$query_children = $this -> db -> query($sql_children);
					$immunized_children = $query_children -> row_array();
					if (isset($immunized_children['fully_immunized_children'])) {
						$data_buffer .= "<td>" . $immunized_children['fully_immunized_children'] . "</td>";
					} else {
						$data_buffer .= "<td>N/A</td>";
					}

				}*/
				$data_buffer .= "</tr>";

				/*foreach ($vaccines as $vaccine) {
					$data_buffer .= "<tr><td>" . $vaccine -> Name . "</td>";
					$dhis_remaining = $vaccine -> Dhis_Remaining;
					$dhis_received = $vaccine -> Dhis_Received;
					$dhis_stock = $vaccine -> Dhis_Stock;
					foreach ($months as $month) {
						//For each vaccine, create a query to get its dhis data
						$sql_remaining = "select $dhis_remaining, $dhis_received, $dhis_stock from dhis_data where facility_code = '$facility->facilitycode' and reporting_period = '$month'";
						$query_remaining = $this -> db -> query($sql_remaining);
						$remaining_stock = $query_remaining -> row_array();
						//var_dump($query_remaining);

						if (!empty($remaining_stock)) {
							if (strlen($remaining_stock[$dhis_received]) > 0 || strlen($remaining_stock[$dhis_remaining]) > 0 || strlen($remaining_stock[$dhis_stock]) > 0) {
								if ($remaining_stock[$dhis_remaining] == "0" || $remaining_stock[$dhis_remaining] == "" || $remaining_stock[$dhis_remaining] == null) {
									$data_buffer .= "<td style='background-color: red;'>" . $remaining_stock[$dhis_remaining] . "</td>";
								} else {
									$data_buffer .= "<td>" . $remaining_stock[$dhis_remaining] . "</td>";
								}

							} else {
								$data_buffer .= "<td style='text-align:center'>-</td>";
							}
						} else {
							$data_buffer .= "<td style='text-align:center'>-</td>";
						}

					}

				}*/
				$data_buffer .= "</tr>";
			}
			$data_buffer .= "</table>";
			echo $data_buffer;
			//$this -> generatePDF($data_buffer, $district_name);
		}

	}

	public function echoTitles() {
		return "<tr><th rowspan='2'>Facility Name</th><th rowspan='2'>Antigen</th><th colspan='12'>Period</th></tr>";
	}

	function generatePDF($data, $district_name) {
		$html_title = "<img src='Images/coat_of_arms-resized.png' style='position:absolute; width:96px; height:92px; top:0px; left:0px; '></img>";
		$html_title .= "<h3 style='text-align:center; text-decoration:underline; margin-top:-50px;'>" . $district_name . " Stock Status</h3>";

		$this -> load -> library('mpdf');
		$this -> mpdf = new mPDF('c', 'A4-L');
		$this -> mpdf -> SetTitle('District Facility Stock Outs');
		$this -> mpdf -> simpleTables = true;
		$this -> mpdf -> defaultfooterfontsize = 9;
		/* blank, B, I, or BI */
		$this -> mpdf -> defaultfooterline = 1;
		/* 1 to include line below header/above footer */
		$this -> mpdf -> mirrorMargins = 1;
		$mpdf -> defaultfooterfontstyle = B;
		$this -> mpdf -> SetFooter('Generated on: {DATE d/m/Y}|-{PAGENO}-|District Stock Status');
		/* defines footer for Odd and Even Pages - placed at Outer margin */

		$this -> mpdf -> WriteHTML($html_title);
		$this -> mpdf -> WriteHTML($data);
		$this -> mpdf -> WriteHTML($html_footer);
		$report_name = "District Stock Status.pdf";
		$content = $this -> mpdf -> Output($report_name, 'I');
		/*//Add code to send the email
		$content = chunk_split(base64_encode($content));
		$mailto = 'eriknjenga@gmail.com';
		$from_name = 'DVI Stock Management System';
		$from_mail = 'smt@dvi.org';
		$replyto = 'smt@dvi.org';
		$uid = md5(uniqid(time()));
		$subject = 'District Stock Status for '.$district_name;
		$message = 'Attached Herein';
		$filename = 'District Stock Status.pdf';

		$header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
		$header .= "Reply-To: " . $replyto . "\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n\r\n";
		$header .= "This is a multi-part message in MIME format.\r\n";
		$header .= "--" . $uid . "\r\n";
		$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
		$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$header .= $message . "\r\n\r\n";
		$header .= "--" . $uid . "\r\n";
		$header .= "Content-Type: application/pdf; name=\"" . $filename . "\"\r\n";
		$header .= "Content-Transfer-Encoding: base64\r\n";
		$header .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
		$header .= $content . "\r\n\r\n";
		$header .= "--" . $uid . "--";
		$is_sent = mail($mailto, $subject, "", $header);
		var_dump($content);
*/
		// You can now optionally also send it to the browser
		//$this -> mpdf -> Output($report_name, 'I');

	}

	public function validate_form() {
		$this -> form_validation -> set_rules('start_date', 'Start Date', 'trim|required|xss_clean');
		$this -> form_validation -> set_rules('end_date', 'End Date', 'trim|required|xss_clean');
		return $this -> form_validation -> run();
	}

}
