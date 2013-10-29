<?php

class Facility_Management extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this -> load -> library('pagination');
	}

	public function index() {
		$this -> view_list();
	}

	public function whole_list($offset = 0) {
		$items_per_page = 20;
		$number_of_facilities = Facilities::getTotalNumber();
		$facilities = Facilities::getPagedFacilities($offset, $items_per_page);
		if ($number_of_facilities > $items_per_page) {
			$config['base_url'] = base_url() . "facility_management/whole_list/";
			$config['total_rows'] = $number_of_facilities;
			$config['per_page'] = $items_per_page;
			$config['uri_segment'] = 3;
			$config['num_links'] = 5;
			$this -> pagination -> initialize($config);
			$data['pagination'] = $this -> pagination -> create_links();
		}

		$data['facilities'] = $facilities;
		$data['title'] = "Facility Management::All Facilities";
		$data['module_view'] = "view_facilities_view";
		$this -> new_base_params($data);
	}

	public function view_list() {
		$additional_facilities = new Additional_Facilities();
		$returned = $additional_facilities -> getExtraFacilities($this -> session -> userdata('district_province_id'));
		$data['facilities'] = $returned;
		$data['title'] = "Facility Management::All My Facilities";
		$data['content_view'] = "view_extra_facilities_view";
		$this -> base_params($data);
	}

	public function add() {
		$data['title'] = "Facility Management::Add Extra Facility";
		$data['content_view'] = "add_extra_facility_view";
		$data['quick_link'] = "new_extra_facility";
		$this -> base_params($data);
	}

	public function search() {
		$search_term = $this -> input -> post('search');
		$data['facilities'] = Facilities::search($search_term);
		$data['search_term'] = $search_term;
		$data['title'] = "Facility Management::Click on a Facility";
		$data['content_view'] = "search_facilities_result_view";
		$this -> base_params($data);
	}

	public function save($code) {
		$additional_facility = new Additional_Facilities();
		$exists = $additional_facility -> record_exists($this -> session -> userdata('district_province_id'), $code);
		if (!$exists) {
			$additional_facility -> District_Id = $this -> session -> userdata('district_province_id');
			$additional_facility -> Facility = $code;
			$additional_facility -> Added_By = $this -> session -> userdata('user_id');
			$additional_facility -> Timestamp = date('U');
			$additional_facility -> save();
		}
		redirect("facility_management");
	}

	public function remove($code) {
		$facility = Additional_Facilities::get_facility($this -> session -> userdata('district_province_id'), $code);
		$facility -> delete();
		redirect("facility_management");
	}

	private function base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['link'] = "facilities";
		$this -> load -> view('template', $data);

	}

	private function new_base_params($data) {
		$data['scripts'] = array("jquery-ui.js", "tab.js");
		$data['styles'] = array("jquery-ui.css", "tab.css");
		$data['content_view'] = "admin_view";
		$data['quick_link'] = "facility_management";
		$data['link'] = "system_administration";
		$this -> load -> view('template', $data);

	}

}
