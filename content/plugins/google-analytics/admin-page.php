<?php

if(isset($_POST['action'])){
	if($_POST['action'] == 'save'){
		if(isset($_POST['measurement-id']) && $_POST['measurement-id'] != ''){
			set_pref('google-analytics-measurement-id', $_POST['measurement-id']);
			show_alert('GA ID Saved!', 'success');
		} else {
			remove_pref('google-analytics-measurement-id');
			show_alert('GA ID Removed!', 'success');
		}
	} else if($_POST['action'] == 'remove'){
		remove_pref('google-analytics-measurement-id');
		show_alert('GA ID Removed!', 'success');
	}
}

$ga_measurement_id = get_pref('google-analytics-measurement-id');

if($ga_measurement_id == ''){
	$ga_measurement_id = null;
}

?>

<div class="section">
	<div class="mb-3">
		<form method="post">
			<input type="hidden" name="action" value="save">
			<div class="mb-3">
				<label class="form-label">Google Analytics Measurement ID:</label>
				<input type="text" class="form-control" autocomplete="off" name="measurement-id" value="<?php echo $ga_measurement_id ? $ga_measurement_id : '' ?>" placeholder="G-T4ZC8MXXXX">
			</div>
			<button class="btn btn-primary btn-md">SAVE</button>
		</form>
	</div>
	<?php
	if($ga_measurement_id){
		?>
		<div class="mb-3">
			<form method="post">
				<input type="hidden" name="action" value="remove">
				<button class="btn btn-danger btn-md">REMOVE</button>
			</form>
		</div>
		<?php
	}
	?>
</div>