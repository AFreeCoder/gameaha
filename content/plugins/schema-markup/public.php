<?php
/**
 * This file interacts with the frontend (theme or visitor pages) of the CMS.
 * It is used to modify or insert dynamic content into the public-facing parts of the site.
 */

function _safe_substr($str, $start, $length = null) {
    if (function_exists('mb_substr')) {
        return mb_substr($str, $start, $length);
    } else {
        return substr($str, $start, $length);
    }
}

// Hook into 'head_bottom' to add content to the head section of the theme
add_to_hook('head_bottom', function() {
    global $game;
    global $base_taxonomy;

    // Dynamically set the plugin slug by getting the plugin folder name
    $plugin_slug = basename(dirname(__FILE__)); // Example: 'sample-plugin'

    // Check if we're on a game page
    if($base_taxonomy == 'game' && isset($game)){

        // Retrieve plugin preferences
        $author_enabled = get_plugin_pref_bool($plugin_slug, 'author_enabled', false);
        $author_name = get_plugin_pref($plugin_slug, 'author_name', 'John Doe');
        $author_type = get_plugin_pref($plugin_slug, 'author_type', 'Person');

        $publisher_enabled = get_plugin_pref_bool($plugin_slug, 'publisher_enabled', false);
        $publisher_name = get_plugin_pref($plugin_slug, 'publisher_name', 'CloudArcade Ltd.');

        $offers_enabled = get_plugin_pref_bool($plugin_slug, 'offers_enabled', true);
        $game_url = get_permalink('game', $game->slug);

        // Prepare game data to replace placeholders
        $game_data = [
            'game_name' => $game->title,
            'game_description' => _safe_substr(strip_tags($game->description), 0, 300),
            'game_image' => $game->thumb_1,
            'game_genre' => explode(',', $game->category)[0],
            'game_platform' => 'HTML5',
            'rating_value' => ($game->upvote + $game->downvote > 0) ? round(($game->upvote / ($game->upvote + $game->downvote)) * 5, 1) : 0,
            'rating_count' => ((int)$game->upvote + (int)$game->downvote),
            'user_interaction_count' => $game->views,
        ];

        // Start building the schema
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "VideoGame",
            "name" => $game_data['game_name'],
            "description" => $game_data['game_description'],
            "image" => $game_data['game_image'],
            "genre" => $game_data['game_genre'],
            "gamePlatform" => $game_data['game_platform'],
            "interactionStatistic" => [
                "@type" => "InteractionCounter",
                "interactionType" => [
                    "@type" => "http://schema.org/PlayAction"
                ],
                "userInteractionCount" => $game_data['user_interaction_count']
            ],
            "monetization" => [
                "@type" => "MonetizationPolicy",
                "policyType" => "AdsSupported"
            ]
        ];

        // Have ratings
        if($game_data['rating_count'] > 0){
            if($game_data['rating_value'] <= 0){
                $game_data['rating_value'] = 1;
            }
            $schema["aggregateRating"] = [
                "@type" => "AggregateRating",
                "ratingValue" => $game_data['rating_value'],
                "ratingCount" => $game_data['rating_count'],
                "bestRating" => "5",
                "worstRating" => "1"
            ];
        }

        // Add author if enabled
        if ($author_enabled) {
            $schema["author"] = [
                "@type" => $author_type,
                "name" => $author_name
            ];
        }

        // Add publisher if enabled
        if ($publisher_enabled) {
            $schema["publisher"] = [
                "@type" => "Organization",
                "name" => $publisher_name
            ];
        }

        // Add offers if enabled
        if ($offers_enabled) {
            $schema["offers"] = [
                "@type" => "Offer",
                "price" => "0.00", // Always free
                "priceCurrency" => "USD",
                "url" => $game_url
            ];
        }

        // Output the dynamically generated JSON-LD schema
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
});
?>