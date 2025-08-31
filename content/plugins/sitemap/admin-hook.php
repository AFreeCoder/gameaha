<?php

if(!get_setting_value('auto_sitemap')){
    return;
}

function _file_age_2($file){
	if (file_exists($file)) {
		// Get the file modification time
		$file_modification_time = filemtime($file);
		// Get the current time
		$current_time = time();
		// Calculate the file age in seconds
		$file_age_in_seconds = $current_time - $file_modification_time;
		// Convert the file age to minutes
		$file_age_in_minutes = $file_age_in_seconds / 60;
		return $file_age_in_minutes;
	} else {
		echo "The file does not exist.";
		return null;
	}
}

function _generate_sitemap(){
    // Check if sitemap doesn't exist OR is older than 20 seconds
    if(!file_exists(dirname(__FILE__).'/../../../sitemap.xml') || _file_age_2(dirname(__FILE__).'/../../../sitemap.xml') > (20/60)){
        define( '_AUTO_SITEMAP', true );
        global $conn;
        require_once(dirname(__FILE__).'/../../../admin/admin-functions.php');
        require_once(dirname(__FILE__).'/../../../sitemap.php');
    }
}

add_admin_filter('pre_game_insert', function($game) {
    _generate_sitemap();
});

add_admin_filter('after_game_delete', function($game) {
    _generate_sitemap();
});

?>