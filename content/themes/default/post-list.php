<?php include  TEMPLATE_PATH . "/includes/header.php" ?>
<div class="container">
	<div class="post-container">
		<div class="content-wrapper">
			<h3 class="page-title"><?php _e('LATEST POSTS') ?></h3>
			<section class="blog-list">
			<?php
				foreach($posts as $post){
					?>
						<div class="post-item">
							<div class="post-media">
								<div class="post-thumb">
									<img src="<?php echo ($post->thumbnail_url) ? $post->thumbnail_url : DOMAIN . 'images/post-no-thumb.png'  ?>" alt="<?php echo $post->title ?>">
								</div>
								<div class="post-body">
									<h3 class="post-title">
										<a href="<?php echo get_permalink('post', $post->slug) ?>"><?php echo $post->title ?></a>
									</h3>
									<div class="post-meta">
										<span class="date">Published on <?php echo gmdate("j M Y", $post->created_date) ?></span>
									</div>
									<div class="post-intro">
										<?php echo mb_strimwidth(strip_tags($post->content), 0, 250, "...") ?>
									</div>
									<a class="more-link" href="<?php echo get_permalink('post', $post->slug) ?>">Read more →</a>
								</div>
							</div>
						</div>
					<?php
				}
			?>
			</section>
			<div class="pagination-wrapper">
				<nav aria-label="Page navigation example">
					<?php
					render_pagination($total_page, $cur_page, 8, 'post', '');
					?>
				</nav>
			</div>
		</div>
	</div>
</div>
<?php include  TEMPLATE_PATH . "/includes/footer.php" ?>