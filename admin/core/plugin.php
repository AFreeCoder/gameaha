<?php

if (ADMIN_DEMO) {
	echo ('Restricted for "DEMO" mode.');
	return;
}

if (isset($_SESSION['message'])) {
	show_alert($_SESSION['message']['text'], $_SESSION['message']['type']);
	unset($_SESSION['message']);
}

if (isset($_GET['name'])) {
	$_GET['name'] = esc_slug($_GET['name']);
	if (is_plugin_exist($_GET['name'])) {
		if ($login_user->hasAccess('plugin', $_GET['name'])) {
			$plugin = get_plugin_info($_GET['name']);
			echo '<h4 class="plugin-title">';
			echo $plugin['name'];
			if (isset($plugin['documentation'])) {
				if (filter_var($plugin['documentation'], FILTER_VALIDATE_URL)) {
					echo '<a href="' . $plugin['documentation'] . '" target="_blank" class="tooltip-doc-plugin" data-bs-toggle="tooltip" data-bs-placement="left" title="' . _t('Click here to visit plugin manual or documentation.') . '"><i class="fas fa-question"></i></a>';
				}
			}
			echo '</h4>';
			if (file_exists($plugin['path'] . '/admin-page.php')) {
				require_once($plugin['path'] . '/admin-page.php');
			} else {
				// since v1.7.8 page.php is deprecated, use admin-page.php instead
				// this used for backward compatibility
				require_once($plugin['path'] . '/page.php');
			}
		} else {
			show_forbidden_alert();
		}
	} else {
		echo ('<div class="section">');
		_e('Plugin %a is missing or removed.', $_GET['name']);
		echo ('</div>');
	}
} else {
	if (isset($_GET['slug']) && $_GET['slug'] == 'upload') {
		include 'core/plugin-upload.php';
	} else if (isset($_GET['slug']) && $_GET['slug'] == 'install-premium-plugin') {
		include 'core/plugin-install-premium.php';
	} else {
		if (isset($_GET['status'])) {
			if ($_GET['status'] == 'success') {
				show_alert(isset($_GET['info']) ? $_GET['info'] : 'Plugin successfully installed!', 'success');
			} elseif ($_GET['status'] == 'warning') {
				show_alert(isset($_GET['info']) ? $_GET['info'] : 'Failed to install!', 'warning');
			} elseif ($_GET['status'] == 'error') {
				show_alert(isset($_GET['info']) ? $_GET['info'] : 'Error!', 'danger');
			}
		}

?>
		<div id="action-alert" style="display: none;">
			<?php show_alert('Plugin updated!', 'success') ?>
		</div>
		<div class="row">
			<div class="col-lg-8">
				<div class="section section-full">
					<?php

					if (isset($_GET['slug']) && $_GET['slug'] == 'repository') {
						include 'core/plugin-repository.php';
					} else {
						if (count($plugin_list) > 0) { ?>
							<div class="table-responsive">
								<table class="table custom-table">
									<thead>
										<tr>
											<th>#</th>
											<th><?php _e('Plugin') ?></th>
											<th><?php _e('Description') ?></th>
											<th><?php _e('Action') ?></th>
										</tr>
									</thead>
									<tbody class="plugin-list">
										<?php

										$index = 0;
										foreach ($plugin_list as $plugin) {
											$index++;
											$is_active = substr($plugin['dir_name'], 0, 1) == '_' ? false : true;
											$plugin_class = $is_active ? 'plugin-active' : 'plugin-inactive';
											$has_plugin_update = false;
											//
											if (!is_null($available_plugin_updates) && count($available_plugin_updates)) {
												if (in_array($plugin['dir_name'], $available_plugin_updates)) {
													$plugin_class .= ' plugin-update-available';
													$has_plugin_update = true;
												}
											}
										?>
											<tr class='<?php echo $plugin_class ?>'>
												<th scope="row"><?php echo $index ?></th>
												<td>
													<strong>
														<?php echo $plugin['name'] ?>
														<?php
														if ($has_plugin_update) {
															echo '<i class="plugin-update-icon text-success fas fa-exclamation-circle"></i>';
														}
														?>
													</strong>
													<br>
													Version <?php echo $plugin['version'] ?> | By <a href="<?php echo $plugin['website'] ?>" target="_blank"><?php echo $plugin['author'] ?></a>
												</td>
												<td><?php echo $plugin['description'] ?></td>
												<td><?php if ($is_active) {
														echo ('<a href="#" id="' . $plugin['dir_name'] . '" class="deactivate-plugin">' . _t('Deactivate') . '</a>');
													} else {
														echo ('<a href="#" id="' . $plugin['dir_name'] . '" class="activate-plugin">' . _t('Activate') . '</a>');
													} ?> | <a href="#" id="<?php echo $plugin['dir_name'] ?>" class="remove-plugin text-danger"><?php _e('Remove') ?></a>
													<?php
													if ($has_plugin_update) {
													?>
														<div class="plugin-update-btn b-<?php echo $plugin['dir_name'] ?>">
															<?php if (isset($plugin['is_premium']) && $plugin['is_premium']) { ?>
																<a href="#" data-id="<?php echo $plugin['dir_name'] ?>" class="update-premium-plugin text-success"><?php _e('Update') ?></a>
															<?php } else { ?>
																<a href="#" data-id="<?php echo $plugin['dir_name'] ?>" class="update-plugin text-success" data-path="https://api.cloudarcade.net/plugin-repo/plugins/<?php echo $plugin['dir_name'] ?>/<?php echo $plugin['dir_name'] ?>"><?php _e('Update') ?></a>
															<?php } ?>
														</div>
													<?php
													}
													?>
												</td>
											</tr>
										<?php
										}

										?>
									</tbody>
								</table>
							</div>
					<?php } else {
							echo '<div class="general-wrapper">';
							_e('No plugins installed!');
							echo '</div>';
						}
					}
					?>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="card border-0 shadow-sm">
					<!-- Plugin Statistics Section -->
					<div class="card-body border-bottom">
						<h5 class="card-title mb-3"><?php _e('Plugin Statistics') ?></h5>
						<div class="row g-3 text-center">
							<div class="col-6">
								<div class="p-3 bg-light rounded">
									<h3 class="mb-1"><?php echo count($plugin_list) ?></h3>
									<small class="text-muted"><?php _e('Installed') ?></small>
								</div>
							</div>
							<div class="col-6">
								<div class="p-3 bg-light rounded">
									<h3 class="mb-1"><?php echo count(array_filter($plugin_list, function ($p) {
															return substr($p['dir_name'], 0, 1) != '_';
														})) ?></h3>
									<small class="text-muted"><?php _e('Active') ?></small>
								</div>
							</div>
						</div>
					</div>

					<!-- Quick Actions Section -->
					<div class="card-body">
						<h5 class="card-title mb-3"><?php _e('Quick Actions') ?></h5>
						<!-- Browse Plugins Button Group -->
						<div class="d-grid gap-2 mb-4">
							<a  href="dashboard.php?viewpage=plugin&slug=repository"class="btn btn-primary d-flex align-items-center justify-content-between text-light">
								<span>
									<i class="fas fa-box-open me-2"></i>
									<?php _e('Plugin Repository') ?>
								</span>
								<i class="fas fa-chevron-right"></i>
							</a>

							<a href="https://store.cloudarcade.net" target="_blank"
								class="btn btn-success d-flex align-items-center justify-content-between text-light">
								<span>
									<i class="fas fa-store me-2"></i>
									<?php _e('Premium Plugin Store') ?>
								</span>
								<i class="fas fa-external-link-alt"></i>
							</a>
						</div>

						<!-- Plugin Management Buttons -->
						<div class="list-group list-group-flush mb-4">
							<a href="dashboard.php?viewpage=plugin&slug=upload"
								class="list-group-item list-group-item-action d-flex align-items-center">
								<i class="fas fa-upload me-3 text-primary"></i>
								<?php _e('Upload Plugin') ?>
							</a>

							<a href="dashboard.php?viewpage=plugin&slug=install-premium-plugin"
								class="list-group-item list-group-item-action d-flex align-items-center">
								<i class="fas fa-puzzle-piece me-3 text-primary"></i>
								<?php _e('Install Premium Plugin') ?>
							</a>

							<a href="https://cloudarcade.net/plugin-activity/" target="_blank"
								class="list-group-item list-group-item-action d-flex align-items-center">
								<i class="fas fa-history me-3 text-primary"></i>
								<?php _e('Activity Log') ?>
								<i class="fas fa-external-link-alt ms-auto"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
<?php
	}
}
?>