<?php

define('GOOGLE_LOGIN', true);

function render_google_login_btn() {
	$g_client_id = get_option('google_client_id');
	if($g_client_id){
	?>
	<br>
	<div id="g_id_onload"
		data-client_id="<?php echo $g_client_id; ?>"
		data-callback="handleCredentialResponse">
	</div>
	<div class="g_id_signin"
		data-type="standard"
		data-size="large"
		data-theme="outline"
		data-text="sign_in_with"
		data-shape="rectangular"
		data-width="350"
		data-logo_alignment="center">
	</div>
	<script>
		function handleCredentialResponse(response) {
			// Send the token to your server
			fetch('<?php echo DOMAIN ?>content/plugins/google-login/action.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({ token: response.credential })
			})
			.then(response => response.json())
			.then(data => {
				if(data['status'] == 'success'){
					location.reload();
				} else {
					alert('Error 2!');
					console.error('Error:', data);
				}
			})
			.catch((error) => {
				alert('Error!');
				console.error('Error:', error);
			});
		}

		window.onload = function () {
			google.accounts.id.initialize({
				client_id: "<?php echo $g_client_id; ?>",
				callback: handleCredentialResponse
			});
			google.accounts.id.renderButton(
				document.querySelector('.g_id_signin'), {
					type: "standard",
					size: "large",
					theme: "outline",
					text: "sign_in_with",
					shape: "rectangular",
					width: "350",
					logo_alignment: "center"
				}
			);
			google.accounts.id.prompt();
		};
	</script>
	<?php
	}
}


?>