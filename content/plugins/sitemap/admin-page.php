<?php

$_auto_sitemap = get_setting_value('auto_sitemap');

if(isset($_POST['action'])){
	if($_POST['action'] == 'remove'){
		if(isset($_POST['xml'])){
			foreach ($_POST['xml'] as $xml_file) {
				if(file_exists('../'.$xml_file)){
					unlink('../'.$xml_file);
					show_alert($xml_file.' removed!', 'success');
				}
			}
		}
	} elseif($_POST['action'] == 'change-auto-sitemap'){
		if(isset($_POST['auto-sitemap'])){
			update_setting('auto_sitemap', true);
			$_auto_sitemap = true;
		} else {
			update_setting('auto_sitemap', false);
			$_auto_sitemap = false;
		}
		show_alert('Setting updated!', 'success');
	}
}

$directory = '../';
$xmlFiles = [];

if (is_dir($directory)) {
	if ($handle = opendir($directory)) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry !== '.' && $entry !== '..' && is_file($directory . '/' . $entry)) {
				if (preg_match('/^sitemap.*\.xml$/i', $entry)) {
					$xmlFiles[] = $entry;
				}
			}
		}
		closedir($handle);
	} else {
		echo "Unable to open directory.";
	}
} else {
	echo "Directory does not exist.";
}

if(!PRETTY_URL){
	show_alert('Pretty URL must be activated!', 'warning');
	return;
}

?>


<div class="section">
	<div class="mb-3">
		<p>Included in sitemap:</p>
		<ul>
			<li>Games</li>
			<li>Categories</li>
			<li>Posts</li>
		</ul>
		<div>
			<button class="btn btn-primary btn-md" onclick="_generateSitemap()">GENERATE SITEMAP</button>
		</div>
	</div>
	<div class="mb-3" id="generated-result" style="display: none;">
		<div class="alert alert-success fade show" role="alert" id="xml-list"></div>
	</div>
	<div class="mb-3">
		<hr>
	</div>
	<form method="post">
		<input type="hidden" name="action" value="change-auto-sitemap">
		<div class="mb-3">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" name="auto-sitemap" value="1" id="id-1" <?php echo $_auto_sitemap ? 'checked' : '' ?>>
				<label class="form-check-label" for="id-1">
					Auto Sitemap
				</label>
			</div>
		</div>
		<button class="btn btn-primary btn-md">SAVE</button>
	</form>
	<?php
	if(!empty($xmlFiles)){
		?>
		<div class="mb-3">
			<form method="post">
				<input type="hidden" name="action" value="remove">
				<div class="mb-3">
					<label class="form-label">Sitemaps to remove:</label>
					<?php
					$index = 0;
					foreach ($xmlFiles as $xml) {
						$index++;
						?>
						<div class="form-check">
							<input class="form-check-input" type="checkbox" name="xml[]" value="<?php echo $xml ?>" id="xml-<?php echo $index ?>">
							<label class="form-check-label" for="xml-<?php echo $index ?>">
								<?php echo $xml ?>
							</label>
						</div>
						<?php
					}
					?>
				</div>
				<button class="btn btn-danger btn-md">REMOVE SITEMAP</button>
			</form>
		</div>
		<?php
	}
	?>
</div>
<script type="text/javascript">
	function _generateSitemap(){
		fetch("../sitemap.php")
			.then(response => {
				if (!response.ok) {
					throw new Error('Network response was not ok ' + response.statusText);
				}
				return response.text();
			})
			.then(data => {
				// Process the data
				if(_isValidJSON(data)){
					let _array = JSON.parse(data);
					document.getElementById("generated-result").style.display = 'block';
					document.getElementById("xml-list").innerHTML = "Generated sitemap: "+_array.join(', ');
				} else {
					alert('Error! Check console log for more info');
					console.log(data);
				}
			})
			.catch(error => {
				alert('Error');
				console.error('There has been a problem with your fetch operation:', error);
			});
	}
	function _isValidJSON(str) {
		try {
			JSON.parse(str);
			return true;
		} catch (e) {
			return false;
		}
	}
</script>