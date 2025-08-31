<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<main class="min-h-screen bg-[#0F1729]">
    <!-- Search Results Banner -->
    <section class="relative">
        <div class="relative max-w-7xl mx-auto px-4 py-18">
            <div class="text-center pt-40">
                <span class="inline-block text-6xl mb-6 transform hover:scale-110 transition-transform">
                    🔍
                </span>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    <?php _e('%a Games', esc_string($archive_title)) ?>
                </h1>
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-violet-500/10 text-violet-300">
                    <?php _e('%a games', esc_int($total_games)) ?>
                    <span class="mx-2">•</span>
                    <?php _e('Page %a of %b', esc_int($cur_page), esc_int($total_page)) ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Games Grid -->
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <?php widget_aside('top-content') ?>

            <!-- Search Results Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php
                foreach ($games as $game) {
                    include TEMPLATE_PATH . "/includes/grid-2.php";
                }
                ?>
            </div>

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <?php
                    $cur_page = isset($_GET['page']) ? esc_int($_GET['page']) : 1;
                    render_pagination(
                        $total_page,
                        $cur_page,
                        8,
                        'search',
                        $_GET['slug'],
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
    </section>
</main>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>