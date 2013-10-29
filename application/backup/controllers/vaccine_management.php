<?php

class Vaccine_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		if ($this -> session -> userdata('user_group') >= 2) {
			redirect("user_management");
		}
	}

	public function index() {
		$this -> view_vaccines();
	}

	public function new_vaccine() {
		$data['title'] = "Vaccine Management::Add New Vaccine";
		$data['module_view'] = "add_vaccine_view";
		$data['scripts'] = array("jquery-ui.js", "colorpicker/js/colorpicker.js");
		$data['styles'] = array("jquery-ui.css", "colorpicker/css/colorpicker.css"); 
		$this -> base_params($data);
	}

	public function view_vaccines() {
		$data['vaccines'] = Vaccines::getAll();
		$data['title'] = "Vaccine Management::All Vaccines";
		$data['module_view'] = "view_vaccines_view";
		$this -> base_params($data);
	}

	public function save_vaccine() {
		$vaccine = new Vaccines();
		$vaccine -> Name = $this -> input -> post("name");
		$vaccine -> Doses_Required = $this -> input -> post("doses_required");
		$vaccine -> Wastage_Factor = $this -> input -> post("wastage_factor");
		$vaccine -> Designation = $this -> input -> post("designation");
		$vaccine -> Formulation = $this -> input -> post("formulation");
		$vaccine -> Administration = $this -> input -> post("administration");
		$vaccine -> Presentation = $this -> input -> post("presentation");
		$vaccine -> Vaccine_Packed_Volume = $this -> input -> post("vaccine_packed_volume");
		$vaccine -> Diluents_Packed_Volume = $this -> input -> post("diluents_packed_volume");
		$vaccine -> Vaccine_Vial_Price = $this -> input -> post("vaccine_vial_price");
		$vaccine -> Vaccine_Dose_Price = $this -> input -> post("vaccine_dose_price");
		$vaccine -> Added_By = $this -> session -> userdata('user_id');
		$vaccine -> Timestamp = date('U');
		$vaccine -> Tray_Color = $this -> input -> post("tray_color");
		$vaccine -> save();
		redirect("vaccine_management");
	}

	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js");
		$data['styles'] = array("jquery-ui.css");
		$data['quick_link'] = "vaccine_management";
		$data['link'] = "system_administration";
		$data['content_view'] = "admin_view";
		$this -> load -> view('template', $data);

	}

}
