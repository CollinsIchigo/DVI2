<div class="quick_menu">
<a class="quick_menu_link" href="<?php echo site_url("vaccine_management/new_vaccine");?>">New Vaccine</a>
</div>
<div id="BCG">
<table border="0" class="data-table">
	<th class="subsection-title" colspan="11">Vaccines List</th>
	<tr>
		<th>Name</th>
		<th>Doses Required</th>
		<th>Wastage Factor</th>
		<th>Tray Color</th>
		<th>Added By</th>
		<th>Date Added</th>
		<th>Action</th>
	</tr>
 
	<?php 
	foreach($vaccines as $vaccine){?>
	<tr>
		<td><?php echo $vaccine->Name?></td>
		<td><?php echo $vaccine->Doses_Required?></td>
		<td><?php echo $vaccine->Wastage_Factor?></td>
		<td style="background-color:<?php echo '#'.$vaccine->Tray_Color;?>"></td>
		<td><?php echo $vaccine->User->Full_Name?></td>
		<td><?php echo date("d/m/Y",$vaccine->Timestamp);?></td>
		<td><a href="#" class="link">Edit</a> | <a href="#" class="link">Delete</a> | <a href="#" class="link"> More Details</a></td>
		</tr>
	<?php }
	
	?>
 

</table>
</div>