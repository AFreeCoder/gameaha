<?php

// Load the icons
$category_icons = json_decode(file_get_contents(ABSPATH . TEMPLATE_PATH . '/includes/category-icons.json'), true);

// Function to get icon for a slug
function get_category_icon_emoji($slug) {
    global $category_icons;
    foreach ($category_icons as $icon => $slugs) {
        if (in_array($slug, $slugs)) {
            return $icon;
        }
    }
    return '🎮'; // Default icon
}

function show_user_profile_header() {
    global $login_user;
    
    if ($login_user) {
    ?>
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" @click.away="open = false" 
            class="flex items-center space-x-3 p-1.5 rounded-full hover:bg-gray-700/50 transition-colors">
            <img src="<?php echo get_user_avatar() ?>" 
                class="h-8 w-8 rounded-full object-cover" 
                alt="<?php echo $login_user->username ?>">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="open" 
            class="absolute right-0 mt-2 w-48 rounded-xl bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 py-1"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100" style="display: none;">
            
            <div class="px-4 py-2">
                <p class="text-sm font-medium text-white truncate"><?php echo $login_user->username ?></p>
                <p class="text-xs text-violet-400 mt-1"><?php echo $login_user->xp ?> XP</p>
            </div>
            
            <div class="border-t border-gray-700 my-1"></div>
            
            <a href="<?php echo get_permalink('user', $login_user->username) ?>" 
                class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                <svg class="inline-block w-4 h-4 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <?php _e('My Profile') ?>
            </a>
            
            <a href="<?php echo get_permalink('user', $login_user->username, array('edit' => 'edit')) ?>" 
                class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                <svg class="inline-block w-4 h-4 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <?php _e('Edit Profile') ?>
            </a>

            <div class="border-t border-gray-700 my-1"></div>

            <a href="<?php echo DOMAIN ?>admin.php?action=logout" 
                class="block px-4 py-2 text-sm text-red-400 hover:bg-gray-700 hover:text-red-300">
                <svg class="inline-block w-4 h-4 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} 
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <?php _e('Log Out') ?>
            </a>
        </div>
    </div>
    <?php
    }
}

function custom_render_game_comments($game_id) {
    ?>
    <div id="tpl-comment-section" data-id="<?php echo esc_int($game_id) ?>" class="space-y-6">
        <?php if(is_login()){ ?>
            <div id="comment-form" class="flex gap-4">
                <div class="comment-profile-avatar flex-shrink-0">
                    <img src="<?php echo get_user_avatar() ?>" 
                         alt="User Avatar"
                         class="w-10 h-10 rounded-full">
                </div>
                <div class="comment-form-wrapper flex-grow" id="tpl-comment-form">
                    <div class="tpl-alert-tooshort hidden mb-2 text-red-400 text-sm">
                        <?php _e('Your comment is too short. Please enter at least {{min}} characters.') ?>
                    </div>
                    <textarea 
                        class="tpl-comment-input w-full rounded-xl bg-[#1E2A45] border-2 border-[#2A364F] focus:border-violet-600 focus:ring-0 text-gray-200 placeholder-gray-500 p-3 mb-2"
                        rows="3" 
                        placeholder="Enter your comment here..."></textarea>
                    <div class="post-comment-btn-wrapper flex justify-end">
                        <button class="tpl-post-comment-btn px-4 py-2 bg-violet-600 text-white rounded-xl hover:bg-violet-700 transition-colors text-sm font-medium">
                            <?php _e('Post comment') ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="comment-require-login-wrapper flex gap-4 items-center p-4 rounded-xl bg-[#1E2A45]">
                <div class="comment-profile-avatar flex-shrink-0">
                    <img src="<?php echo DOMAIN . 'images/default_profile.png' ?>" 
                         alt="Default Avatar"
                         class="w-10 h-10 rounded-full">
                </div>
                <div class="comment-alert text-gray-400">
                    <?php _e('You must log in to write a comment.') ?>
                </div>
            </div>
        <?php } ?>

        <div id="tpl-comment-list" class="space-y-6">
            <!-- Comments will be loaded here -->
        </div>

        <!-- Comment Templates -->
        <div id="tpl-comment-template" class="hidden">
            <!-- User comment template -->
            <div class="tpl-user-comment" data-id="{{comment_id}}">
                <div class="user-comment-wrapper flex gap-4">
                    <div class="user-comment-avatar flex-shrink-0">
                        <img class="tpl-user-comment-avatar w-10 h-10 rounded-full" 
                             src="{{profile_picture_url}}" 
                             alt="User Avatar">
                    </div>
                    <div class="comment-content flex-grow">
                        <div class="flex items-baseline gap-2 mb-1">
                            <div class="tpl-comment-author text-white font-medium">{{fullname}}</div>
                            <div class="tpl-comment-timestamp text-sm text-gray-400">{{created}}</div>
                        </div>
                        <div class="tpl-comment-text text-gray-300 mb-3">{{content}}</div>
                        <div class="comment-actions flex justify-between items-center">
                            <div class="comment-action-left">
                                <div class="reply-wrapper space-x-2">
                                    <button class="tpl-btn-show-replies text-sm text-violet-400 hover:text-violet-300" data-id="{{comment_id}}">
                                        💬 <?php _e('Show replies') ?>
                                    </button>
                                    <button class="tpl-btn-hide-replies hidden text-sm text-violet-400 hover:text-violet-300" data-id="{{comment_id}}">
                                        💬 <?php _e('Hide replies') ?>
                                    </button>
                                </div>
                            </div>
                            <?php if(is_login()){ ?>
                                <div class="comment-action-right">
                                    <button class="tpl-comment-reply text-sm text-violet-400 hover:text-violet-300" data-id="{{comment_id}}">
                                        ↩️ <?php _e('Reply') ?>
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="tpl-reply-form-wrapper ml-14 mt-4"></div>
                <div class="tpl-comment-children ml-14 mt-4 space-y-4"></div>
            </div>

            <!-- Reply form template -->
            <div class="tpl-reply-form bg-[#1E2A45] rounded-xl p-4">
                <div class="comment-reply-wrapper space-y-3">
                    <textarea 
                        class="tpl-reply-input w-full rounded-xl bg-[#2A364F] border-2 border-[#2A364F] focus:border-violet-600 focus:ring-0 text-gray-200 placeholder-gray-500 p-3"
                        placeholder="Your reply..."></textarea>
                    <div class="reply-action-buttons flex justify-end gap-2">
                        <button class="tpl-btn-cancel-reply px-4 py-2 text-sm text-gray-300 hover:text-white" data-id="{{comment_id}}">
                            <?php _e('Cancel') ?>
                        </button>
                        <button class="tpl-btn-send-reply px-4 py-2 bg-violet-600 text-white rounded-xl hover:bg-violet-700 transition-colors text-sm font-medium" data-id="{{comment_id}}">
                            <?php _e('Reply') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Load More Button -->
        <button id="tpl-btn-load-more-comments" 
                class="hidden w-full p-3 text-center text-violet-400 hover:text-violet-300 bg-[#1E2A45] rounded-xl">
            <?php _e('Load more comments') ?> ⌄
        </button>
    </div>
    <?php
}

function wgt_list_games_grid($type, $amount) {
    $data = fetch_games_by_type($type, $amount, 0, false);
    $games = $data['results'];
    ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <?php foreach ($games as $_game): ?>
            <a href="<?php echo get_permalink('game', $_game->slug) ?>" 
               class="group block">
                <div class="relative aspect-[1/1] rounded-xl overflow-hidden bg-[#2A364F]">
                    <img src="<?php echo get_template_path(); ?>/images/thumb-placeholder3.png" 
                         data-src="<?php echo get_small_thumb($_game) ?>" 
                         class="lazyload w-full h-full object-cover"
                         alt="<?php echo esc_string($_game->title) ?>">
                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-violet-900/90 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300">
                        <div class="absolute bottom-0 left-0 right-0 p-4">
                            <h3 class="text-white font-medium line-clamp-2">
                                <?php echo esc_string($_game->title) ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
}

function wgt_list_games_vertical($type, $amount) {
    $data = fetch_games_by_type($type, $amount, 0, false);
    $games = $data['results'];
    ?>
    <div class="space-y-4">
        <?php foreach ($games as $_game):
            $category = $_game->category;
            $categories = explode(",", $category);
            foreach ($categories as $key => $cat) {
                $categories[$key] = _t($cat);
            }
            $category = implode(", ", $categories);
        ?>
            <a href="<?php echo get_permalink('game', $_game->slug) ?>" 
               class="block group">
                <div class="flex gap-4 p-3 rounded-xl bg-[#2A364F] hover:bg-violet-600/20 transition-colors">
                    <!-- Thumbnail -->
                    <div class="w-20 h-20 flex-shrink-0">
                        <div class="relative aspect-square rounded-lg overflow-hidden bg-[#1E2A45]">
                            <img src="<?php echo get_template_path(); ?>/images/thumb-placeholder3.png" 
                                 data-src="<?php echo get_small_thumb($_game) ?>" 
                                 class="lazyload w-full h-full object-cover"
                                 alt="<?php echo esc_string($_game->title) ?>">
                        </div>
                    </div>
                    <!-- Game Info -->
                    <div class="flex-grow min-w-0">
                        <h3 class="text-white font-medium mb-1 truncate group-hover:text-violet-300 transition-colors">
                            <?php echo esc_string($_game->title) ?>
                        </h3>
                        <p class="text-gray-400 text-sm truncate">
                            <?php echo esc_string($category) ?>
                        </p>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
}

register_sidebar(array(
	'name' => 'Head',
	'id' => 'head',
	'description' => 'HTML element before &#x3C;/head&#x3E;',
));

register_sidebar(array(
	'name' => 'Sidebar 1',
	'id' => 'sidebar-1',
	'description' => 'Right sidebar',
));

register_sidebar(array(
	'name' => 'Footer 1',
	'id' => 'footer-1',
	'description' => 'Footer 1',
));

register_sidebar(array(
	'name' => 'Footer 2',
	'id' => 'footer-2',
	'description' => 'Footer 2',
));

register_sidebar(array(
	'name' => 'Footer 3',
	'id' => 'footer-3',
	'description' => 'Footer 3',
));

register_sidebar(array(
	'name' => 'Footer 4',
	'id' => 'footer-4',
	'description' => 'Footer 4',
));

register_sidebar(array(
	'name' => 'Top Content',
	'id' => 'top-content',
	'description' => 'Above main content element. Recommended for Ad banner placement.',
));

register_sidebar(array(
	'name' => 'Bottom Content',
	'id' => 'bottom-content',
	'description' => 'Under main content element. Recommended for Ad banner placement.',
));

register_sidebar(array(
	'name' => 'Homepage Bottom',
	'id' => 'homepage-bottom',
	'description' => 'Bottom content on homepage. Can be used to show site description or explaining about your site.',
));

register_sidebar(array(
	'name' => 'Footer Copyright',
	'id' => 'footer-copyright',
	'description' => 'Copyright section.',
));

class Widget_Game_List extends Widget {
	function __construct() {
 		$this->name = 'Game List';
 		$this->id_base = 'game-list';
 		$this->description = 'Show game list ( Grid ). Is recommended to put this on sidebar.';
	}
	public function widget( $instance, $args = array() ){
		$label = isset($instance['label']) ? $instance['label'] : '';
		$class = isset($instance['class']) ? $instance['class'] : 'widget';
		$type = isset($instance['type']) ? $instance['type'] : 'new';
		$amount = isset($instance['amount']) ? $instance['amount'] : 9;
		$layout = isset($instance['layout']) ? $instance['layout'] : 'grid';

		echo '<div class="'.$class.' widget-game-list">';

		if($label != ''){
			echo '<h3 class="widget-title">'._t($label).'</h3>';
		}
		if($layout == 'grid'){
			wgt_list_games_grid($type, (int)$amount);
		} else if($layout == 'vertical'){
			wgt_list_games_vertical($type, (int)$amount);
		}
		echo '</div>';
	}

	public function form( $instance = array() ){

		if(!isset( $instance['label'] )){
			$instance['label'] = '';
		}
		if(!isset( $instance['type'] )){
			$instance['type'] = 'new';
		}
		if(!isset( $instance['amount'] )){
			$instance['amount'] = 9;
		}
		if(!isset( $instance['class'] )){
			$instance['class'] = 'widget';
		}
		if(!isset( $instance['layout'] )){
			$instance['layout'] = 'grid'; // vertical, grid
		}
		?>
		<div class="form-group">
			<label><?php _e('Widget label/title (optional)') ?>:</label>
			<input type="text" class="form-control" name="label" placeholder="NEW GAMES" value="<?php echo $instance['label'] ?>">
		</div>
		<div class="form-group">
			<label><?php _e('Sort game list by') ?>:</label>
			<select class="form-control" name="type">
				<?php

				$opts = array(
					'new' => 'New',
					'popular' => 'Popular',
					'random' => 'Random',
					'likes' => 'Likes',
					'trending' => 'Trending'
				);

				foreach ($opts as $key => $value) {
					$selected = '';
					if($key == $instance['type']){
						$selected = 'selected';
					}
					echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<label><?php _e('Amount') ?>:</label>
			<input type="number" class="form-control" name="amount" placeholder="9" min="1" value="<?php echo $instance['amount'] ?>">
		</div>
		<div class="mb-3">
			<label class="form-label"><?php _e('Layout') ?>:</label>
			<select name="layout" class="form-control">
				<option value="vertical" <?php echo $instance['layout'] == 'vertical' ? 'selected' : '' ?>>Vertical</option>
				<option value="grid" <?php echo $instance['layout'] == 'grid' ? 'selected' : '' ?>>Grid</option>
			</select>
		</div>
		<div class="form-group">
			<label><?php _e('Div class (Optional)') ?>:</label>
			<input type="text" class="form-control" name="class" placeholder="widget" value="<?php echo $instance['class'] ?>">
		</div>
		<?php
	}
}

register_widget( 'Widget_Game_List' );

if(file_exists(ABSPATH . TEMPLATE_PATH . '/includes/custom.php')){
	include(ABSPATH . TEMPLATE_PATH . '/includes/custom.php');
}

?>