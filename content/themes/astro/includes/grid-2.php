<?php
$_game_title = get_content_title_translation('game', $game->id, $game->title);
?>
<a href="<?php echo get_permalink('game', $game->slug) ?>"
	class="group block">
	<div class="relative aspect-[1/1] rounded-xl overflow-hidden bg-[#2A364F]">
		<img src="<?php echo get_template_path(); ?>/images/thumb-placeholder1.png" data-src="<?php echo get_small_thumb($game) ?>"
			alt="<?php echo esc_string($_game_title) ?>"
			class="w-full h-full object-cover lazyload">
		<!-- Hover Overlay -->
		<div class="absolute inset-0 bg-gradient-to-t from-violet-900/90 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300">
			<div class="absolute bottom-0 left-0 right-0 p-4">
				<h3 class="text-white font-medium line-clamp-2">
					<?php echo esc_string($_game_title) ?>
				</h3>
				<div class="flex items-center mt-2 text-sm">
					<div class="flex items-center text-yellow-400">
						<svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
							<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
						</svg>
						<?php echo get_rating('5-decimal', $game) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</a>