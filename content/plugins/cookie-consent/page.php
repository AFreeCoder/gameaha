<?php

$default_html ='
<div id="cookieConsentBanner" style="z-index: 10;">
	<div class="cookie-consent-msg">
		This website uses cookies to ensure you get the best experience on our website.
	</div>
	<div class="cookie-consent-actions">
		<button class="btn btn-primary btn-cookie-accept" id="acceptCookies">Accept</button>
		<button class="btn btn-secondary btn-cookie-decline" id="declineCookies">Decline</button>
	</div>
</div>';

function remove_javascript_from_html($html) {
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    
    // Specify encoding when loading the HTML content
    $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
    libxml_clear_errors();
    
    // Find and remove all <script> tags
    $script_tags = $dom->getElementsByTagName('script');
    for ($i = $script_tags->length - 1; $i >= 0; $i--) {
        $script_tag = $script_tags->item($i);
        $script_tag->parentNode->removeChild($script_tag);
    }

    // Extract HTML from DOMDocument and strip the encoding declaration
    $cleanedHtml = $dom->saveHTML();
    return str_replace('<?xml encoding="UTF-8">', '', $cleanedHtml);
}


if(isset($_POST['action'])){
	if($_POST['action'] == 'update_html'){
		if(isset($_POST['html']) && $_POST['html'] != ''){
			set_pref('cookie-consent-html', $_POST['html']);
			show_alert('HTML Template updated!', 'success');
		} else {
			set_pref('cookie-consent-html', '');
			show_alert('HTML Template set to default', 'success');
		}
	} else if($_POST['action'] == 'reset'){
		set_pref('cookie-consent-html', '');
		show_alert('HTML Template set to default', 'success');
	}
}

?>
<div class="section">
	<form method="post" enctype="multipart/form-data" action="dashboard.php?viewpage=plugin&name=cookie-consent">
		<input type="hidden" name="action" value="update_html">
		<label class="form-label"><?php _e('HTML Template') ?></label>
		<textarea name="html" class="form-control" rows="10">
			<?php
			$html = get_pref('cookie-consent-html');
			if($html && $html != ''){
				echo htmlspecialchars(remove_javascript_from_html($html));
			} else {
				echo htmlspecialchars($default_html);
			}
			?>
		</textarea>
		<div class="mb-3"></div>
		<button class="btn btn-primary"><?php _e('Save') ?></button>
	</form>
	<form method="post" enctype="multipart/form-data" action="dashboard.php?viewpage=plugin&name=cookie-consent">
		<input type="hidden" name="action" value="reset">
		<div class="mb-3"></div>
		<button class="btn btn-secondary"><?php _e('Reset') ?></button>
	</form>
	<div class="bs-callout bs-callout-info">
		This plugin only works for themes that implement the 'footer' hook.
	</div>
</div>