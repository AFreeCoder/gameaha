<?php

if(!has_admin_access()){
	die();
}

$_gamepix_sid = get_pref('gamepix-sid');
if(is_null($_gamepix_sid)){
	$_gamepix_sid = '1';
}

?>

<div class="section">
	<p>Change GamePix games SID to your SID.</p>
	<div class="alert alert-info" role="alert">
		All GamePix game SID will be changed in the player/visitor URL (game iframe URL), but the game URL in the editor or database will remain original.
	</div>
	<div class="alert alert-info" role="alert">
		You no longer need to update the SID for every new game added. Your SID will be implemented for all GamePix games, both old and new.
	</div>
	<div class="alert alert-warning" role="alert">
		GamePix games added using the 'remote add' method will not be affected.
	</div>
	<form id="plugin-gamepix-sid">
		<div class="mb-3">
			<label class="form-label" for="sid">Your SID:</label>
			<input type="text" class="form-control" name="sid" value="<?php echo $_gamepix_sid ?>" maxlength="7" required/>
		</div>
		<button type="submit" class="btn btn-primary btn-md">Update</button>
	</form>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$( "form" ).submit(function( event ) {
			let arr = $( this ).serializeArray();
			if($(this).attr('id') === 'plugin-gamepix-sid'){
				event.preventDefault();
				$.ajax({
					url: "<?php echo DOMAIN ?>content/plugins/gamepix-sid/action.php",
					type: 'POST',
					dataType: 'json',
					data: arr,
					success: function (data) {
						//console.log(data.responseText);
					},
					error: function (data) {
						//console.log(data.responseText);
					},
					complete: function (data) {
						console.log(data.responseText);
						if(data.responseText === 'ok'){
							$('.section').before('<div class="alert alert-success alert-dismissible fade show" role="alert">SID updated<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
						} else if(data.responseText === 'no-games'){
							alert('You don\'t have any Gamepix games!');
						} else {
							alert('Error! Check console log for more info!');
						}
					}
				});
			}
		});
	})
</script>