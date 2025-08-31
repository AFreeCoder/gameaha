<?php

// Replacement for domain/admin.php
// No login redirect (intentionally)

session_start();

require( "../config.php" );
require( "../init.php" );

if(isset($_GET['action']) && $_GET['action'] == 'logout'){
    CA_Auth::delete();
	unset( $_SESSION['username'] );
	header( "Location: ".DOMAIN );
	return;
} else {
    if(isset( $_SESSION['username'] )){
        header( "Location: dashboard.php" );
        return;
    }
}

die('FORBIDDEN 1010');

?>