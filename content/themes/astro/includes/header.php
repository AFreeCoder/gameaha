<!DOCTYPE html>
<html <?php the_html_attrs() ?>>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?php echo apply_filters('site_title', get_page_title()) ?></title>
    <?php the_canonical_link() ?>
    <meta name="description" content="<?php echo apply_filters('meta_description',substr($meta_description, 0, 360)) ?>">
    <?php the_head('top') ?>
    <?php
        if(is_game()){ //Game page
            ?>
            <meta property="og:type" content="game">
            <meta property="og:url" content="<?php echo get_canonical_url() ?>">
            <meta property="og:title" content="<?php echo apply_filters('site_title', get_page_title()) ?>">
            <meta property="og:description" content="<?php echo substr(esc_string($meta_description), 0, 200) ?>">
            <meta property="og:image" content="<?php echo (substr($game->thumb_1, 0, 1) == '/') ? home_url() . $game->thumb_1 : $game->thumb_1 ?>">
            <?php
        }
    ?>
    <meta name="color-scheme" content="dark">
    <link rel="stylesheet" type="text/css" href="<?php echo DOMAIN . TEMPLATE_PATH; ?>/css/tailwindcss.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo DOMAIN . TEMPLATE_PATH; ?>/css/style.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo DOMAIN . TEMPLATE_PATH; ?>/css/custom.css" />
    <?php load_plugin_headers() ?>
    <?php the_head('bottom') ?>
    <?php widget_aside('head') ?>
</head>

<body class="bg-game-dark text-gray-100">
    <nav class="bg-game-card/50 backdrop-blur-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Desktop Layout -->
            <div class="hidden md:flex justify-between h-20">
                <div class="flex items-center">
                    <!-- Logo -->
                    <a href="<?php echo DOMAIN ?>" class="flex-shrink-0 flex items-center">
                        <img class="h-10 w-auto" src="<?php echo DOMAIN . SITE_LOGO ?>" alt="<?php echo SITE_TITLE ?>">
                    </a>
                    <!-- Desktop Navigation -->
                    <div class="ml-10 flex space-x-8">
                        <?php render_nav_menu('top_nav', array(
                            'no_ul' => false,
                            'ul_class' => 'flex space-x-8',
                            'li_class' => 'relative group',
                            'li_class_parent' => 'relative',
                            'a_class' => 'text-sm font-medium text-gray-300 hover:text-white transition-colors',
                            'a_class_parent' => 'inline-flex items-center',
                            'after_parent' => '<svg class="ml-2 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>',
                            'children' => array(
                                'ul_class' => 'invisible group-hover:visible absolute left-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none transform opacity-0 group-hover:opacity-100 transition-all duration-200',
                                'li_class' => 'relative group/sub',
                                'li_class_parent' => 'relative parent_child',
                                'a_class' => 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100',
                                'submenu_ul_class' => 'invisible group-hover/sub:visible absolute left-full top-0 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none transform opacity-0 group-hover/sub:opacity-100 transition-all duration-200'
                            )
                        )); ?>
                    </div>
                </div>

                <!-- Desktop Search and Auth -->
                <div class="flex items-center space-x-4">
                    <form action="<?php echo DOMAIN ?>" class="relative search-bar">
                        <input type="hidden" name="viewpage" value="search">
                        <?php the_lang_input() ?>
                        <input type="text" name="slug" minlength="2" required
                            class="w-64 bg-game-dark/50 px-4 py-2 rounded-full border border-gray-700 focus:outline-none focus:border-game-accent"
                            placeholder="<?php _e('Search game') ?>">
                        <button type="submit" class="absolute right-3 top-2.5">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                    <?php if (is_null($login_user) && get_setting_value('show_login')): ?>
                        <a href="<?php echo get_permalink('login') ?>"
                            class="px-6 py-2 rounded-full text-sm font-medium text-white bg-game-accent hover:bg-game-accent/90 transition-colors">
                            <?php _e('Login') ?>
                        </a>
                    <?php else: ?>
                        <?php show_user_profile_header() ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mobile Layout -->
            <div class="md:hidden" x-data="{ open: false }">
                <div class="flex items-center justify-between h-20">
                    <!-- Mobile Menu Button -->
                    <button @click="open = !open" class="p-2 text-gray-300 hover:text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="<?php echo DOMAIN ?>" class="flex-shrink-0 flex items-center">
                        <img class="h-10 w-auto" src="<?php echo DOMAIN . SITE_LOGO ?>" alt="<?php echo SITE_TITLE ?>">
                    </a>

                    <!-- Mobile Auth -->
                    <div>
                        <?php if (is_null($login_user) && get_setting_value('show_login')): ?>
                            <a href="<?php echo get_permalink('login') ?>"
                                class="block w-full px-4 py-2 text-center rounded-full text-sm font-medium text-white bg-game-accent hover:bg-game-accent/90 transition-colors">
                                <?php _e('Login') ?>
                            </a>
                        <?php else: ?>
                            <?php show_user_profile_header() ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div x-show="open" class="pb-4">
                    <!-- Mobile Search -->
                    <div class="px-2 pt-2 pb-3">
                        <form action="<?php echo DOMAIN ?>" class="relative">
                            <?php the_lang_input() ?>
                            <input type="hidden" name="viewpage" value="search" />
                            <input type="text" name="slug" minlength="2" required
                                class="w-full bg-game-dark/50 px-4 py-2 rounded-full border border-gray-700 focus:outline-none focus:border-game-accent"
                                placeholder="<?php _e('Search game') ?>">
                            <button type="submit" class="absolute right-3 top-2.5">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile Navigation -->
                    <div class="px-2 pt-2 pb-3">
                        <?php render_nav_menu('top_nav', array(
                            'no_ul' => false,
                            'ul_class' => 'space-y-1',
                            'li_class' => 'block',
                            'li_class_parent' => 'block',
                            'a_class' => 'block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700',
                            'a_class_parent' => 'block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700',
                            'children' => array(
                                'ul_class' => 'pl-4 space-y-1',
                                'li_class' => 'block',
                                'a_class' => 'block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700'
                            )
                        )); ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</body>
</html>