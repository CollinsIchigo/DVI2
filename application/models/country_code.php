<?php

class Country_code extends Doctrine_Record {

	public function setTableDefinition() {
		$this -> hasColumn('extention', 'int', 11);
		$this -> hasColumn('Country', 'varchar', 50);
		
	}

	public function setUp() {
		$this -> setTableName('country_code');
	}

public  function getcountry() {
		$query = Doctrine_Query::create() -> select("ID,extention,Country") -> from("country_code") -> where("valid = '1'") -> orderBy("ID");
		$country = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $country;
	}


}
?>