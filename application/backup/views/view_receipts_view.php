<script type="text/javascript">
 $(function() {
		var dates = $( "#from, #to" ).datepicker({
			defaultDate: "+1w",
			changeMonth: true,
			changeYear: true, 
			onSelect: function( selectedDate ) {
				var option = this.id == "from" ? "minDate" : "maxDate",
					instance = $( this ).data( "datepicker" ),
					date = $.datepicker.parseDate(
						instance.settings.dateFormat ||
						$.datepicker._defaults.dateFormat,
						selectedDate, instance.settings );
				dates.not( this ).datepicker( "option", option, date );
			}
		}); 

		$(".supply_graph").click(function(){    
 var supply_div = "supply_graph_div_"+$(this).attr("vaccine");
			   var chart = new FusionCharts("<?php echo base_url()."Scripts/FusionCharts/Charts/MSLine.swf"?>", "ChartId", "800", "450", "0", "0");
			   var url = '<?php echo base_url()."supply_graph/get/".$type."/".$store_id."/"?>' + $(this).attr("vaccine");
			   chart.setDataURL(url);		   
			   chart.render(supply_div); 


		$("#"+supply_div).dialog( {
			height: 500,
			width: 850,
			modal: true
			} );


			});
	});
function cleanup(){
}
</script>
 
<div class="section_title"><?php echo $title;?></div>

<div class="filter">
<fieldset>
<legend>Filter Options</legend>
<?php 
$url = "disbursement_management/drill_down/".$type."/".$id;
echo form_open($url);?>
<label for="from">From</label>
<input type="text" id="from" name="from"/>
<label for="to">to</label>
<input type="text" id="to" name="to"/>
<input type="submit" class="button" name="submit" value="Filter"/>
</form>
</fieldset>

</div>


<?php
$this->load->view('vaccine_tabs');
foreach($vaccines as $vaccine){
?>
<div id="<?php echo $vaccine->id?>">
<table border="0" class="data-table">
	<th class="subsection-title" colspan="10">Aggregated Receipts For <?php echo $vaccine->Name?> in <span style="color:#E01B4C; font-size:14px;"><?php echo $recipient;?></span> 
	</th>
	<tr>
		<th rowspan="2">Date</th>
		<th rowspan="2">Received From</th>
		<th rowspan="2" >Ammount (Doses)</th>
		<th rowspan="2" >Voucher Number</th>
		<th colspan="2">Vaccine Information</th> 
		<th rowspan="2">Entered By</th> 
	</tr>
	<tr>
		 
		<th>Lot/Batch No.</th>
		<th>Expiry Date</th>
	</tr>

	<?php
	$vaccine_totals = 0;
	$vaccine_disbursements = $disbursements[$vaccine->id];
	if(count($vaccine_disbursements) == 0){?>
	<tr><td colspan="7">No Records Exist For This Vaccine in The Specified Time Period</td></tr>
	<?php }
	else{
	foreach($vaccine_disbursements as $disbursement){ 
	if($disbursement->Vaccine_Id == $vaccine->id){ 
 
	?>
	<tr>

		<td><?php echo date("d/m/Y",strtotime($disbursement->Date_Issued));?></td>

		<?php
  
			if($disbursement->Issued_By_National == "0"){ ?>
			<td> National Store </td> 
			<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
			<?php 
			}		
 
		else if($disbursement->Issued_By_Region != null){?>
		<td><?php echo $disbursement->Region_Issued_By->name?> Regional Store</td>
		<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
 
		<?php }
  
		else if($disbursement->Issued_By_District != null){?>
		<td><?php echo $disbursement->District_Issued_By->name?> District Store</td>
		<td style="color: green"><?php $vaccine_totals +=$disbursement->Quantity; echo $disbursement->Quantity?></td>
 
		<?php }?>
 
 		
<td><?php echo $disbursement->Voucher_Number?></td>
		<?php if($disbursement->Batch_Number != null){?>
		<td><?php echo $disbursement->Batch_Number?></td>
		<td><?php echo $disbursement->Batch->Expiry_Date?></td>
		<?php }
		else{?>
		<td>N/A</td>
		<td>N/A</td>
		<?php }
		?>

 
		<td><?php echo $disbursement->User->Full_Name?></td>
 
	</tr>
	
	<?php }
	}?>
	<tr><td>Balance From Previous Period</td><td colspan="8" style="font-weight:bold"><?php echo $current_stocks[$vaccine->id] - $vaccine_totals?></td></tr>
 <?php 
	}
	?>



</table>

<?php
if(count($vaccine_disbursements) >0){
if($disbursement->Issued_To_Facility == null){

?>
<div id="additional">
<table border="0" class="data-table">
<th colspan="2">Derived Information</th>

<tr>
<td>Stock at Hand</td>
<td><?php echo $current_stocks[$vaccine->id];?> Doses</td>
</tr>


<tr>
<td>Population</td>
<td><?php echo $population;?></td>
</tr>

<tr>
<td>Monthly Requirement</td>
<td><?php 
$population = str_replace(",","",$population);
$monthly_requirement =  (($vaccine->Doses_Required*$population*$vaccine->Wastage_Factor)/12);
echo ceil($monthly_requirement);?> Doses</td>
</tr>


<tr>
<td>Stock at Hand Forecast</td>
<td><?php 
$population = str_replace(",","",$population); 
echo floor($current_stocks[$vaccine->id]/$monthly_requirement);?> Month(s)</td>
</tr>

<tr>
<td>Stock Received this Year</td>
<td><?php echo $vaccine_totals?> Doses</td>
</tr>

<tr>
<td>Estimated Coverage</td>
<td><?php 
$yearly_requirement = $population*$vaccine->Doses_Required*$vaccine->Wastage_Factor;
echo ceil(($vaccine_totals/$yearly_requirement)*100)?>%</td>
</tr>


<tr>
<td>Supply Graph</td>
<td><a href="#" class="link supply_graph" vaccine = "<?php echo $vaccine->id;?>" >Click to View Graph</a></td>

</tr>



</table>
<div id="supply_graph_div_<?php echo $vaccine->id;?>" title="Supply Graph for <?php echo $vaccine->Name;?> in <?php echo $recipient;?>" ></div>
</div>
<?php 
}
}
?>
</div>



	<?php
}
?>

 