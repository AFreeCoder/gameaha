<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<main class="min-h-screen bg-[#0F1729] pt-24">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header Section -->
        <section class="bg-[#1E2A45] rounded-2xl p-6 mb-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4 flex items-center justify-center">
                    <span class="text-violet-500 mr-3">📰</span><?php _e('Latest Posts') ?>
                </h1>
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-violet-500/10 text-violet-300">
                    <?php _e('%a Posts', esc_int($total_posts)) ?>
                    <span class="mx-2">•</span>
                    <?php _e('Page %a of %b', esc_int($cur_page), esc_int($total_page)) ?>
                </div>
            </div>
        </section>

        <!-- Posts Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <?php foreach($posts as $post): ?>
                <article class="bg-[#1E2A45] rounded-xl overflow-hidden shadow-xl transition-transform hover:scale-105">
                    <a href="<?php echo get_permalink('post', $post->slug) ?>" class="block">
                        <img 
                            src="<?php echo ($post->thumbnail_url) ? $post->thumbnail_url : DOMAIN . 'images/post-no-thumb.png' ?>" 
                            alt="<?php echo htmlspecialchars($post->title) ?>"
                            class="w-full h-48 object-cover"
                        >
                        <div class="p-4">
                            <h2 class="text-lg font-bold text-white mb-2 line-clamp-2 hover:text-violet-400">
                                <?php echo htmlspecialchars($post->title) ?>
                            </h2>
                            <p class="text-gray-400 text-sm mb-4 line-clamp-2">
                                <?php echo mb_strimwidth(strip_tags($post->content), 0, 80, "...") ?>
                            </p>
                            <div class="flex items-center text-sm text-gray-500">
                                <span class="flex items-center">
                                    📅 <?php echo gmdate("j M Y", $post->created_date) ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mb-12">
            <nav class="flex items-center space-x-2">
                <?php
                render_pagination(
                    $total_page,
                    $cur_page,
                    8,
                    'post',
                    '',
                    [
                        'container_class' => 'flex items-center space-x-4',
                        'item_class' => '',
                        'link_class' => 'px-4 py-2 rounded-md transition-colors bg-violet-500/10 text-violet-300 hover:bg-violet-600 hover:text-white [.current_&]:bg-violet-600 [.current_&]:text-white [.current_&]:cursor-not-allowed text-lg',
                        'disabled_class' => 'current',
                    ]
                );
                ?>
            </nav>
        </div>

        <?php widget_aside('bottom-content') ?>
    </div>
</main>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>