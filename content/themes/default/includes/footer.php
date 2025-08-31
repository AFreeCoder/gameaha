	<footer class="footer">
		<div class="container">
			<div class="row">
				<div class="col-lg-3">
					<div class="footer-1">
						<?php widget_aside('footer-1') ?>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="footer-2">
						<?php widget_aside('footer-2') ?>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="footer-3">
						<?php widget_aside('footer-3') ?>
					</div>
				</div>
				<div class="col-lg-3">
					<div class="footer-4">
						<?php widget_aside('footer-4') ?>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<div class="copyright py-4 text-center text-white">
		<div class="container">
			<?php
			if(isset($stored_widgets['footer-copyright'])){
				widget_aside('footer-copyright');
			} else {
				echo SITE_TITLE . ' © '.date('Y').'. All rights reserved.';
			}
			?>
			<span class="dsb-panel">
				<?php
				if(is_login() && USER_ADMIN ){
					echo '<a href="'.DOMAIN.'admin.php">Admin Dashboard</a>';
				} else {
					echo 'V-'.VERSION;
				}
				?>
			</span>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/jquery-3.6.2.min.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/lazysizes.min.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN ?>js/comment-system.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/script.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN . TEMPLATE_PATH ?>/js/custom.js"></script>
	<script type="text/javascript" src="<?php echo DOMAIN ?>js/stats.js"></script>
	<?php run_hook('footer') ?>
	<?php load_plugin_footers() ?>
  </body>
</html>