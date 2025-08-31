<?php

// Functions for Admin Panel

if(!USER_ADMIN){
	if(!$login_user || $login_user->role != 'crew'){
		die('Forbidden - Error 910');
	}
}

define( "SKIP_QUERY_CACHE", true );

const AVAILABLE_PERMISSIONS = [
    'dashboard' => [
        'title' => 'Dashboard'
    ],
    'gamelist' => [
        'title' => 'Game List',
		'children' => [
            'edit' => [
                'title' => 'Edit Game'
            ],
            'delete' => [
                'title' => 'Delete Game'
            ]
        ]
    ],
    'addgame' => [
        'title' => 'Add Game',
        'children' => [
            'upload' => [
                'title' => 'Upload Game'
            ],
            'fetch' => [
                'title' => 'Fetch Games'
            ],
            'remote' => [
                'title' => 'Remote Add'
            ],
            'json' => [
                'title' => 'JSON Importer'
            ]
        ]
    ],
    'categories' => [
        'title' => 'Categories',
		'children' => [
			'add' => [
                'title' => 'Add Category'
            ],
            'edit' => [
                'title' => 'Edit Category'
            ],
            'delete' => [
                'title' => 'Delete Category'
            ]
        ]
    ],
    'collections' => [
        'title' => 'Collections',
		'children' => [
			'add' => [
                'title' => 'Add Collection'
            ],
            'edit' => [
                'title' => 'Edit Collection'
            ],
            'delete' => [
                'title' => 'Delete Collection'
            ]
        ]
    ],
    'pages' => [
        'title' => 'Pages',
		'children' => [
			'add' => [
                'title' => 'Add Page'
            ],
            'edit' => [
                'title' => 'Edit Page'
            ],
            'delete' => [
                'title' => 'Delete Page'
            ]
        ]
    ],
    'themes' => [
        'title' => 'Themes',
		'children' => [
			'add' => [
                'title' => 'Add Theme'
            ],
            'duplicate' => [
                'title' => 'Duplicate Theme'
            ],
            'delete' => [
                'title' => 'Delete Theme'
            ]
        ]
    ],
    'theme-options' => [
        'title' => 'Theme Options'
    ],
    'layout' => [
        'title' => 'Layout',
		'children' => [
			'menus' => [
                'title' => 'Menus'
            ],
            'widgets' => [
                'title' => 'Widgets'
            ]
        ]
	],
    'support' => [
        'title' => 'Support'
    ]
];

$admin_hooks = [];
$admin_filters = [];

// Function to register admin hook
function add_admin_hook($hook_name, $callback, $priority = 10) {
    global $admin_hooks;
    
    if (!isset($admin_hooks[$hook_name])) {
        $admin_hooks[$hook_name] = [];
    }
    
    if (!isset($admin_hooks[$hook_name][$priority])) {
        $admin_hooks[$hook_name][$priority] = [];
    }
    
    $admin_hooks[$hook_name][$priority][] = $callback;
}

// Function to execute admin hooks
function do_admin_hook($hook_name, ...$args) {
    global $admin_hooks;
    
    if (!isset($admin_hooks[$hook_name])) {
        return;
    }
    
    ksort($admin_hooks[$hook_name]); // Sort by priority
    
    foreach ($admin_hooks[$hook_name] as $priority => $callbacks) {
        foreach ($callbacks as $callback) {
            call_user_func_array($callback, $args);
        }
    }
}

function add_admin_filter($tag, $function_to_add, $priority = 10) {
	/*
	example:

	add_admin_filter('pre_game_update', function($game) {
		$game->last_modified = date('Y-m-d H:i:s');
		return $game;
	});
	*/
    global $admin_filters;
    
    if (!isset($admin_filters[$tag])) {
        $admin_filters[$tag] = [];
    }
    
    if (!isset($admin_filters[$tag][$priority])) {
        $admin_filters[$tag][$priority] = [];
    }
    
    $admin_filters[$tag][$priority][] = $function_to_add;
}

function apply_admin_filters($tag, $value) {
    global $admin_filters;

	/*
	example:
	
	apply_admin_filters('pre_game_insert', $this)
	*/
    
    if (!isset($admin_filters[$tag])) {
        return $value;
    }
    
    if (isset($admin_filters[$tag])) {
        // Sort by priority if multiple exist
        ksort($admin_filters[$tag]);
        
        foreach ($admin_filters[$tag] as $functions) {
            foreach ($functions as $function) {
                $value = call_user_func($function, $value);
            }
        }
    }
    
    return $value;
}

function get_setting_group($category){
	// $conn = open_connection();
	// $sql = "SELECT * FROM settings WHERE category = :category";
	// $st = $conn->prepare($sql);
	// $st->bindValue('category', $category, PDO::PARAM_STR);
	// $st->execute();
	// $rows = $st->fetchAll(PDO::FETCH_ASSOC);
	// return $rows;
	$group = [];
	foreach (SETTINGS as $item) {
		if($item['category'] == $category){
			$group[] = $item;
		}
	}
	return $group;
}

function update_setting($name, $value){
	// Migrated, replacing update_settings()
	$this_setting = get_setting($name);
	// Validating data type
	if($this_setting['type'] == 'bool'){
		if($value == 1 || $value == 0){
			//
		} else {
			die('Type not valid');
		}
	} else if($this_setting['type'] == 'number'){
		if(!is_numeric($value)){
			die('Type not valid');
		}
	}
	$conn = open_connection();
	$sql = "UPDATE settings SET value = :value WHERE name = :name LIMIT 1";
	$st = $conn->prepare($sql);
	$st->bindValue(":name", $name, PDO::PARAM_STR);
	$st->bindValue(":value", $value, PDO::PARAM_STR);
	$st->execute();
}

function to_numeric_version($str_version){
	// Used to convert "1.5.0" to int 150
	return (int)str_replace('.', '', $str_version);
}

function curl_request($url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
	$response = curl_exec($ch);
	if (curl_errno($ch)) {
		// If an error occured during the request, print the error
		echo 'Error:' . curl_error($ch);
		return false;
	}
	curl_close($ch);
	return $response;
}

function generate_small_thumbnail($path, $slug){
	// $path == $game->thumb_2
	// This function only works if thumb 2 is already stored locally
	$parent_dir = dirname(__FILE__) . '/../'; // CloudArcade root / installation folder
	if(!file_exists($parent_dir.$path)){
		echo 'error 910: img file not found!';
		return;
	}
	// $use_webp = get_setting_value('webp_thumbnail');
	$path_info = pathinfo(strtok($path, '?'));
	$root_folder = explode("/", $path);
	$output = "thumbs/" . $slug . "_small." . $path_info['extension'];
	if($path_info['extension'] == 'webp'){
		// WEBP thumbnail
		$file_extension = pathinfo(strtok($path, '?'), PATHINFO_EXTENSION);
		$output = str_replace('.'.$file_extension, '.webp', $output);
		$_img = getimagesize($parent_dir.$path);
		$width  = $_img[0];
		$height = $_img[1];
		$img = imagecreatefromwebp($parent_dir.$path);
		$new_img = imagecreatetruecolor(160, 160);
		imagecopyresampled($new_img, $img, 0, 0, 0, 0, 160, 160, $width, $height);
		// Output
		imagewebp($new_img, $parent_dir.$output, 100); // Best quality
		imagedestroy($img);
		imagedestroy($new_img);
	} else {
		// PNG, JPG, GIF
		$x = getimagesize($parent_dir.$path);
		$width  = $x[0];
		$height = $x[1];
		switch ($x['mime']) {
		  case "image/gif":
			 $img = imagecreatefromgif($parent_dir.$path);
			 break;
		  case "image/jpg":
		  case "image/jpeg":
			 $img = imagecreatefromjpeg($parent_dir.$path);
			 break;
		  case "image/png":
			 $img = imagecreatefrompng($parent_dir.$path);
			 break;
		}
		$img_base = imagecreatetruecolor(160, 160);
		if($x['mime'] == "image/png"){
			imagealphablending($img_base, false);
			imagesavealpha($img_base, true);
		}
		imagecopyresampled($img_base, $img, 0, 0, 0, 0, 160, 160, $width, $height);
		$path_info = pathinfo($parent_dir.$path);
		switch ($path_info['extension']) {
		  case "gif":
			 imagegif($img_base, $parent_dir.$output); // No compression
			 break;
		case "jpg":
		case "jpeg":
			 imagejpeg($img_base, $parent_dir.$output, 100); // Best quality
			 break;
		  case "png":
			 imagepng($img_base, $parent_dir.$output, 6); // Balance compression
			 break;
		}
		imagedestroy($img);
		imagedestroy($img_base);
	}
}

function import_thumbnail($url, $game_slug, $index = null){
	// import_thumb() replacement from request.php
	// Used to import thumb_1 and thumb_2 from remote source
	$parent_dir = dirname(__FILE__) . '/../'; // CloudArcade root / installation folder
	if($url) {
		if (!file_exists($parent_dir.'thumbs')) {
			mkdir($parent_dir.'thumbs', 0777, true);
		}
		$extension = pathinfo(strtok($url, '?'), PATHINFO_EXTENSION);
		$identifier = '';
		if(!is_null($index)){
			$identifier = '_'.$index;
		}
		$new = $parent_dir.'thumbs/'.$game_slug.$identifier.'.'.$extension;
		if( get_setting_value('webp_thumbnail') ){
			// Using WEBP format
			$file_extension = pathinfo(strtok($url, '?'), PATHINFO_EXTENSION);
			$new = str_replace('.'.$file_extension, '.webp', $new);
			// Create a cURL resource
			$ch = curl_init();
			// Set cURL options for retrieving the remote image file
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
			// Retrieve the remote image and save it to a local file
			$remoteImage = curl_exec($ch);
			if($remoteImage !== false){
				$localFile = fopen($new, 'w');
				if($localFile){
					fwrite($localFile, $remoteImage);
					fclose($localFile);
				} else {
					echo 'Could not create local file';
				}
			} else {
				echo 'Could not download remote image';
			}
			// Close the cURL resource
			curl_close($ch);
			image_to_webp($new, 100, $new);
		} else {
			// Using JPG/PNG/GIF format
			save_remote_thumbnail($url, $new);
		}
	}
}

function save_remote_thumbnail($source, $destination, $quality = 100) {
	// compressImage() replacement from request.php
	// Create a cURL resource
	$ch = curl_init();
	// Set cURL options for retrieving the remote image file
	curl_setopt($ch, CURLOPT_URL, $source);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
	// Retrieve the remote image
	$remoteImage = curl_exec($ch);
	// Close the cURL resource
	curl_close($ch);
	if ($remoteImage !== false) {
		$image = imagecreatefromstring($remoteImage);
		if ($image !== false) {
			$info = getimagesizefromstring($remoteImage);
			switch ($info['mime']) {
				case 'image/png':
					imagealphablending($image, false);
					imagesavealpha($image, true);
					imagepng($image, $destination, 6); // Compression level from 0 (no compression) to 9
					break;
				case 'image/jpeg':
				case 'image/jpg':
					imagejpeg($image, $destination, $quality); // Quality level from 0 (worst) to 100 (best)
					break;
				case 'image/gif':
					imagegif($image, $destination);
					break;
				default:
					echo 'Unsupported image format: ' . $info['mime'];
					imagedestroy($image);
					return false;
			}
			imagedestroy($image);
		} else {
			echo 'Could not create image resource';
			return false;
		}
	} else {
		echo 'Could not download remote image';
		return false;
	}
	return true;
}

function update_content_translation($content_type, $content_id, $language, $field_data) {
	// Sample usage =
	// Single : update_content_translation('game', 1, 'en', ['title' => 'New Title']);
	// Multiple : update_content_translation('game', 1, 'en', ['title' => 'New Title', 'description' => 'New Description']);
	if (ADMIN_DEMO || !USER_ADMIN) {
		die('ERR 918');
	}
	$conn = open_connection();
	try {
		$conn->beginTransaction();
		foreach ($field_data as $field => $translation) {
			$checkSql = "SELECT COUNT(*) FROM translations WHERE content_type = :content_type AND content_id = :content_id AND language = :language AND field = :field";
			$checkStmt = $conn->prepare($checkSql);
			$checkStmt->bindParam(':content_type', $content_type, PDO::PARAM_STR);
			$checkStmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
			$checkStmt->bindParam(':language', $language, PDO::PARAM_STR);
			$checkStmt->bindParam(':field', $field, PDO::PARAM_STR);
			$checkStmt->execute();
			if ($checkStmt->fetchColumn() > 0) {
				$sql = "UPDATE translations SET translation = :translation WHERE content_type = :content_type AND content_id = :content_id AND language = :language AND field = :field";
			} else {
				$sql = "INSERT INTO translations (content_type, content_id, language, field, translation) VALUES (:content_type, :content_id, :language, :field, :translation)";
			}
			$stmt = $conn->prepare($sql);
			$stmt->bindParam(':content_type', $content_type, PDO::PARAM_STR);
			$stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
			$stmt->bindParam(':language', $language, PDO::PARAM_STR);
			$stmt->bindParam(':field', $field, PDO::PARAM_STR);
			$stmt->bindParam(':translation', $translation, PDO::PARAM_STR);
			$stmt->execute();
		}
		$conn->commit();
		return true;
	} catch (Exception $e) {
		$conn->rollback();
		return false;
	}
}

function delete_content_translation($content_type, $content_id, $language = null, $field = null) {
	if (ADMIN_DEMO || !USER_ADMIN) {
		global $login_user;
		if($login_user && $login_user->role == 'crew'){
			//
		} else {
			die('ERR 237');
		}
	}
	$conn = open_connection();
	$sql = "DELETE FROM translations WHERE content_type = :content_type AND content_id = :content_id";
	if ($language !== null) {
		$sql .= " AND language = :language";
	}
	if ($field !== null) {
		$sql .= " AND field = :field";
	}
	$stmt = $conn->prepare($sql);
	$stmt->bindParam(':content_type', $content_type, PDO::PARAM_STR);
	$stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
	if ($language !== null) {
		$stmt->bindParam(':language', $language, PDO::PARAM_STR);
	}
	if ($field !== null) {
		$stmt->bindParam(':field', $field, PDO::PARAM_STR);
	}
	return $stmt->execute();
}

function get_extra_fields($content_type) {
	$conn = open_connection();
	$sql = "SELECT * FROM extra_fields WHERE content_type = :content_type";
	$st = $conn->prepare($sql);
	$st->bindValue(':content_type', $content_type, PDO::PARAM_STR);
	$st->execute();
	$rows = $st->fetchAll(PDO::FETCH_ASSOC);
	return $rows;
}

function get_extra_field_by_id($id) {
	$conn = open_connection();
	$sql = "SELECT * FROM extra_fields WHERE id = :id LIMIT 1";
	$st = $conn->prepare($sql);
	$st->bindValue(':id', $id, PDO::PARAM_INT);
	$st->execute();
	$row = $st->fetch(PDO::FETCH_ASSOC);
	return $row;
}

function get_extra_field_by_key($field_key, $content_type = null) {
	$allowed_types = ['game', 'category', 'page', 'post'];
	$including_type = false;
	if(!is_null($content_type)){
		if(in_array($content_type, $allowed_types)){
			$including_type = true;
		}
	}
	$conn = open_connection();
	$sql = "SELECT * FROM extra_fields WHERE field_key = :field_key";
	if ($including_type) {
		$sql .= " AND content_type = :content_type";
	}
	$sql .= " LIMIT 1";
	$st = $conn->prepare($sql);
	$st->bindValue(':field_key', $field_key, PDO::PARAM_STR);
	if ($including_type) {
		$st->bindValue(':content_type', $content_type, PDO::PARAM_STR);
	}
	$st->execute();
	$row = $st->fetch(PDO::FETCH_ASSOC);
	return $row;
}

function backup_cms($root_path, $backup_type = 'part'){
	// Backup directory and file name
	if (extension_loaded('zip') && is_login() && USER_ADMIN && !ADMIN_DEMO) {
		$backup_dir = $root_path.'/admin/backups';
		if (!file_exists($backup_dir)) {
			mkdir($backup_dir, 0755, true);
		}
		if (!file_exists($backup_dir.'/index.php')) {
			file_put_contents($backup_dir.'/index.php', '');
		}
		$backup_file = $_SESSION['username'].'-cloudarcade-backup-'.$backup_type.'-'.VERSION.'-'.time().'-'.generate_random_strings().'.zip';
		$allowed_folders = [];
		$allowed_extensions = [];
		if($backup_type == 'part'){
			$allowed_folders = ['admin', 'classes', 'db', 'includes', 'js', 'locales']; // 'images'
			$allowed_extensions = ['php', 'js', 'html', 'xml', 'json', 'css', 'htaccess', 'ico', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];
		}
		$options = [
			'allowed_folders'	=> $allowed_folders, // root
			'ignore_folders'	=> ['cloudarcade', 'private', 'cache', 'temp', 'backups'], // also applied on sub-folder
			'ignore_extensions'	=> ['zip', 'rar', '7z'],
			'whitelisted_files'	=> [],
			'allowed_extensions'	=> $allowed_extensions,
			'ignore_files'		=> []
		];
		if($backup_type == 'part'){
			$options['whitelisted_files'] = ['content/themes/theme-functions.php'];
			$options['ignore_files'] = ['connect.php'];
		}
		zip_files_recursive( $root_path, ABSPATH . 'admin/backups/'.$backup_file, $options );
	}
}

function zip_files_recursive($source, $destination, $options = []) {
    // Extract options with defaults
    $allowedFolders = isset($options['allowed_folders']) ? $options['allowed_folders'] : [];
    $ignoreFolders = isset($options['ignore_folders']) ? $options['ignore_folders'] : [];
    $ignoreExtensions = isset($options['ignore_extensions']) ? $options['ignore_extensions'] : [];
    $whitelistedFiles = isset($options['whitelisted_files']) ? $options['whitelisted_files'] : [];
    $ignoreFiles = isset($options['ignore_files']) ? $options['ignore_files'] : [];
    $allowedExtensions = isset($options['allowed_extensions']) ? $options['allowed_extensions'] : [];
    
    // Set security limits
    $maxFileSize = 50 * 1024 * 1024; // 50 MB per file
    $maxTotalSize = 200 * 1024 * 1024; // 200 MB total archive size
    $maxFileCount = 10000; // Maximum number of files to process
    $currentTotalSize = 0;
    $fileCount = 0;
    
    // Validate environment and authentication
    if (!extension_loaded('zip') || !is_login()) {
        return false;
    }
    
    // Sanitize and validate paths
    $source = rtrim(realpath($source), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $destinationDir = dirname($destination);
    
    // Ensure destination directory exists and is writable
    if (!is_dir($destinationDir) || !is_writable($destinationDir)) {
        return false;
    }
    
    // Validate source exists
    if (!file_exists($source)) {
        return false;
    }
    
    // Create ZIP archive
    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)) {
        return false;
    }
    
    try {
        if (is_dir($source)) {
            $iterators = [];
            
            // Process allowed folders or all folders
            if (!empty($allowedFolders)) {
                // Process allowed folders
                foreach ($allowedFolders as $allowedFolder) {
                    $folderPath = $source . $allowedFolder;
                    $realFolderPath = realpath($folderPath);
                    
                    // Security check: ensure folder is within source path
                    if ($realFolderPath && strpos($realFolderPath, $source) === 0 && is_dir($realFolderPath)) {
                        $iterators[] = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($realFolderPath, RecursiveDirectoryIterator::SKIP_DOTS),
                            RecursiveIteratorIterator::SELF_FIRST
                        );
                    }
                }
                
                // Process root files
                $root_files = scandir($source);
                $_root_files = [];
                foreach ($root_files as $file) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $filePath = $source . $file;
                    if (is_file($filePath)) {
                        $_root_files[] = new SplFileInfo($filePath);
                    }
                }
                $iterators[] = $_root_files;
            } else {
                // Process all files and folders
                $iterators[] = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
            }
            
            // Process whitelisted files (files that should be included regardless of other filters)
            if (!empty($whitelistedFiles)) {
                foreach ($whitelistedFiles as $whitelistedFile) {
                    $fullPath = $source . $whitelistedFile;
                    $realFullPath = realpath($fullPath);
                    
                    // Security check: ensure file is within source path and exists
                    if ($realFullPath && file_exists($realFullPath) && is_file($realFullPath) && strpos($realFullPath, $source) === 0) {
                        // Convert backslashes to forward slashes for consistent handling
                        $relativePath = str_replace('\\', '/', substr($realFullPath, strlen($source)));
                        
                        // Check file size
                        $fileSize = filesize($realFullPath);
                        if ($fileSize <= $maxFileSize) {
                            // Add file to ZIP
                            $zip->addFile($realFullPath, $relativePath);
                            $fileCount++;
                            $currentTotalSize += $fileSize;
                        }
                    }
                }
            }
            
            // Process files
            foreach ($iterators as $files) {
                foreach ($files as $file) {
                    // Check file count limit
                    if ($fileCount >= $maxFileCount) {
                        break 2; // Break out of both loops
                    }
                    
                    $filepath = $file->getPathname();
                    $realFilePath = realpath($filepath);
                    
                    // Security check: ensure file is within source path
                    if (!$realFilePath || strpos($realFilePath, $source) !== 0) {
                        continue;
                    }
                    
                    // Convert backslashes to forward slashes for consistent handling
                    $relativePath = str_replace('\\', '/', substr($realFilePath, strlen($source)));
                    
                    // Security check: prevent directory traversal
                    if (strpos($relativePath, '../') !== false || strpos($relativePath, '..\\') !== false) {
                        continue;
                    }
                    
                    // Check if this is a file we should ignore
                    if (in_array($relativePath, $ignoreFiles)) {
                        continue;
                    }
                    
                    // Process directory
                    if (is_dir($file)) {
                        // Check if directory should be ignored
                        $shouldIgnore = false;
                        foreach ($ignoreFolders as $ignoreFolder) {
                            if (strpos($relativePath, $ignoreFolder) !== false) {
                                $shouldIgnore = true;
                                break;
                            }
                        }
                        
                        if ($shouldIgnore) {
                            continue;
                        }
                        
                        // Add directory to ZIP
                        if (count(glob("$realFilePath/*")) > 0) { // If folder not empty
                            $zip->addEmptyDir($relativePath);
                        }
                    } 
                    // Process file
                    else if (is_file($file)) {
                        // Check if this file is whitelisted (already processed)
                        $relFilePath = str_replace('\\', '/', substr($realFilePath, strlen($source)));
                        if (in_array($relFilePath, $whitelistedFiles)) {
                            continue; // Skip files already processed in the whitelist
                        }
                        
                        // Check extension if allowed extensions are specified
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (!empty($allowedExtensions) && !in_array($ext, $allowedExtensions)) {
                            continue;
                        }
                        
                        // Check if extension should be ignored
                        if (in_array($ext, $ignoreExtensions)) {
                            continue;
                        }
                        
                        // Check if directory should be ignored
                        $shouldIgnore = false;
                        foreach ($ignoreFolders as $ignoreFolder) {
                            if (strpos($relativePath, $ignoreFolder) !== false) {
                                $shouldIgnore = true;
                                break;
                            }
                        }
                        
                        if ($shouldIgnore) {
                            continue;
                        }
                        
                        // Check file size
                        $fileSize = filesize($realFilePath);
                        if ($fileSize > $maxFileSize) {
                            continue;
                        }
                        
                        // Check total archive size limit
                        $currentTotalSize += $fileSize;
                        if ($currentTotalSize > $maxTotalSize) {
                            break 2; // Break out of both loops
                        }
                        
                        // Add file to ZIP (using addFile instead of addFromString to avoid memory issues)
                        $zip->addFile($realFilePath, $relativePath);
                        $fileCount++;
                    }
                }
            }
        } else if (is_file($source)) {
            // Single file handling (optional)
            $realFilePath = realpath($source);
            $filename = basename($realFilePath);
            
            // Add the single file to the zip
            $zip->addFile($realFilePath, $filename);
        }
        
        // Close the ZIP archive
        return $zip->close();
    } catch (Exception $e) {
        // Close the ZIP archive on error
        $zip->close();
        return false;
    }
}

function install_product($email, $purchaseCode, $type = 'plugin') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($purchaseCode)) {
        throw new Exception('Invalid email or purchase code.');
    }

    // Use content directory for temporary file
    $target = ABSPATH . 'content/tmp_product.zip';
    
    try {
        // Verify purchase and get download URL
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://store.cloudarcade.net/api/install/verify?email=" . urlencode($email) . "&code=" . urlencode($purchaseCode),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        if (curl_errno($ch) || curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
            throw new Exception('Verification failed: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $data = json_decode($response, true);
        if (empty($data['download_url'])) throw new Exception('Invalid response');

        if($type != 'theme' && $type != 'plugin'){
            throw new Exception('Selected product type invalid!');
        }

        if($type == 'plugin' && $data['type'] != 'CloudArcade Plugin'){
            throw new Exception('Invalid product type selected!');
        }

        if($type == 'theme' && $data['type'] != 'CloudArcade Theme'){
            throw new Exception('Invalid product type selected!');
        }
        
        // Download file using your working approach
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $data['download_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        
        $remoteFile = curl_exec($ch);
        if($remoteFile === false) {
            throw new Exception('Download failed: ' . curl_error($ch));
        }
        curl_close($ch);

        // Save file using your working approach
        $localFile = fopen($target, 'w');
        if(!$localFile) {
            throw new Exception('Could not create local file');
        }
        
        fwrite($localFile, $remoteFile);
        fclose($localFile);

        if(!file_exists($target)) {
            throw new Exception('Downloaded file not found');
        }
        
        // Extract files
        $zip = new ZipArchive;
        $res = $zip->open($target);
        if ($res !== TRUE) {
            throw new Exception('Invalid ZIP');
        }
        
        $extractPath = ABSPATH . ($type === 'plugin' ? 'content/plugins/' : 'content/themes/');
        if ($type === 'theme') $extractPath .= $data['product_slug'] . '/';
        
        if (!$zip->extractTo($extractPath)) {
            throw new Exception('Extract failed');
        }
        $zip->close();
        
        return [
            'status' => 'success',
            'item_name' => $data['item_name'],
            'version' => $data['version'], 
            'slug' => $data['product_slug']
        ];
    } finally {
        if (file_exists($target)) {
            unlink($target);
        }
    }
}

function log_action($action_type, $object_type, $object_id = null, $object_name = null, $details = null) {
	// Add user action log
	/* Example:
	log_action(
		'action_type',  // 'create', 'update', 'delete'
		'object_type',  // 'game', 'category', 'page', etc.
		$object_id,     // ID of the affected object (optional)
		$object_name,   // Name/title of the affected object (optional)
		$details        // Array of additional details (optional)
	);
	*/
    global $login_user;
    
    if (!$login_user) {
        return false; // Can't log if no user is logged in
    }
    
    $conn = open_connection();
    
    // First check if the action_logs table exists
    try {
        $tableExists = false;
        
        // Check if table exists using information_schema
        $checkSql = "SELECT 1 FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'action_logs'";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn()) {
            $tableExists = true;
        }
        
        // If table doesn't exist, return without error
        if (!$tableExists) {
            return false;
        }
        
        // If we get here, table exists, so proceed with logging
        $sql = "INSERT INTO action_logs (user_id, username, user_role, action_type, object_type, object_id, object_name, details) 
                VALUES (:user_id, :username, :user_role, :action_type, :object_type, :object_id, :object_name, :details)";
        
        $st = $conn->prepare($sql);
        $st->bindValue(":user_id", $login_user->id, PDO::PARAM_INT);
        $st->bindValue(":username", $login_user->username, PDO::PARAM_STR);
        $st->bindValue(":user_role", $login_user->role, PDO::PARAM_STR);
        $st->bindValue(":action_type", $action_type, PDO::PARAM_STR);
        $st->bindValue(":object_type", $object_type, PDO::PARAM_STR);
        $st->bindValue(":object_id", $object_id, PDO::PARAM_INT);
        $st->bindValue(":object_name", $object_name, PDO::PARAM_STR);
        
        // Convert details array to JSON if it's an array
        if (is_array($details)) {
            $details = json_encode($details);
        }
        
        $st->bindValue(":details", $details, PDO::PARAM_STR);
        
        return $st->execute();
    } catch (PDOException $e) {
        // If any database error occurs, just return false
        // This prevents the feature from breaking existing functionality
        return false;
    }
}

?>