<?php

if (!has_admin_access()) {
    exit('Access denied');
}

$is_plugin_installed = false;

if(isset($_POST['action'])) {
    if($_POST['action'] == 'begin-install-plugin-with-code') {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $purchase_code = preg_replace('/[^A-Z0-9\-]/', '', strtoupper($_POST['pcode']));
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            show_alert('Please enter a valid email address.', 'danger');
        } else {
            try {
                if (!is_writable(ABSPATH . 'content/plugins/')) {
                    throw new Exception('Plugin directory is not writable. Please check permissions.');
                }

                $result = install_product($email, $purchase_code, 'plugin');
                
                // Store the purchase information using the plugin slug from installation
                set_plugin_pref($result['slug'], 'purchase_email', $email);
                set_plugin_pref($result['slug'], 'purchase_code', $purchase_code);
                
                $message = sprintf(
                    'Installation successful! %s version %s has been installed.',
                    htmlspecialchars($result['item_name']),
                    htmlspecialchars($result['version'])
                );
                $is_plugin_installed = true;
                show_alert($message, 'success');
                
            } catch (Exception $e) {
                show_alert($e->getMessage(), 'danger');
                error_log('Plugin installation failed: ' . $e->getMessage());
            }
        }
    }
}

if(!$is_plugin_installed){
?>

<div class="bs-callout bs-callout-info">
    If you've already purchased a plugin, you can submit your purchase code here to install the plugin.
</div>

<div class="row">
    <div class="col-md-4">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="begin-install-plugin-with-code">
            
            <div class="mb-3">
                <label class="form-label">The email you used for the purchase</label>
                <input type="email" 
                       class="form-control" 
                       name="email" 
                       required
                       value="<?php echo htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : ''); ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Your plugin purchase code</label>
                <input type="text" 
                       class="form-control" 
                       name="pcode" 
                       required
                       pattern="CS-[A-Z0-9\-]+"
                       title="Please enter a valid purchase code in the format CS-XXXX-XXXX-XXXX"
                       value="<?php echo htmlspecialchars(isset($_POST['pcode']) ? $_POST['pcode'] : ''); ?>">
            </div>
            
            <input type="submit" class="btn btn-primary" value="Install">
        </form>
    </div>
</div>

<?php } else { ?>
    <a href="dashboard.php?viewpage=plugin" class="btn btn-primary"><?php _e('Back to Manage Plugins') ?></a>
<?php } ?>