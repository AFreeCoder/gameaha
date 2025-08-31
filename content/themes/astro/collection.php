<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<main class="min-h-screen bg-[#0F1729]">
    <!-- Collection Banner -->
    <section class="relative">
        <div class="relative max-w-7xl mx-auto px-4 py-18">
            <div class="text-center pt-40">
                <span class="inline-block text-6xl mb-6 transform hover:scale-110 transition-transform">
                    🎮
                </span>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    <?php _e('%a Games', esc_string($archive_title)) ?>
                </h1>
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-violet-500/10 text-violet-300">
                    <?php _e('%a games', esc_int($total_games)) ?>
                </div>
                <?php if ($collection->description != ''): ?>
                    <div class="mt-8 text-gray-300 max-w-2xl mx-auto">
                        <?php echo nl2br($collection->description) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Games Grid -->
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <?php widget_aside('top-content') ?>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php
                foreach ($games as $game) {
                    include TEMPLATE_PATH . "/includes/grid-1.php";
                }
                ?>
            </div>

            <?php widget_aside('bottom-content') ?>
        </div>
    </section>
</main>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>