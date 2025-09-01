<?php

session_start();

require_once( '../../../config.php' );
require_once( '../../../init.php' );

if(!has_admin_access()){
	die();
}

if(is_login() && USER_ADMIN){
	if(isset($_POST['sid'])){
		$sid = $_POST['sid'];
		// Filter the input
		if (preg_match('/^[0-9A-Za-z]+$/', $sid)) {
			set_pref('gamepix-sid', $sid);
			echo('ok');
		} else {
			// The input is not valid
			echo "Invalid SID. Please use only 0-9, A-Z, and a-z.";
		}
	}
}

?>