<?php

if (isset($_COOKIE['cookieConsent'])) {
    $consent = $_COOKIE['cookieConsent'];
    if ($consent === 'true') {
        // User has accepted cookies
    } elseif ($consent === 'false') {
        // User has declined cookies
    }
    return; // Stop script from here
} else {
    // Cookie consent has not been given yet
    // Show cookie consent banner
}

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

add_to_hook('footer', function(){
	$html = get_pref('cookie-consent-html');
	if($html && $html != ''){
		echo remove_javascript_from_html($html);
	} else {
	?>
	<div id="cookieConsentBanner" style="z-index: 10;">
		<div class="cookie-consent-msg">
			This website uses cookies to ensure you get the best experience on our website.
		</div>
		<div class="cookie-consent-actions">
			<button class="btn btn-primary btn-cookie-accept" id="acceptCookies">Accept</button>
			<button class="btn btn-secondary btn-cookie-decline" id="declineCookies">Decline</button>
		</div>
	</div>
	<?php
	}
});

add_to_hook('footer', function(){
	?>
	<script type="text/javascript">
	var c=getCookie("cookieConsent");null===c&&(document.getElementById("cookieConsentBanner").style.display="flex"),document.getElementById("acceptCookies").addEventListener("click",function(){setCookie("cookieConsent","true",365),document.getElementById("cookieConsentBanner").style.display="none"}),document.getElementById("declineCookies").addEventListener("click",function(){setCookie("cookieConsent","false",365),document.getElementById("cookieConsentBanner").style.display="none"});function setCookie(e,t,n){var o="";n&&(o=new Date,o.setTime(o.getTime()+24*n*60*60*1e3),o="; expires="+o.toUTCString()),document.cookie=e+"="+t+o+"; path=/"}function getCookie(e){for(var t=document.cookie.split(";"),n=0;n<t.length;n++){var o=t[n].split("=");if(e==o[0].trim())return o[1]}return null}
	</script>
	<?php
});

add_to_hook('head_bottom', function(){
	?>
	<style>
		#cookieConsentBanner {
			position: fixed;
			bottom: 0;
			left: 0;
			width: 100%;
			background-color: #333;
			color: white;
			padding: 1em;
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;
			align-items: center;
		}
		.cookie-consent-msg {
			text-align: left;
			flex: 1;
		}
		.cookie-consent-actions {
			text-align: right;
		}
		@media (max-width: 767px) {
			#cookieConsentBanner {
				flex-direction: column;
				align-items: flex-start;
			}
			.cookie-consent-msg, .cookie-consent-actions {
				width: 100%;
				text-align: center;
				margin-bottom: 0.5em;
			}
		}
	</style>
	<?php
});

?>