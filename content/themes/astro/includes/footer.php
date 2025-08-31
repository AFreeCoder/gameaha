<footer class="mt-10">
    <div class="max-w-7xl mx-auto py-12 px-4">
        <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
            <div class="space-y-8">
                <?php widget_aside('footer-1') ?>
            </div>
            <div class="space-y-8">
                <?php widget_aside('footer-2') ?>
            </div>
            <div class="space-y-8">
                <?php widget_aside('footer-3') ?>
            </div>
            <div class="space-y-8">
                <?php widget_aside('footer-4') ?>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-8">
            <p class="text-sm text-gray-400 text-center">
                <?php
                if (isset($stored_widgets['footer-copyright'])) {
                    widget_aside('footer-copyright');
                } else {
                    echo SITE_TITLE . ' © ' . date('Y') . '. All rights reserved.';
                }
                ?>
            </p>
            <?php if (is_login() && USER_ADMIN): ?>
                <p class="text-center mt-4">
                    <a href="<?php echo DOMAIN ?>admin.php" class="text-game-accent hover:text-game-accent/80">
                        Admin Dashboard
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</footer>
<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/lazysizes.min.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN ?>js/comment-system.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/script.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN ?>js/stats.js"></script>
<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/custom.js"></script>
<?php run_hook('footer') ?>
<?php load_plugin_footers() ?>
</body>

</html>