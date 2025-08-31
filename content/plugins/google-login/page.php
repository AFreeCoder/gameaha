<?php

if(isset($_POST['client_id'])){
	update_option('google_client_id', esc_string($_POST['client_id']));
	show_alert('Saved!', 'success');
}

$g_client_id = get_option('google_client_id');
if(!$g_client_id){
	$g_client_id = '';
}

?>

<div class="section">
	<form id="plugin-google-login" method="post" action="">
		<div class="form-group">
			<label for="client_id">YOUR_GOOGLE_CLIENT_ID:</label>
			<input type="text" autocomplete="off" id="client_id" name="client_id" placeholder="<?php _e('YOUR_GOOGLE_CLIENT_ID') ?>" class="form-control" value="<?php echo $g_client_id ?>" required>
		</div>
		<button type="submit" class="btn btn-primary btn-md">Save</button>
	</form>
</div>