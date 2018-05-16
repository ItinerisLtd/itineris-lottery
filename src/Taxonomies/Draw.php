<?php
declare(strict_types=1);

namespace Itineris\Lottery\Taxonomies;

use Itineris\Lottery\Plugin;
use Itineris\Lottery\PostTypes\Result;

class Draw
{
    public const TAXONOMY = Plugin::PREFIX . 'draw';

    public static function register(): void
    {
        register_taxonomy(static::TAXONOMY, [Result::POST_TYPE], [
            'labels' => [
                'name' => static::getName(),
                'singular_name' => static::getSingularName(),
            ],
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => false,
            'rewrite' => false,
            'capabilities' => [
                'delete_terms' => 'manage_draws',
            ],
        ]);

        register_taxonomy_for_object_type(static::TAXONOMY, Result::POST_TYPE);
    }

    protected static function getName(): string
    {
        return __('Draws', 'itineris-lottery');
    }

    protected static function getSingularName(): string
    {
        return __('Draw', 'itineris-lottery');
    }
}
