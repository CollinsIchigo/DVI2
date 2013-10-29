<?php
class Supply_Graph extends MY_Controller {
function __construct()
{
parent::__construct();

}

public function get($type,$id,$vaccine)
{
$monthly_opening_stocks = array();
$year = date('Y');
$year_start = date("U", mktime(0, 0, 0, 1, 1, date('Y')));
$vaccine_object = Vaccines::getVaccine($vaccine); 

if($type == 0){
//Regional Store
$population = regional_populations::getRegionalPopulation($id,$year);
for($month = 1; $month<=12; $month++){
$to = date("U", mktime(0, 0, 0, $month, 1, date('Y')));
$monthly_opening_stocks[$month] = Disbursements::getRegionalReceiptsTotals($id, $vaccine,$year_start,$to);
}
}
else if($type == 1){
//District Store
$population = district_populations::getDistrictPopulation($id,$year);
for($month = 1; $month<=12; $month++){
$to = date("U", mktime(0, 0, 0, $month, 1, date('Y')));
$monthly_opening_stocks[$month] = Disbursements::getDistrictReceiptsTotals($id, $vaccine,$year_start,$to); 
}
} 
$population = str_replace(",","",$population);
$monthly_requirement =  ceil(($vaccine_object->Doses_Required*$population*$vaccine_object->Wastage_Factor)/12);
$upper_limit = $monthly_requirement*2;
$lower_limit = ceil($monthly_requirement/2); 
$chart =  '
<chart caption="Monthly Stock at Hand Summary" subcaption="For the year '.$year.'" xAxisName="Month" yAxisName="Quantity"  numberSuffix=" doses" showValues="0" alternateHGridColor="FCB541" alternateHGridAlpha="20" divLineColor="FCB541" divLineAlpha="50" canvasBorderColor="666666" baseFontColor="666666" lineColor="FCB541">
<categories>
<category label="Jan"/>
<category label="Feb"/>
<category label="Mar"/>
<category label="Apr"/>
<category label="May"/>
<category label="Jun"/>
<category label="Jul"/>
<category label="Aug"/>
<category label="Sep"/>
<category label="Oct"/>
<category label="Nov"/>
<category label="Dec"/>
</categories> 
<dataset seriesName="Upper Limit" color="269600" anchorBorderColor="269600" anchorBgColor="269600">';

for($x=1;$x<=12;$x++){
$cumulative_value = $x * $upper_limit;
$chart .= '<set value="'.$cumulative_value.'"/>';
}

$chart .= '</dataset>

<dataset seriesName="Receipts" color="0008FF" anchorBorderColor="0008FF" anchorBgColor="0008FF">
<set  value="'.$monthly_opening_stocks[1].'"/>
<set  value="'.$monthly_opening_stocks[2].'"/>
<set  value="'.$monthly_opening_stocks[3].'"/>
<set value="'.$monthly_opening_stocks[4].'"/>
<set  value="'.$monthly_opening_stocks[5].'"/>
<set value="'.$monthly_opening_stocks[6].'"/>
<set  value="'.$monthly_opening_stocks[7].'"/>
<set  value="'.$monthly_opening_stocks[8].'"/>
<set  value="'.$monthly_opening_stocks[9].'"/>
<set  value="'.$monthly_opening_stocks[10].'"/>
<set  value="'.$monthly_opening_stocks[11].'"/>
<set value="'.$monthly_opening_stocks[12].'"/>
</dataset>
<dataset seriesName="Lower Limit" color="FF0000" anchorBorderColor="FF0000" anchorBgColor="FF0000">';

for($x=1;$x<=12;$x++){
$cumulative_value = $x * $lower_limit;
$chart .= '<set value="'.$cumulative_value.'"/>';
}

$chart .= '</dataset>
<styles>
<definition>
<style name="Anim1" type="animation" param="_xscale" start="0" duration="1"/>
<style name="Anim2" type="animation" param="_alpha" start="0" duration="0.6"/>
<style name="DataShadow" type="Shadow" alpha="40"/>
</definition>
<application>
<apply toObject="DIVLINES" styles="Anim1"/>
<apply toObject="HGRID" styles="Anim2"/>
<apply toObject="DATALABELS" styles="DataShadow,Anim2"/>
</application>
</styles>
</chart>
';

echo $chart;
}
}