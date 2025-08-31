<?php

$slug = isset($_GET['slug']) ? $_GET['slug'] : 'menus';

$can_access_menus = $login_user->hasAccess('layout', 'menus');
$can_access_widgets = $login_user->hasAccess('layout', 'widgets');

$tab_list = array(
	'menus' => 'Menus',
	'widgets' => 'Widgets',
);

if(!$can_access_menus){
	unset($tab_list['menus']);
}

if(!$can_access_widgets){
	unset($tab_list['widgets']);
}

if(!$can_access_menus && $can_access_widgets){
	$slug = 'widgets';
}

if($slug == 'menus'){
	if($can_access_menus){
		require_once( 'core/menus.php' );
	}
} elseif($slug == 'widgets'){
	if($can_access_widgets){
		require_once( 'core/widgets.php' );
	}
}

?>