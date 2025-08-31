<?php

$page_title = _t('All Games');

$meta_description = _t('All Games');

?>

<?php include TEMPLATE_PATH . "/includes/header.php" ?>

<?php

$cur_page = 1;
if(isset($url_params[1])){
    $_GET['page'] = $url_params[1];
    if(!is_numeric($_GET['page'])){
        $_GET['page'] = 1;
    }
}
if(isset($_GET['page'])){
    $cur_page = htmlspecialchars($_GET['page']);
    if(!is_numeric($cur_page)){
        $cur_page = 1;
    }
}

$game_data = fetch_games_by_type('new', 42, 42*($cur_page-1), true);
$games = $game_data['results'];
$total_games = $game_data['totalRows'];
$total_page = $game_data['totalPages'];

?>

<main class="min-h-screen bg-[#0F1729]">
    <section class="relative">
        <div class="relative max-w-7xl mx-auto px-4">
            <div class="text-center pt-40">
                <span class="inline-block text-6xl mb-6 transform hover:scale-110 transition-transform">
                    🎮
                </span>
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                    <?php _e('All Games') ?>
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

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 gap-4">
                <?php
                foreach ($games as $game) {
                    include TEMPLATE_PATH . "/includes/grid-2.php";
                }
                ?>
            </div>

            <?php widget_aside('bottom-content') ?>
        </div>
    </section>

    <!-- Pagination -->
    <div class="mt-12 flex justify-center">
        <nav class="flex items-center space-x-2">
            <?php
            $cur_page = isset($_GET['page']) ? esc_int($_GET['page']) : 1;
            render_pagination(
                $total_page,
                $cur_page,
                8,
                'all-games',
                '',
                [
                    'container_class' => 'flex items-center space-x-4',
                    'item_class' => '',
                    'link_class' => 'px-4 py-2 rounded-md transition-colors bg-violet-500/10 text-violet-300 hover:bg-violet-600 hover:text-white [.current_&]:bg-violet-600 [.current_&]:text-white [.current_&]:cursor-not-allowed text-lg', // Increased padding and font size
                    'disabled_class' => 'current',
                ]
            );
            ?>
        </nav>
    </div>
</main>

<?php include TEMPLATE_PATH . "/includes/footer.php" ?>