<?php
if (isset($_GET['status'])) {
	if ($_GET['status'] == 'success') {
		show_alert(isset($_GET['info']) ? $_GET['info'] : 'Game successfully update!', 'success');
	} elseif ($_GET['status'] == 'deleted') {
		show_alert(isset($_GET['info']) ? $_GET['info'] : 'Game removed!', 'danger');
	}
}

$can_edit_game = $login_user->hasAccess('gamelist', 'edit');
$can_delete_game = $login_user->hasAccess('gamelist', 'delete');

if (isset($_GET['slug'])){
	if($_GET['slug'] === 'edit' && $can_edit_game){
		include 'core/gamelist-edit.php';
	}
} else {
	include 'core/gamelist-list.php';
}
?>