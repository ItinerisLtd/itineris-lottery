<?php
declare(strict_types=1);

namespace Itineris\Lottery\Taxonomies;

use Itineris\Lottery\PostTypes\Result;

abstract class AbstractTaxonomy
{
    public const TAXONOMY = 'TODO: Define taxonomy in child class';

    public static function register(): void
    {
        register_taxonomy(static::TAXONOMY, [Result::POST_TYPE], [
            'labels' => [
                'name' => static::getName(),
                'singular_name' => static::getSingularName(),
            ],
            'public' => true,
            'publicly_queryable' => false,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'show_in_quick_edit' => false,
            'show_admin_column' => true,
            'query_var' => false,
            'rewrite' => false,
        ]);

        register_taxonomy_for_object_type(static::TAXONOMY, Result::POST_TYPE);
    }

    abstract protected static function getName(): string;

    abstract protected static function getSingularName(): string;
}
