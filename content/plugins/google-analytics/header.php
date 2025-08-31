<?php

$ga_measurement_id = get_pref('google-analytics-measurement-id');

if($ga_measurement_id == ''){
	$ga_measurement_id = null;
}

if($ga_measurement_id){
	?>
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $ga_measurement_id ?>"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', '<?php echo $ga_measurement_id ?>');
	</script>
	<?php
}

?>