<?php include  TEMPLATE_PATH . "/includes/header.php" ?>
<div class="container">
	<div class="game-container single-page">
	<div class="row">
		<div class="col-md-9">
			<div class="singlepage-post">
				<h1 class="singlepage-title"><?php echo htmlspecialchars( $post->title )?></h1>
				<div class="post-meta">
					Published on <?php echo gmdate("j M Y", $post->created_date) ?>
				</div>
				<div class="page-content">
					<?php echo apply_filters('single_post_content', $post) ?>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<?php include  TEMPLATE_PATH . "/parts/sidebar.php" ?>
		</div>
	</div>
	</div>
</div>
<?php include  TEMPLATE_PATH . "/includes/footer.php" ?>