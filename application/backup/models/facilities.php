<?php
class Facilities extends Doctrine_Record {
	public function setTableDefinition() {
		$this -> hasColumn('facilitycode', 'int', 32);
		$this -> hasColumn('name', 'varchar', 100);
		$this -> hasColumn('ftype', 'varchar', 32);
		$this -> hasColumn('facilitytype', 'int', 32);
		$this -> hasColumn('owner', 'int', 32);
		$this -> hasColumn('district', 'int', 32);
		$this -> hasColumn('flag', 'int', 1);
	}

	public function setUp() {
		$this -> setTableName('facilities');
		$this -> hasOne('Districts as Parent_District', array('local' => 'district', 'foreign' => 'id'));
	}

	public function getDistrictFacilities($district) {
		$query = Doctrine_Query::create() -> select("facilitycode,name") -> from("Facilities") -> where("District = '" . $district . "'");
		$facilities = $query -> execute();
		return $facilities;
	}

	public static function search($search) {
		$query = Doctrine_Query::create() -> select("facilitycode,name") -> from("Facilities") -> where("name like '%" . $search . "%'");
		$facilities = $query -> execute();
		return $facilities;
	}

	public static function getFacilityName($facility_code) {
		$query = Doctrine_Query::create() -> select("name") -> from("Facilities") -> where("facilitycode = '$facility_code'");
		$facility = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $facility[0]['name'];
	}

	public static function getTotalNumber() {
		$query = Doctrine_Query::create() -> select("COUNT(*) as Total_Facilities") -> from("Facilities");
		$count = $query -> execute();
		return $count[0] -> Total_Facilities;
	}

	public function getPagedFacilities($offset, $items) {
		$query = Doctrine_Query::create() -> select("*") -> from("Facilities") -> orderBy("name") -> offset($offset) -> limit($items);
		$facilities = $query -> execute();
		return $facilities;
	}

}
