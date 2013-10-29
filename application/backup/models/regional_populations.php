<?php
 
class Regional_Populations extends Doctrine_Record{
public function setTableDefinition() {
$this->hasColumn('id', 'varchar',10);
$this->hasColumn('name', 'varchar',100);
$this->hasColumn('population', 'varchar', 20);
$this->hasColumn('year', 'varchar', 5); 
$this->hasColumn('region_id', 'varchar', 5); 
}

public function setUp() {
$this->setTableName('regional_populations');
}
public static function getRegionalPopulation($region,$year){ 
	$query = Doctrine_Query::create()->select("population")->from("regional_populations")->where("region_id = '$region' and year='$year'");
	$population = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
	if(isset($population[0])){
	return $population[0]['population']; 
	}
	else{
		return '0';
	}
}

public static function getNationalPopulation($year){ 
	$query = Doctrine_Query::create()->select("sum(population) as National_Population")->from("regional_populations")->where("year = '$year'"); 
	$population = $query->execute(array(), Doctrine::HYDRATE_ARRAY); 
	if(isset($population[0])){
	return $population[0]['National_Population']; 
	}
	else{
		return '0';
	}
}
	public static function getAllForRegion($region) {
		$query = Doctrine_Query::create() -> select("population,year") -> from("regional_populations") -> where("region_id = '$region'");
		$populations = $query -> execute();
		return $populations;
	}
 

}