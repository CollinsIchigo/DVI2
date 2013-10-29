<div class="quick_menu">
<a class="quick_menu_link" href="<?php echo site_url("vaccine_management");?>">&lt; &lt; Listing</a>
</div>
<script type="text/javascript">
$(document).ready(function() {
	$('#tray_color').ColorPicker({
	onSubmit: function(hsb, hex, rgb, el) {
		$(el).val(hex);
		$(el).ColorPickerHide();
	},
	onBeforeShow: function () {
		$(this).ColorPickerSetColor(this.value);
	}
});
	});
</script>
<?php
$attributes = array('enctype' => 'multipart/form-data');
echo form_open('vaccine_management/save_vaccine',$attributes);
echo validation_errors('
<p class="error">','</p>
'); 
?>

<table border="0" class="data-table">

	<tr>
		<th class="subsection-title" colspan="2">Vaccine Information</th>

	</tr>
	<tbody>
		<tr>
			<td colspan="4"><em>Enter required details below:-</em></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Vaccine Name</td>
			<td><?php

			$data_name = array(
				'name'        => 'name',
			);
			echo form_input($data_name); ?></td>
		</tr>
		
		
		<tr>
			<td><span class="mandatory">*</span> Doses Required</td>
			<td><?php

			$data_doses_required= array(
				 'name'        => 'doses_required'
				 );
				 echo form_input($data_doses_required); ?></td>
		</tr>
		
		
		<tr>
			<td><span class="mandatory">*</span> Wastage Factor</td>
			<td><?php

			$data_wastage_factor= array(
				 'name'        => 'wastage_factor'
				 );
				 echo form_input($data_wastage_factor); ?></td>
		</tr>
		<tr>
			<td><span class="mandatory">*</span> Tray Color</td>
			<td><?php

			$data_tray_color= array(
				 'name'        => 'tray_color', 'id' => 'tray_color'
				 );
				 echo form_input($data_tray_color); ?></td>
		</tr>
		
		
		
		<tr>
			<td> Vaccine Designation</td>
			<td><?php

			$data_designation= array(
				 'name'        => 'designation'
				 );
				 echo form_input($data_designation); ?></td>
		</tr>
		<tr>
			<td> Vaccine Formulation</td>
			<td><?php
			$options_formulation = array(
                  '0'  => 'Lyophilized',
                  '1'    => 'Liquid',
                  '2'   => 'Liquid + Lyophilized',
                  '3' => 'Liquid+Liquid'
                  );
                  echo form_dropdown("formulation",$options_formulation); ?></td>
		</tr>
		<tr>
			<td> Mode of Administration</td>
			<td><?php

			$options_administration = array(
                  '0'  => 'ID',
                  '1'    => 'IM',
                  '2'   => 'SC',
                  '3' => 'Oral',
				  '4' => 'Nasal'
				  );
				  echo form_dropdown("administration",$options_administration); ?></td>
		</tr>
		<tr>
			<td> Vaccine Presentation
			(Doses/Vial)</td>
			<td><?php

			$data_presentation = array(
				 'name'        => 'presentation'
				 );
				 echo form_input($data_presentation); ?></td>
		</tr>
		<tr>
			<td> Vaccine Packed Volume (cm3/dose)</td>
			<td><?php

			$data_vaccine_packed_volume = array(
				 'name'        => 'vaccine_packed_volume'
				 );
				 echo form_input($data_vaccine_packed_volume); ?></td>
		</tr>
		<tr>
			<td> Diluents Packed Volume
			(cm3/dose)</td>
			<td><?php

			$data_diluents_packed_volume = array(
				 'name'        => 'diluents_packed_volume'
				 );
				 echo form_input($data_diluents_packed_volume); ?></td>
		</tr>
		<tr>
			<tr>
				<td>Vaccine Price ($USD/Vial)</td>
				<td><?php

				$data_vaccine_vial_price = array(
				 'name'        => 'vaccine_vial_price'
				 );
				 echo form_input($data_vaccine_vial_price); ?></td>
			</tr>
			<tr>
				<td>Vaccine Price ($USD/Dose)</td>
				<td><?php

				$data_vaccine_dose_price = array(
				 'name'        => 'vaccine_dose_price'
				 );
				 echo form_input($data_vaccine_dose_price); ?></td>
			</tr>
			<tr>
				<td align="center" colspan=2><input name="submit" type="submit"
					class="button" value="Save Vaccine Information"> <input
					name="reset" type="reset" class="button" value="Reset Fields"></td>
			</tr>
	
	</tbody>
</table>
<?php echo form_close();?>