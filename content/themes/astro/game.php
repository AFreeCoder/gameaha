<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<main class="min-h-screen bg-[#0F1729] pt-28">
	<div class="max-w-7xl mx-auto px-4">
		<!-- Main Content Grid -->
		<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
			<!-- Main Content Column -->
			<div class="lg:col-span-3">
				<!-- Game Container -->
				<div class="bg-[#1E2A45] rounded-2xl overflow-hidden shadow-xl mb-8">
					<div class="game-content relative" data-id="<?php echo $game->id ?>">
						<!-- Game iframe -->
						<div class="game-iframe-container" id="game-player">
							<iframe class="game-iframe w-full aspect-video" id="game-area"
								src="<?php echo get_game_url($game); ?>"
								width="<?php echo esc_int($game->width); ?>"
								height="<?php echo esc_int($game->height); ?>"
								scrolling="no"
								frameborder="0"
								allowfullscreen></iframe>
						</div>
					</div>

					<!-- Game Info Bar -->
					<div class="p-6 bg-[#2A364F]">
						<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
							<div>
								<h1 class="text-2xl md:text-3xl font-bold text-white mb-2">
									<?php echo htmlspecialchars($game->title) ?>
								</h1>
								<div class="flex items-center space-x-4 text-sm text-gray-400">
									<div class="flex items-center">
										⭐ <?php echo get_rating('5-decimal', $game) ?>
										<span class="ml-1">(<?php echo $game->upvote + $game->downvote ?> <?php _e('Reviews') ?>)</span>
									</div>
									<div class="flex items-center">
										🎮 <?php _e('%a Plays', $game->views) ?>
									</div>
								</div>
							</div>

							<!-- Action Buttons -->
							<div class="flex flex-wrap items-center gap-2">
								<div class="flex gap-2 single-game__actions">
									<?php if ($login_user): ?>
										<button
											class="w-10 h-10 flex items-center justify-center rounded-full transition-colors duration-200 <?php echo is_favorited_game($game->id) ? 'text-red-500 hover:text-red-600' : 'text-gray-400 hover:text-violet-500' ?>"
											id="favorite"
											data-id="<?php echo $game->id ?>">
											<svg class="w-5 h-5" viewBox="0 0 24 24" fill="<?php echo is_favorited_game($game->id) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
												<path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
											</svg>
										</button>
									<?php endif; ?>

									<button
										class="w-10 h-10 flex items-center justify-center rounded-full transition-colors duration-200 text-gray-400 hover:text-violet-500"
										id="upvote"
										data-id="<?php echo $game->id ?>">
										<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3" />
										</svg>
									</button>

									<button
										class="w-10 h-10 flex items-center justify-center rounded-full transition-colors duration-200 text-gray-400 hover:text-violet-500"
										id="downvote"
										data-id="<?php echo $game->id ?>">
										<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h3a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-3" />
										</svg>
									</button>
								</div>

								<a href="<?php echo get_permalink('full', $game->slug); ?>" target="_blank"
									class="px-6 py-2.5 rounded-full bg-violet-600 text-white hover:bg-violet-700 transition-colors btn-game-primary">
									🔲 <?php _e('New Window') ?>
								</a>

								<button onclick="openFullscreen()" class="px-6 py-2.5 rounded-full bg-violet-600 text-white hover:bg-violet-700 transition-colors btn-game-primary">
									📺 <?php _e('Fullscreen') ?>
								</button>

								<!-- Report button -->
								<?php if (is_plugin_exist('game-reports')): ?>
									<button id="report-game" class="btn-game-action">
										🚨
									</button>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<!-- Description -->
				<div class="bg-[#1E2A45] rounded-xl p-6 mb-8">
					<h2 class="text-xl font-bold text-white mb-4 flex items-center">
						📝 <?php _e('Description') ?>
					</h2>
					<div class="text-gray-300 space-y-4">
						<?php echo apply_filters('single_game_description', $game) ?>
					</div>
				</div>

				<!-- Instructions -->
				<div class="bg-[#1E2A45] rounded-xl p-6 mb-8">
					<h2 class="text-xl font-bold text-white mb-4 flex items-center">
						📋 <?php _e('Instructions') ?>
					</h2>
					<div class="text-gray-300 space-y-4">
						<?php echo apply_filters('single_game_instructions', $game) ?>
					</div>
				</div>

				<!-- Leaderboard -->
				<?php if (can_show_leaderboard()) { ?>
					<div class="bg-[#1E2A45] rounded-xl p-6 mb-8">
						<h2 class="text-xl font-bold text-white mb-4 flex items-center" id="subheading-leaderboard" style="display: none;">
							🏆 <?php _e('Leaderboard') ?>
						</h2>
						<div id="content-leaderboard" class="text-gray-300" data-id="<?php echo $game->id ?>"></div>
					</div>
				<?php } ?>

				<!-- Categories -->
				<?php if ($game->category): ?>
					<div class="bg-[#1E2A45] rounded-xl p-6 mb-8">
						<h2 class="text-xl font-bold text-white mb-4 flex items-center">
							🎯 <?php _e('Categories') ?>
						</h2>
						<div class="flex flex-wrap gap-2">
							<?php
							$categories = $game->getCategoryList();
							foreach ($categories as $cat):
								$category = Category::getById($cat['id']);
								if ($category && Category::getCategoryCount($category->id) > 0):
									$icon = get_category_icon_emoji($category->slug);
							?>
									<a href="<?php echo get_permalink('category', $category->slug) ?>"
										class="inline-flex items-center px-4 py-2 rounded-full bg-violet-600/20 text-violet-300 hover:bg-violet-600 hover:text-white transition-colors">
										<span class="mr-2"><?php echo $icon ?></span>
										<?php _e(esc_string($category->name)) ?>
									</a>
							<?php
								endif;
							endforeach;
							?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Tags -->
				<?php
				$tag_string = $game->get_tags();
				if ($tag_string != ''): ?>
					<div class="bg-[#1E2A45] rounded-xl p-6 mb-8">
						<h2 class="text-xl font-bold text-white mb-4 flex items-center">
							🏷️ <?php _e('Tags') ?>
						</h2>
						<div class="flex flex-wrap gap-2">
							<?php
							$tags = explode(',', $tag_string);
							foreach ($tags as $tag_name): ?>
								<a href="<?php echo get_permalink('tag', $tag_name) ?>"
									class="px-3 py-1 rounded-full bg-[#2A364F] text-sm text-gray-300 hover:bg-violet-600 hover:text-white transition-colors">
									<?php echo _t(esc_string($tag_name)) ?>
								</a>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Comments -->
				<?php if (get_setting_value('comments')): ?>
					<div class="bg-[#1E2A45] rounded-xl p-6 mb-8 single-game__comment">
						<h2 class="text-xl font-bold text-white mb-4 flex items-center">
							💭 <?php _e('Comments') ?>
						</h2>
						<?php render_game_comments($game->id) ?>
					</div>
				<?php endif; ?>

				<!-- You May Like -->
				<section class="bg-[#1E2A45] rounded-xl p-6 mb-8">
					<h2 class="text-xl font-bold text-white mb-6 flex items-center">
						🎮 <?php _e('You May Like') ?>
					</h2>
					<div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
						<?php
						$games = fetch_games_by_type('random', 18, 0, false)['results'];
						foreach ($games as $game) {
							include  TEMPLATE_PATH . "/includes/grid-2.php";
						}
						?>
					</div>
				</section>
			</div>

			<!-- Sidebar -->
			<div class="lg:col-span-1">
				<div class="sticky top-24">
					<?php widget_aside('sidebar-1') ?>
				</div>
			</div>
		</div>

		<?php widget_aside('bottom-content') ?>
	</div>
</main>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>