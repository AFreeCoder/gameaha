<?php
if (isset($_GET['status'])) {
    $type = 'success';
    $message = '';
    if ($_GET['status'] == 'updated') {
        $message = 'CloudArcade successfully updated to version ' . VERSION . '!';
    } elseif ($_GET['status'] == 'error') {
        $type = 'warning';
        $message = 'Error: ' . esc_string($_GET['info']);
    }
    show_alert($message, $type);
}
?>

<div class="check-update"></div>
<div class="section rounded p-4 shadow-sm">
    <?php if (!check_purchase_code() && !ADMIN_DEMO): ?>
        <div class="alert alert-warning border-0 shadow-sm">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fa-2x text-warning"></i>
                <div>
                    <p class="mb-1">Please provide your <b>Item Purchase code</b>. You can submit or update your Purchase code on site settings.</p>
                    <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code"
                        target="_blank"
                        class="btn btn-outline-warning btn-sm mt-2">
                        <i class="fas fa-external-link-alt me-1"></i> Where to get Envato purchase code?
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>

        <?php if (!ADMIN_DEMO): ?>
            <?php
            if (function_exists('check_writeable')) {
                if (!check_writeable()) {
                    $msg = 'CloudArcade don\'t have permissions to modify files, any settings can\'t be saved and can\'t do backup and update. Change all folders and files CHMOD to 777 to fix this.';
            ?>
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex">
                            <i class="fas fa-shield-alt me-3 fa-2x text-warning"></i>
                            <div><?php echo $msg; ?></div>
                        </div>
                    </div>
                <?php
                }
            }

            if (isset($_GET['beta'])) {
                $_GET['test_update'] = true;
            }

            require_once ABSPATH . 'classes/SystemUpdater.php';
            $updater = new SystemUpdater();
            $result = $updater->checkUpdate();
            $has_cms_update = false;

            if ($result['status'] === 'update'): ?>
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                    <h5 class="mb-0">CloudArcade version <?php echo $result['next_version']; ?> is available!</h5>
                </div>
                <?php
                $has_cms_update = true;

                // Fetch changelog data for the next version
                $current_version = VERSION;
                $next_version = $result['next_version'];
                $changelog_data = null;

                try {
                    $api_url = "https://cloudarcade.net/changelog/fetch.php?version=v" . urlencode($next_version);
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $api_url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 10);

                    $response = curl_exec($curl);
                    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);

                    if ($http_code === 200 && !empty($response)) {
                        $changelog_data = json_decode($response, true);
                    }
                } catch (Exception $e) {
                    // Silent fail, will just not show changelog
                }
                ?>

                <?php if ($changelog_data && $changelog_data['status'] === 'success'): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-0">
                            <h5 class="mb-0">
                                <i class="fas fa-clipboard-list me-2"></i>
                                <?php _e('What\'s new in %a', $changelog_data['data']['version']) ?>
                                <small class="fs-6 ms-2">(<?php _e('Released: %a', $changelog_data['data']['release_date']); ?>)</small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php
                            $sections = [
                                'new' => ['icon' => 'fa-plus-circle', 'title' => 'New Features'],
                                'changes' => ['icon' => 'fa-exchange-alt', 'title' => 'Changes'],
                                'fix' => ['icon' => 'fa-bug', 'title' => 'Bug Fixes']
                            ];

                            foreach ($sections as $key => $section):
                                if (empty($changelog_data['data']['sections'][$key])) continue;
                            ?>
                                <div class="mb-4">
                                    <h6 class="mb-3">
                                        <i class="fas <?php echo $section['icon']; ?> me-2"></i>
                                        <strong><?php _e($section['title']) ?></strong>
                                    </h6>
                                    <div class="ps-3">
                                        <?php foreach ($changelog_data['data']['sections'][$key] as $item): ?>
                                            <div class="mb-3 ms-2">
                                                <h6 class="mb-1"><?php echo $item['title']; ?></h6>
                                                <?php if (!empty($item['description'])): ?>
                                                    <p class="mb-1 text-muted ms-3">
                                                        <?php echo $item['description']; ?>
                                                        <?php if (!empty($item['link'])): ?>
                                                            <a href="<?php echo $item['link']; ?>" target="_blank" class="text-decoration-none">
                                                                More info
                                                            </a>
                                                        <?php endif; ?>
                                                    </p>
                                                <?php elseif (!empty($item['link'])): ?>
                                                    <p class="mb-1 ms-3">
                                                        <a href="<?php echo $item['link']; ?>" target="_blank" class="text-decoration-none">
                                                            More info
                                                        </a>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            <?php elseif ($result['status'] === 'current'):
                set_pref('cms_update_available', false);
            ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                    <h5 class="mb-0"><?php _e('Congratulation! You are up to date.'); ?></h5>
                </div>
            <?php else: ?>
                <div class="alert alert-danger border-0 shadow-sm">
                    <div class="d-flex">
                        <i class="fas fa-exclamation-circle me-3 fa-2x"></i>
                        <div>Error checking for updates: <?php echo $result['message']; ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($has_cms_update): ?>
                <div class="alert alert-info border-0 shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 fa-2x"></i>
                        <div>
                            Read more info about the update history here
                            <a href="https://cloudarcade.net/changelog/" target="_blank" class="text-decoration-none">
                                https://cloudarcade.net/changelog/
                                <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="progress mb-4 d-none">
                    <div class="progress-bar progress-bar-striped progress-bar-animated"
                        role="progressbar"
                        aria-valuenow="75"
                        aria-valuemin="0"
                        aria-valuemax="100"
                        style="width: 100%">
                    </div>
                </div>

                <form id="form-update" method="post" enctype="multipart/form-data" class="mb-4">
                    <div class="form-group">
                        <input type="hidden" name="action" value="updater">
                        <input type="hidden" name="redirect" value="<?php echo DOMAIN ?>admin/dashboard.php?viewpage=update">
                        <input type="hidden" name="code" minlength="5" value="<?php echo check_purchase_code() ?>" required />
                        <?php if (isset($_GET['beta'])): ?>
                            <input type="hidden" name="test" value="true">
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="btn-update">
                            <i class="fas fa-cloud-download-alt me-2"></i>
                            <?php _e('Update Now') ?>
                        </button>
                    </div>
                </form>

                <div id="update-error" class="d-none">
                    <div class="alert alert-danger border-0 shadow-sm" id="u-error"></div>
                    <div class="mt-3">
                        <h6><?php _e('Server response') ?>:</h6>
                        <div class="alert alert-warning border-0 shadow-sm" id="u-response"></div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!ADMIN_DEMO): ?>
                <div class="mt-5">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header border-0">
                            <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Got issues after updating?</h5>
                        </div>
                        <div class="card-body">
                            <p>You can go back to previous version using <b>Backup Restore</b> plugin. Each update attempt, system will create a backup file (Games and thumbnail files are not backed up).</p>
                            <p class="mb-0">
                                Have an unknown issues? Contact us
                                <a href="dashboard.php?viewpage=support" class="text-decoration-none">here</a>
                                or visit our
                                <a href="https://codecanyon.net/user/redfoc" target="_blank" class="text-decoration-none">
                                    codecanyon profile <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-0">
                            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>How updater works?</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">The updater will override specific files and folders that have changes in the new version, and can modify database tables. Custom modifications to core CMS files can potentially lost during updates.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="card border-0 bg-success text-white">
                <div class="card-body d-flex align-items-center">
                    <i class="fas fa-check-circle fa-2x me-3"></i>
                    <h5 class="mb-0"><?php _e('Congratulation! You are up to date.'); ?></h5>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>