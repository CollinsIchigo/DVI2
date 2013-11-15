<?php

class Emails extends Doctrine_Record {

	public function setTableDefinition() {
		$this -> hasColumn('email', 'varchar', 200);
		$this -> hasColumn('provincial', 'varchar', 30);
		$this -> hasColumn('district', 'varchar', 30);
		$this -> hasColumn('national', 'int', 11);
		$this -> hasColumn('valid', 'int', 11);
		$this -> hasColumn('stockout', 'int', 11);
		$this -> hasColumn('consumption', 'int', 10);
		$this -> hasColumn('coldchain', 'int', 10);
		$this -> hasColumn('recepient', 'varchar', 50);
		$this -> hasColumn('number', 'varchar', 50);
	}

	public function setUp() {
		$this -> setTableName('emails');
	}

	//assists to dosplay data from db to view
	public static function emailandsms() {
		$query = Doctrine_Query::create() -> select("id,number,email,stockout,consumption,coldchain,recepient,valid") -> from("emails") -> orderBy("id");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getNational() {
		$query = Doctrine_Query::create() -> select("id,number,email,stockout,consumption,coldchain,recepient,valid") -> from("emails") -> where("national = '1' and valid = '1'") -> orderBy("id");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getRegional($region) {
		$query = Doctrine_Query::create() -> select("id,number,email,stockout,consumption,coldchain,recepient,valid") -> from("emails") -> where("provincial = '$region'  and valid = '1'") -> orderBy("id");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getDistrict($district) {
		$query = Doctrine_Query::create() -> select("id,number,email,stockout,consumption,coldchain,recepient,valid") -> from("emails") -> where("district = '$district'  and valid = '1'") -> orderBy("id");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getEmails() {
		$query = Doctrine_Query::create() -> select("ID,email,valid") -> from("emails") -> where("national = '1'  and valid = '1'") -> orderBy("email asc");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}
public static function getState() {
		$query = Doctrine_Query::create() -> select("ID,national") -> from("emails") ->  where("valid='1' and national='1'") ;
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}



public static function getNational_Stock_Out() {
		$query = Doctrine_Query::create() -> select("ID,number") -> from("emails") -> where("national = '1' and valid='1' and stockout='1'") -> orderBy("ID asc");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}


public static function getSmslevel() {
		$query = Doctrine_Query::create() -> select("ID,stockout,consumption,coldchain,number") -> from("emails") -> where("valid='1' and national='1'");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}




public static function getProvinceEmails($store) {

		$query = Doctrine_Query::create() -> select("ID,email,valid") -> from("emails") -> where("provincial = '$store'  and valid = '1'") -> orderBy("email asc");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	public static function getDistrictEmails($store) {
		$query = Doctrine_Query::create() -> select("ID,email,valid") -> from("emails") -> where("district ='$store'  and valid = '1'") -> orderBy("ID asc");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	//sets valid to one in the db
	public static function setValidEmails($code) {
		$query = Doctrine_Query::create() -> update('emails') -> set('valid', '1') -> where('id ="' . $code . '"');
		$emails = $query -> execute();
		return $emails;

	}

	//sets valid to zero in db
	public static function setInvalidEmails($code) 
	{
		$query = Doctrine_Query::create() -> update('emails') -> set('valid', '0') -> where('id ="' . $code . '"');
		$emails = $query -> execute();
		return $emails;

	}
	
		
public static function getStateprovincial() {
		$query = Doctrine_Query::create() -> select("ID,provincial") -> from("emails") ->  where("valid='1' and provincial>0 ") ;
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}





	public static function getEmail($id) {
		$query = Doctrine_Query::create() -> select("*") -> from("emails") -> where("id = '$id'  and valid = '1'");
		$emails = $query -> execute();
		return $emails[0];
	}
	
	
	
public static function getSmslevel_provincial() {
		$query = Doctrine_Query::create() -> select("consumption") -> from("emails") -> where("valid='1' and provincial>0");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}

	
public static function getprovincial_number() {
		$query = Doctrine_Query::create() -> select("number") -> from("emails") -> where("valid='1' and provincial>0");
		$emails = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $emails;
	}


}
?>