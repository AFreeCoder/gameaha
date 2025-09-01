<?php

if(!has_admin_access()){
	die();
}

if(isset($_POST['action'])){
	if($_POST['action'] == 'save'){
		file_put_contents('../ads.txt', $_POST['content']);
		show_alert('ads.txt saved!', 'success', true);
	} elseif($_POST['action'] == 'remove'){
		if(file_exists('../ads.txt')){
			unlink('../ads.txt');
			show_alert('ads.txt removed!', 'success');
		}
	}
}
$content = '';
if(file_exists('../ads.txt')){
	$content = file_get_contents('../ads.txt');
}
?>
<div class="section">
	<?php if(!file_exists('../ads.txt')){ ?>
		<div class="bs-callout bs-callout-warning">There is no <b>ads.txt</b> on root, click "SAVE" to create one.</div>
	<?php } ?>
	<h4>ads.txt</h4>
	<form action="" method="post">
		<input type="hidden" name="action" value="save">
		<div class="form-group">
			<textarea class="form-control" name="content" rows="10" required><?php echo $content ?></textarea>
		</div>
		<div class="mb-3"></div>
		<button type="submit" class="btn btn-primary btn-md"><?php _e('Save') ?></button>
	</form>
	<form action="" method="post">
		<input type="hidden" name="action" value="remove">
		<div class="mb-3"></div>
		<button type="submit" class="btn btn-danger btn-md"><?php _e('Remove') ?></button>
	</form>
</div>