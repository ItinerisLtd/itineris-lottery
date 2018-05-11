<?php
declare(strict_types=1);

namespace Itineris\Lottery\PostTypes;

use Itineris\Lottery\Plugin;

class Result
{
    public const POST_TYPE = Plugin::PREFIX . 'result';

    public static function register(): void
    {
        register_post_type(self::POST_TYPE, [
            'labels' => [
                'name' => __('Results', 'itineris-lottery'),
                'singular_name' => __('Result', 'itineris-lottery'),
            ],
            'public' => defined('WP_DEBUG') && WP_DEBUG,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => false,
            'supports' => ['title'],
            'rewrite' => false,
            'query_var' => false,
            'can_export' => false,
        ]);
    }
}
