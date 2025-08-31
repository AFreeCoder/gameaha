<?php

require('../../config.php');
require('../../init.php');
require('../admin-functions.php');

if (isset($_POST['action'])) {
	$action = $_POST['action'];
	if ($action == 'upload_image') {
		// Fix post / get issue on page.php Gallery plugin
		$_GET['action'] = $action;
	}
	$super_user = false;
	if (has_admin_access()) {
		$super_user = true;
	}

	if ($action == 'save_widgets_position') {
		$data = $_POST['data'];
		$has_access = $login_user->hasAccess('layout', 'widgets');
		if ($super_user || $has_access) {
			update_option('widgets', json_encode($data));
			echo 'ok';
		}
	} elseif ($action == 'update_widget') {
		$data = $_POST['data'];
		$has_access = $login_user->hasAccess('layout', 'widgets');
		if ($super_user || $has_access) {
			$widget_data = get_pref('widgets') ?: "[]";
			$stored_widgets = json_decode($widget_data, true);

			foreach ($stored_widgets as $key => $item) {
				if ($key == $_POST['parent']) {
					$stored_widgets[$key][(int)$_POST['index']] = $data;
					break;
				}
			}

			update_option('widgets', json_encode($stored_widgets));
			echo 'ok';
		}
	} elseif ($action == 'delete_widget') {
		$has_access = $login_user->hasAccess('layout', 'widgets');
		if ($super_user || $has_access) {
			$widget_data = get_pref('widgets') ?: "[]";
			$stored_widgets = json_decode($widget_data, true);

			foreach ($stored_widgets as $key => $item) {
				if ($key == $_POST['parent']) {
					unset($stored_widgets[$key][(int)$_POST['index']]);
					if (count($stored_widgets[$key])) {
						$stored_widgets[$key] = array_values($stored_widgets[$key]);
					}
					break;
				}
			}

			update_option('widgets', json_encode($stored_widgets));
			echo 'ok';
		}
	} elseif ($action == 'check_theme_updates') {
		if ($super_user) {
			function set_cd()
			{
				$conn = open_connection();
				$st = $conn->prepare('UPDATE settings SET value = "" WHERE name = "purchase_code"');
				$st->execute();
			}
			$themes = [];
			$dirs = scan_folder('content/themes/');
			foreach ($dirs as $dir) {
				$json_path = ABSPATH . 'content/themes/' . $dir . '/info.json';
				if (file_exists($json_path)) {
					$theme = json_decode(file_get_contents($json_path), true);
					$themes[$dir] = array(
						'name' => $theme['name'],
						'version' => $theme['version']
					);
				}
			}
			$update_availabe = get_pref('updates');
			if (is_null($update_availabe)) {
				$update_availabe = [];
			} else {
				$update_availabe = json_decode($update_availabe, true);
			}
			$url = 'https://api.cloudarcade.net/themes/fetch.php?action=check&code=' . check_purchase_code();
			$url .= '&data=' . urlencode(json_encode($themes));
			$url .= '&ref=' . DOMAIN . '&v=' . VERSION;
			$curl = curl_request($url);
			if ($curl != '') {
				if ($curl == 'bl') {
					set_cd();
				} else if ($curl == 'invalid') {
					set_cd();
				} else {
					$update_list = json_decode($curl, true);
					if (count($update_list)) {
						if (!isset($update_availabe['themes'])) {
							$update_availabe['themes'] = [];
						}
						if (json_encode($update_list) != json_encode($update_availabe['themes'])) {
							$update_availabe['themes'] = $update_list;
							update_option('updates', json_encode($update_availabe));
						}
					}
				}
				echo 'ok';
			} else {
				if ($curl == 'bl') {
					set_cd();
				} else {
					if (!is_null($update_availabe) && count($update_availabe)) {
						if (isset($update_availabe['themes'])) {
							unset($update_availabe['themes']);
							update_option('updates', json_encode($update_availabe));
						}
					}
				}
				echo 'ok';
			}
		}
	} elseif ($action == 'check_cms_update') {
		if ($super_user) {
			function set_cd() {
				$conn = open_connection();
				$st = $conn->prepare('UPDATE settings SET value = "" WHERE name = "purchase_code"');
				$st->execute();
			}
			
			$update_available = get_pref('updates');
			if (is_null($update_available)) {
				$update_available = [];
			} else {
				$update_available = json_decode($update_available, true);
			}
			
			$url = 'https://api.cloudarcade.net/cms-update/info.php?action=check&code=' . check_purchase_code();
			$url .= '&ref=' . DOMAIN . '&current_version=' . VERSION;
			
			if (isset($_POST['beta'])) {
				$url .= '&test';
			}
			
			$curl = curl_request($url);
			
			// Validate JSON response
			$response = json_decode($curl, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				$response = [
					'status' => 'error',
					'message' => $curl,
					'error_code' => json_last_error()
				];
			} else {
				if (isset($response['status'])) {
					if ($response['status'] == 'update') {
						// Save update information to preferences
						set_pref('cms_update_available', true);
						//
						$response = [
							'status' => 'success',
							'message' => 'Update available!',
							'update_available' => true,
							'latest' => $response['latest']
						];
					} elseif ($response['status'] == 'current') {
						set_pref('cms_update_available', false);
						$response = [
							'status' => 'success',
							'message' => 'Up to date!'
						];
					} elseif ($response['status'] == 'error') {
						$response = [
							'status' => 'error',
							'message' => $response['message']
						];
					}
				} else {
					$response = [
						'status' => 'error',
						'message' => 'Invalid response structure'
					];
				}
			}
			
			// Ensure output is valid JSON
			$json_response = json_encode($response);
			if ($json_response === false) {
				$json_response = json_encode([
					'status' => 'error',
					'message' => 'JSON encoding error',
					'error_code' => json_last_error()
				]);
			}
			
			echo $json_response;
		}
	} elseif ($action == 'update_alert') {
		if ($super_user) {
			$update_availabe = get_pref('updates');

			if (is_null($update_availabe)) {
				$update_availabe = [];
			} else {
				$update_availabe = json_decode($update_availabe, true);
			}

			$update_availabe[$_POST['type']] = true;

			update_option('updates', json_encode($update_availabe));
			echo 'ok';
		}
	} elseif ($action == 'unset_update_alert') {
		if ($super_user) {
			$update_availabe = get_pref('updates');

			if (is_null($update_availabe)) {
				$update_availabe = [];
			} else {
				$update_availabe = json_decode($update_availabe, true);
			}

			if (isset($update_availabe[$_POST['type']])) {
				unset($update_availabe[$_POST['type']]);
				update_option('updates', json_encode($update_availabe));
			}
			echo 'ok';
		}
	} elseif ($action == 'get_plugin_list') {
		//Used for plugin updates
		if ($super_user) {
			require_once('../../includes/plugin.php');
			if (count($plugin_list)) {
				$list = [];
				foreach ($plugin_list as $plugin) {
					if ($plugin['author'] == 'RedFoc' || $plugin['author'] == 'CloudArcade') {
						array_push($list, array(
							'dir_name' => $plugin['dir_name'],
							'version' => $plugin['version']
						));
					}
				}
				$result = array(
					'plugins' => json_encode($list),
					'code' => check_purchase_code(),
					'version' => VERSION,
					'domain' => DOMAIN
				);
				echo json_encode($result);
			}
		}
	} elseif ($action == 'get_plugin_updates_data') {
		// Only super admin can check for updates
		if ($super_user) {
			require_once('../../includes/plugin.php');

			// Prepare the data structure
			$data = array(
				'plugins' => array(),
				// Site verification data needed by CloudArcade API
				'domain' => DOMAIN,           // Current site domain for license check
				'version' => VERSION,         // CMS version for compatibility check
				'code' => check_purchase_code() // License verification
			);

			// Process installed plugins
			if (count($plugin_list)) {
				foreach ($plugin_list as $plugin) {
					// Only include official plugins from RedFoc or CloudArcade
					if (($plugin['author'] == 'RedFoc' || $plugin['author'] == 'CloudArcade') && substr($plugin['dir_name'], 0, 1) !== '_') {
						// Add minimal required plugin data
						$data['plugins'][] = array(
							'dir_name' => $plugin['dir_name'], // Plugin identifier
							'version' => $plugin['version']    // Current version for update check
						);
					}
				}
			}

			$data['plugins'] = json_encode($data['plugins']);

			// Return JSON response
			echo json_encode($data);
		}
	} elseif ($action == 'set_plugin_updates_notification') {
		if ($super_user) {
			if (isset($_POST['plugin_update_list'])) {
				$_plugin_list = json_decode($_POST['plugin_update_list'], true);
				$_plugin_dir_list = [];
				foreach ($_plugin_list as $item) {
					$_plugin_dir_list[] = $item['dir_name'];
				}
				set_pref('available_plugin_updates', json_encode($_plugin_dir_list));
			} else {
				// No plugin updates list
				remove_pref('available_plugin_updates');
			}
			echo 'ok';
		}
	} elseif ($action == 'get_plugin_repo_list') {
		//Used for plugin updates
		if ($super_user) {
			require_once('../../includes/plugin.php');
			if (true) {
				$list = [];
				foreach ($plugin_list as $plugin) {
					if ($plugin['author'] == 'RedFoc' || $plugin['author'] == 'CloudArcade') {
						array_push($list, array(
							'dir_name' => $plugin['dir_name'],
							'version' => $plugin['version']
						));
					}
				}
				$result = array(
					'plugins' => json_encode($list),
					'code' => check_purchase_code(),
					'version' => VERSION,
					'domain' => DOMAIN
				);
				$url = 'https://api.cloudarcade.net/plugin-repo/fetch2.php?ref=' . DOMAIN . '&code=' . check_purchase_code() . '&v=' . VERSION;
				$curl = curl_request($url);
				if ($curl != '') {
					$json = json_decode($curl, true);
					if (isset($json['status']) && $json['status'] == 'failed') {
						show_alert($json['info'], 'danger', false);
						exit();
					}
					if (!$json) {
						echo $curl;
						exit();
					}
					try {
						$filtered_plugin = []; // Plugin list that aren't installed
						foreach ($json as $plugin) {
							if (!is_plugin_exist($plugin['dir_name'])) {
								$filtered_plugin[] = $plugin;
							}
						}
						?>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>#</th>
										<th>Plugin</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$index = 0;
									foreach ($filtered_plugin as $_plugin) {
										$index++;
										if ($_plugin) { ?>
											<tr>
												<th scope="row"><?php echo $index ?></th>
												<td>
													<strong class="plugin-repo-name"><?php echo $_plugin['name'] ?></strong>
													<p><?php echo $_plugin['description'] ?></p>
													Version: <?php echo $_plugin['version'] ?><br>
													Last update: <?php echo $_plugin['last_update'] ?><br>
													Require CA version: <?php echo $_plugin['require_version'] ?><br>
													Tested CA version: <?php echo $_plugin['tested_version'] ?><br>
													Author: <a href="<?php echo $_plugin['website'] ?>" target="_blank"><?php echo $_plugin['author'] ?><br>
												</td>
												<td>
													<a href="#" class="add-plugin-repo" data-reqversion="<?php echo $_plugin['require_version'] ?>" data-url="<?php echo $_plugin['url'] ?>">
														<i aria-hidden="true" class="fa fa-plus circle"></i>
													</a>
												</td>
											</tr>
									<?php }
									}
									?>
								</tbody>
							</table>
						</div>
					<?php } catch (Throwable $e) {
						show_alert('An error occured while parsing plugin data', 'danger', false);
					}
				}
			}
		}
	} elseif ($action == 'update_plugin') {
		if ($super_user) {
			$status = '';
			$message = '';
			$target = ABSPATH . 'content/plugins/tmp_plugin.zip';

			// Initialize cURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $_POST['path'] . '.zip');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

			// Download file
			$remoteFile = curl_exec($ch);
			curl_close($ch);

			if ($remoteFile !== false) {
				file_put_contents($target, $remoteFile);

				if (file_exists($target)) {
					$zip = new ZipArchive;
					$res = $zip->open($target);
					if ($res === TRUE) {
						$zip->extractTo(ABSPATH . 'content/plugins/');
						$zip->close();
						$status = 'success';
						$message = 'Plugin updated!';
						//
						$_available_plugin_updates = get_pref('available_plugin_updates');
						if (!is_null($_available_plugin_updates)) {
							$_available_plugin_updates = json_decode($_available_plugin_updates, true);
							$index = array_search($_POST['id'], $_available_plugin_updates);
							if ($index !== false) {
								unset($_available_plugin_updates[$index]);
								$_available_plugin_updates = array_values($_available_plugin_updates);
								if (count($_available_plugin_updates)) {
									set_pref('available_plugin_updates', json_encode($_available_plugin_updates));
								} else {
									remove_pref('available_plugin_updates');
								}
							}
						}
					} else {
						echo 'doh!';
					}
					unlink($target);
					echo 'ok';
				} else {
					$status = 'error';
					$message = 'Target zip plugin not found!';
				}
			} else {
				$status = 'error';
				$message = 'Plugin download failed!';
			}
			//
			$_SESSION['message'] = [
				'type' => $status,
				'text' => $message
			];
		}
	} elseif ($action == 'update_premium_plugin') {
		if ($super_user) {
			$status = 'error';
			$message = 'Error 5656';
			$plugin_slug = $_POST['id'];

			$purchase_email = get_plugin_pref($plugin_slug, 'purchase_email');
			$purchase_code = get_plugin_pref($plugin_slug, 'purchase_code');

			$result = install_product($purchase_email, $purchase_code, 'plugin');

			if (isset($result['status']) && $result['status'] == 'success') {
				$status = 'success';
				$message = 'Plugin updated!';
				//
				$_available_premium_plugin_updates = get_pref('available_premium_plugin_updates');
				if (!is_null($_available_premium_plugin_updates)) {
					$_available_premium_plugin_updates = json_decode($_available_premium_plugin_updates, true);
					$index = array_search($plugin_slug, $_available_premium_plugin_updates);
					if ($index !== false) {
						unset($_available_premium_plugin_updates[$index]);
						$_available_premium_plugin_updates = array_values($_available_premium_plugin_updates);
						if (count($_available_premium_plugin_updates)) {
							set_pref('available_premium_plugin_updates', json_encode($_available_premium_plugin_updates));
						} else {
							remove_pref('available_premium_plugin_updates');
						}
					}
				}
				//
				echo 'ok';
			}
			//
			$_SESSION['message'] = [
				'type' => $status,
				'text' => $message
			];
		}
	} elseif ($action == 'get_quote') {
		$url = 'https://api.cloudarcade.net/get_quote.php?ref=' . DOMAIN . '&code=' . check_purchase_code() . '&v=' . VERSION;
		$curl = curl_request($url);
		echo $curl;
	} elseif ($action == 'delete_image') {
		if ($super_user && isset($_POST['name'])) {
			if (file_exists('../../files/images/' . $_POST['name'])) {
				unlink('../../files/images/' . $_POST['name']);
				if (!file_exists('../../files/images/' . $_POST['name'])) {
					echo 'ok';
				} else {
					echo 'Failed to delete';
				}
			} else {
				echo 'File not exist';
			}
		}
	} elseif ($action == 'generate_token_wp') {
		if (isset($_POST['pass'])) {
			$_data = DB_DSN . ";usr=" . DB_USERNAME . ";pw=" . DB_PASSWORD;
			$output_str = str_replace(
				['mysql:host=', ';dbname=', ';usr=', ';pw='],
				['h::', 'db::', 'u::', 'p::'],
				$_data
			);
			$encrypted = bin2hex($output_str . $_POST['pass']);
			$url = 'https://api.cloudarcade.net/ca_wp_token_act.php?&action=generate&data=' . $encrypted . '&p=' . $_POST['pass'] . '&code=' . check_purchase_code() . '&v=' . VERSION;
			$curl = curl_request($url);
			echo $curl;
		}

		//$url = 'https://api.cloudarcade.net/get_quote.php?ref='.DOMAIN.'&code='.check_purchase_code().'&v='.VERSION."&data=";
		//$curl = curl_request($url);
		//echo $curl;
	} elseif ($action == 'change_admin_theme') {
		if ($super_user) {
			if (isset($_POST['admin_theme'])) {
				if ($_POST['admin_theme'] == 'theme-dark') {
					$_SESSION['admin_theme'] = 'theme-dark';
				} else {
					$_SESSION['admin_theme'] = 'theme-light';
				}
			}
		}
	} elseif ($action == 'fetch_games_by_type') {
		if ($super_user) {
			$amount = 15;
			if ($_POST['sort'] == 'most_played') {
				echo json_encode(get_game_list('popular', $amount));
			} else if ($_POST['sort'] == 'most_liked') {
				echo json_encode(get_game_list('likes', $amount));
			} else if ($_POST['sort'] == 'trending') {
				echo json_encode(get_game_list('trending', $amount));
			}
		}
	} elseif ($action == 'submit_support_request') {
		// Check admin access
		if (!has_admin_access()) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Access denied',
				'value' => 'access_denied'
			]);
			exit;
		}

		// Verify CSRF token
		if (!verify_csrf_token()) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Security validation failed',
				'value' => 'invalid_token'
			]);
			exit;
		}

		if (isset($_POST['requestType']) && ($_POST['requestType'] == 'technical' || $_POST['requestType'] == 'bug')) {
			$_POST['serverInfo'] = 'PHP ' . PHP_VERSION;
		}

		// Encrypt sensitive data if provided
		if (isset($_POST['includeCredentials']) && $_POST['includeCredentials'] === 'on') {
			if (!empty($_POST['siteUsername']) && !empty($_POST['sitePassword'])) {
				// Create a unique encryption key based on domain and purchase code
				$encryption_key = hash('sha256', DOMAIN . check_purchase_code());

				// Encrypt credentials
				$_POST['siteUsername'] = openssl_encrypt(
					$_POST['siteUsername'],
					'AES-256-CBC',
					substr($encryption_key, 0, 32),
					0,
					substr($encryption_key, 0, 16)
				);
				$_POST['sitePassword'] = openssl_encrypt(
					$_POST['sitePassword'],
					'AES-256-CBC',
					substr($encryption_key, 0, 32),
					0,
					substr($encryption_key, 0, 16)
				);

				// Add flag to indicate encryption
				$_POST['credentials_encrypted'] = true;
			}
		}

		// Encrypt FTP information if provided
		if (isset($_POST['includeFTP']) && $_POST['includeFTP'] === 'on') {
			if (!empty($_POST['ftpHost']) && !empty($_POST['ftpUsername']) && !empty($_POST['ftpPassword'])) {
				// Create a unique encryption key based on domain and purchase code
				$encryption_key = hash('sha256', DOMAIN . check_purchase_code());

				// Encrypt FTP details
				$_POST['ftpHost'] = openssl_encrypt(
					$_POST['ftpHost'],
					'AES-256-CBC',
					substr($encryption_key, 0, 32),
					0,
					substr($encryption_key, 0, 16)
				);
				$_POST['ftpUsername'] = openssl_encrypt(
					$_POST['ftpUsername'],
					'AES-256-CBC',
					substr($encryption_key, 0, 32),
					0,
					substr($encryption_key, 0, 16)
				);
				$_POST['ftpPassword'] = openssl_encrypt(
					$_POST['ftpPassword'],
					'AES-256-CBC',
					substr($encryption_key, 0, 32),
					0,
					substr($encryption_key, 0, 16)
				);

				// Add flag to indicate encryption
				$_POST['ftp_encrypted'] = true;
			}
		}

		// Forward request to API server
		$api_url = 'https://api.cloudarcade.net/support/submit.php';

		// Add additional data
		$_POST['code'] = check_purchase_code();
		$_POST['domain'] = DOMAIN;
		$_POST['version'] = VERSION;

		// Initialize cURL
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $api_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $_POST,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 30
		]);

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_error = curl_error($ch);
		curl_close($ch);

		if ($response === false) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Connection to support server failed: ' . $curl_error,
				'value' => 'connection_failed'
			]);
			exit;
		}

		// Forward the API response back to client
		echo $response;
		exit;
	} elseif ($action == 'get_premium_product_updates') {
		// Only super admin can check for updates
		if ($super_user) {
			require_once('../../includes/plugin.php');
			// Prepare the data structure
			$data = array(
				'licensed_products' => array(),
				'domain' => DOMAIN,           // Current site domain for license check
				'cms_version' => VERSION,         // CMS version for compatibility check
				'cms_purchase_code' => check_purchase_code() // License verification
			);
			// Process installed plugins
			if (count($plugin_list)) {
				foreach ($plugin_list as $plugin) {
					if (substr($plugin['dir_name'], 0, 1) !== '_') {
						if (isset($plugin['is_premium']) && $plugin['is_premium']) {
							$data['licensed_products'][] = array(
								'type' => 'CloudArcade Plugin',
								'slug' => $plugin['dir_name'], // Plugin identifier
								'purchase_code' => get_plugin_pref($plugin['dir_name'], 'purchase_code'),
								'purchase_email' => get_plugin_pref($plugin['dir_name'], 'purchase_email'),
								'current_version' => $plugin['version']    // Current version for update check
							);
						}
					}
				}
			}
			// Process installed themes
			$themes = [];
			$dirs = scan_folder('content/themes/');
			foreach ($dirs as $dir) {
				$json_path = ABSPATH . 'content/themes/' . $dir . '/info.json';
				if (file_exists($json_path)) {
					$theme = json_decode(file_get_contents($json_path), true);
					if (isset($theme['is_premium']) && $theme['is_premium']) {
						$data['licensed_products'][] = array(
							'type' => 'CloudArcade Theme',
							'slug' => $dir,
							'purchase_code' => get_theme_pref($dir, 'purchase_code'),
							'purchase_email' => get_theme_pref($dir, 'purchase_email'),
							'current_version' => $theme['version']    // Current version for update check
						);
					}
				}
			}

			if (count($data['licensed_products']) === 0) {
				return;
			}

			// Initialize cURL session
			$ch = curl_init('https://store.cloudarcade.net/api/updates/check');

			// Set cURL options
			curl_setopt_array($ch, array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'Accept: application/json'
				),
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => json_encode($data),
				CURLOPT_SSL_VERIFYPEER => true,  // Enable SSL verification
				CURLOPT_TIMEOUT => 30,  // Timeout in seconds
			));

			// Execute cURL request
			$response = curl_exec($ch);
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$curl_error = curl_error($ch);

			// Close cURL session
			curl_close($ch);

			// Handle the response
			if ($http_code == 200 && !$curl_error) {
				// Decode and validate response
				$updates = json_decode($response, true);
				if (json_last_error() === JSON_ERROR_NONE) {
					// Return the updates data
					if (true) {
						$json_result = json_decode($response, true);
						if ($json_result['status'] == 'success') {
							if (count($json_result['updates'])) {
								$list_plugins = [];
								$list_themes = [];
								foreach ($json_result['updates'] as $product) {
									if ($product['type'] == 'CloudArcade Plugin') {
										$list_plugins[] = $product['slug'];
									} else if ($product['type'] == 'CloudArcade Theme') {
										$list_themes[] = $product['slug'];
									}
								}
								if (count($list_plugins)) {
									set_pref('available_premium_plugin_updates', json_encode($list_plugins));
								} else {
									remove_pref('available_premium_plugin_updates');
								}
								//
								if (count($list_themes)) {
									set_pref('available_premium_theme_updates', json_encode($list_themes));
								} else {
									remove_pref('available_premium_theme_updates');
								}
							} else {
								remove_pref('available_premium_plugin_updates');
								remove_pref('available_premium_theme_updates');
							}
						}
					}
				} else {
					// JSON decode error
					echo json_encode(array(
						'status' => 'error',
						'message' => 'Invalid response format'
					));
				}
			} else {
				// Connection or server error
				echo json_encode(array(
					'status' => 'error',
					'message' => 'Failed to check updates: ' . ($curl_error ?: 'HTTP ' . $http_code)
				));
			}
		}
	} elseif ($action == 'system_update') {
		// Only super admin can perform updates
		if (!$super_user) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Access denied',
				'stage' => 'auth'
			]);
			exit;
		}

		if(isset($_POST['is_test']) && $_POST['is_test'] == 'true'){
			$_GET['test_update'] = true;
		}
	
		require_once('../../classes/SystemUpdater.php');
	
		try {
			// Initialize the updater
			$updater = new SystemUpdater();
	
			// Perform the update
			$result = $updater->performUpdate();
	
			if ($result['status'] === 'success') {
				// Store success message in session for the admin UI
				$_SESSION['message'] = [
					'type' => 'success',
					'text' => $result['message']
				];
	
				// Return success response
				echo json_encode([
					'status' => 'updated',
					'message' => $result['message'],
					'version' => VERSION
				]);
			} else {
				// Store error in session
				$_SESSION['message'] = [
					'type' => 'error',
					'text' => $result['message']
				];
	
				// Return error response
				echo json_encode([
					'status' => 'error',
					'message' => $result['message'],
					'info' => $result['message'] // For backwards compatibility
				]);
			}
		} catch (Exception $e) {
			// Handle any unexpected errors
			$_SESSION['message'] = [
				'type' => 'error',
				'text' => 'Update failed: ' . $e->getMessage()
			];
	
			echo json_encode([
				'status' => 'error',
				'message' => $e->getMessage(),
				'info' => $e->getMessage()
			]);
		}
	}
}
if (isset($_GET['action'])) {

	$action = $_GET['action'];

	$super_user = false;
	if ($login_user && USER_ADMIN && !ADMIN_DEMO) {
		$super_user = true;
	}
	if ($action == 'upload_image') {
		if ($super_user) {
			$target_dir = '../../files/images/';
			// Ensure directories exist
			if (!file_exists('../../files')) {
				mkdir('../../files', 0755, true);
			}
			if (!file_exists($target_dir)) {
				mkdir($target_dir, 0755, true);
			}
			if (file_exists($target_dir)) {
				// Prepare array to hold uploaded files
				$files_to_upload = [];
				if (isset($_FILES['file-0'])) {
					$files_to_upload[] = $_FILES['file-0'];
				}
				if (isset($_FILES['files']) && is_array($_FILES['files']['name'])) {
					for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
						$files_to_upload[] = [
							'name' => $_FILES['files']['name'][$i],
							'type' => $_FILES['files']['type'][$i],
							'tmp_name' => $_FILES['files']['tmp_name'][$i],
							'error' => $_FILES['files']['error'][$i],
							'size' => $_FILES['files']['size'][$i],
						];
					}
				}
				$results = [];
				foreach ($files_to_upload as $file_to_upload) {
					$file_to_upload['name'] = strtolower($file_to_upload['name']);
					$file_to_upload['name'] = check_file_name_exist($target_dir, $file_to_upload['name']);

					$uploaded_url = '/files/images/' . $file_to_upload["name"];
					$target_file = $target_dir . $file_to_upload["name"];
					$ok = false;

					// Validate file type
					$validTypes = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'];
					if (in_array($file_to_upload['type'], $validTypes)) {
						$ok = true;
					}
					if ($ok) {
						if (move_uploaded_file($file_to_upload["tmp_name"], $target_file)) {
							$results[] = [
								'url' => $uploaded_url,
								'name' => $file_to_upload['name'],
								'size' => $file_to_upload['size'],
							];
						} else {
							echo '{"errorMessage": "' . _t('Upload failed!') . '"}';
							exit();
						}
					} else {
						echo '{"errorMessage": "' . _t('Image mime type not valid!') . '"}';
						exit();
					}
				}
				echo json_encode(['result' => $results]);
			} else {
				echo '{"errorMessage": "' . _t('Target dir not exist!') . '"}';
			}
		}
	}
}

// Check if file name exists and return a new file name if it does
function check_file_name_exist($dir, $fileName)
{
	$path = $dir . $fileName;
	if (file_exists($path)) {
		$info = pathinfo($fileName);
		$name = $info['filename'] . '-copy';
		$ext  = $info['extension'];
		return $name . "." . $ext;
	}
	return $fileName;
}

?>