<?php

if (class_exists('Article') && is_plugin_exist('articles')) {
	//
} else if (class_exists('Post') && is_plugin_exist('posts')) {
	//
} else {
	die('Article or Posts plugin not installed.');
}

require_once( TEMPLATE_PATH . '/functions.php' );

function _get_single_post($slug){
	// Helper function to get single post
	if (class_exists('Article')) {
		return Article::getBySlug( $slug );
	} else if (class_exists('Post')) {
		// Backward compatibility
		return Post::getBySlug( $slug );
	}
	return null;
}

function _get_posts($items_per_page, $cur_page){
	// Helper function to get post list
	if (class_exists('Article')) {
		return Article::getList($items_per_page, 'created_date DESC', $items_per_page*($cur_page-1));
	} else if (class_exists('Post')) {
		// Backward compatibility
		return Post::getList($items_per_page, 'created_date DESC', $items_per_page*($cur_page-1));
	}
	return null;
}

$post = null;

if ( isset($_GET['slug']) ) {
	$_GET['slug'] = htmlspecialchars($_GET['slug']);
	if(strlen($_GET['slug']) >= 2){
		$post = _get_single_post( $_GET['slug'] );
	}
}

// Process pagination parameters once and make them available to the theme
$cur_page = 1;
if(isset($url_params[1])){
	$_GET['page'] = $url_params[1];
	if(!is_numeric($_GET['page'])){
		$_GET['page'] = 1;
	}
}
if(isset($_GET['page'])){
	$cur_page = htmlspecialchars($_GET['page']);
	if(!is_numeric($cur_page)){
		$cur_page = 1;
	}
}

// Get post data once to avoid duplicate queries
$items_per_page = get_setting_value('post_results_per_page');
$data = _get_posts($items_per_page, $cur_page);
$total_posts = $data['totalRows'];
$total_page = $data['totalPages'];
$posts = $data['results'];

// Centralized validation
$is_valid_post_page = count($posts) >= 1;

if($post){
	if(PRETTY_URL){
		if(count($url_params) >= 3){
			// Post page only contains 3 parameter max
			// Show 404 screen
			require( ABSPATH . 'includes/page-404.php' );
			return;
		}
	}
	if($lang_code != 'en'){
		// If use translation (localization)
		// Begin translate the content if has translation
		$translated_fields = get_content_translation('post', $post->id, $lang_code, 'all');
		if(!is_null($translated_fields)){
			$post->title = isset($translated_fields['title']) ? $translated_fields['title'] : $post->title;
			$post->content = isset($translated_fields['content']) ? $translated_fields['content'] : $post->content;
		}
	}

	$page_title = $post->title . ' | '.SITE_TITLE;
	$meta_description = str_replace(array('"', "'"), "", strip_tags($post->content));
	require( TEMPLATE_PATH . '/post.php' );
} else {
	if(file_exists( TEMPLATE_PATH . '/post-list.php' )){
		if(PRETTY_URL){
			if(count($url_params) > 2){
				// Post list page can contains 3 parameter max
				// Show 404 screen
				require( ABSPATH . 'includes/page-404.php' );
				return;
			}
			if(isset($url_params[1]) && !is_numeric($url_params[1])){
				// Page number should be a number
				// Show 404 screen
				require( ABSPATH . 'includes/page-404.php' );
				return;
			}
		}
		if($is_valid_post_page){
			$page_title = _t('Posts') . ' | '.SITE_TITLE;
			$meta_description = _t('Posts') .' | '.SITE_DESCRIPTION;
			// Pass the already fetched data to template
			require( TEMPLATE_PATH . '/post-list.php' );
		} else {
			require( ABSPATH . 'includes/page-404.php' );
		}
	} else {
		require( ABSPATH . 'includes/page-404.php' );
	}
}

?>