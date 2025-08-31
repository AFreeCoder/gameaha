<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<main class="min-h-screen bg-[#0F1729]">
    <!-- Hero Banner -->
    <section class="relative h-[40vh] flex items-center bg-[#0F1729]">
        <?php
        // Default hero content
        $hero_content = [
            'title' => 'Play Instant',
            'highlight' => 'Browser Games',
            'description' => 'Discover and play thousands of free online games instantly. No downloads required.'
        ];
        
        foreach ($hero_content as $key => $default) {
            $pref_value = get_theme_pref(THEME_NAME, "hero_{$key}");
            if ($pref_value) {
                $hero_content[$key] = $pref_value;
            }
        }
        ?>
        <!-- Background Grid -->
        <div class="absolute inset-0 grid grid-cols-3 md:grid-cols-6 gap-1 opacity-90">
            <?php
            $hero_games = fetch_games_by_type('popular', 6, 0, false)['results'];
            foreach ($hero_games as $game): ?>
                <div class="bg-cover bg-center" style="background-image: url('<?php echo $game->thumb_2 ?>')"></div>
            <?php endforeach; ?>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#0F1729]/90 via-[#0F1729]/80 to-[#0F1729]/90"></div>

        <div class="relative max-w-7xl mx-auto px-4 text-center mt-10">
            <h1 class="text-3xl md:text-5xl font-bold text-white mb-3">
                <?php _e($hero_content['title']) ?>
                <span class="bg-gradient-to-r from-violet-500 to-fuchsia-500 text-transparent bg-clip-text">
                    <?php _e($hero_content['highlight']) ?>
                </span>
            </h1>
            <p class="text-white text-xl max-w-lg mx-auto">
                <?php _e($hero_content['description']) ?>
            </p>
        </div>
    </section>

    <!-- Categories Strip -->
    <section class="bg-[#1E2A45] py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white"><?php _e('Categories') ?></h2>
                <button id="view-all-categories" class="text-sm text-violet-400 hover:text-violet-300" data-label-expand="<?php _e('View All') ?>" data-label-collapse="<?php _e('Show Less') ?>"><?php _e('View All') ?></button>
            </div>

            <!-- Featured Categories -->
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-4 mb-4">
                <?php
                $categories = fetch_all_categories();
                $featured_categories = array_slice($categories, 0, 12);
                foreach ($featured_categories as $cat):
                    $icon = get_category_icon_emoji($cat->slug);
                    $count = Category::getCategoryCount($cat->id);
                ?>
                    <a href="<?php echo get_permalink('category', $cat->slug) ?>"
                        class="flex items-center p-4 rounded-xl bg-[#2A364F] hover:bg-violet-600 transition-all group">
                        <span class="text-2xl mr-3"><?php echo $icon ?></span>
                        <div>
                            <span class="text-gray-300 group-hover:text-white text-sm font-medium block">
                                <?php echo esc_string($cat->name) ?>
                            </span>
                            <span class="text-xs text-gray-400"><?php _e('%a games', number_format($count)) ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Remaining Categories -->
            <div id="all-categories" class="hidden">
                <div class="grid grid-cols-2 sm:grid-cols-6 gap-4">
                    <?php
                    $remaining_categories = array_slice($categories, 12);
                    foreach ($remaining_categories as $cat):
                        $icon = get_category_icon_emoji($cat->slug);
                        $count = Category::getCategoryCount($cat->id);
                    ?>
                        <a href="<?php echo get_permalink('category', $cat->slug) ?>"
                            class="flex items-center p-4 rounded-xl bg-[#2A364F] hover:bg-violet-600 transition-all group">
                            <span class="text-2xl mr-3"><?php echo $icon ?></span>
                            <div>
                                <span class="text-gray-300 group-hover:text-white text-sm font-medium block">
                                    <?php echo esc_string($cat->name) ?>
                                </span>
                                <span class="text-xs text-gray-400"><?php _e('%a games', number_format($count)) ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Trending Games -->
    <section class="py-16 px-4" id="browse">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-white flex items-center">
                    <span class="text-violet-500 mr-3">🔥</span> <?php _e('Trending Now') ?>
                </h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php
                $trending = fetch_games_by_type('popular', 12, 0, false)['results'];
                foreach ($trending as $game):
                    include 'includes/grid-1.php';
                endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Latest Games Grid -->
    <section class="py-16 px-4 bg-[#1E2A45]">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-white flex items-center">
                    <span class="text-violet-500 mr-3">🎮</span> <?php _e('Latest Games') ?>
                </h2>
                <a href="<?php echo get_permalink('all-games') ?>"
                    class="px-6 py-2.5 rounded-full bg-violet-600 text-white hover:bg-violet-700 transition-colors">
                    <?php _e('View All Games') ?>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 gap-4" id="new-games-section">
                <?php
                $games = fetch_games_by_type('new', 70, 0, false)['results'];
                foreach ($games as $game):
                    include 'includes/grid-2.php';
                endforeach; ?>
            </div>
        </div>
    </section>

    <?php widget_aside('bottom-content') ?>
    <?php widget_aside('homepage-bottom') ?>
</main>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>