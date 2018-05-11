<?php
declare(strict_types=1);

namespace Itineris\Lottery\Entities;

use WP_Term;

class TermFactory
{
    public static function makeByWpTerm(WP_Term $wpTerm): AbstractTerm
    {
        return self::make(
            (string) $wpTerm->taxonomy,
            (int) $wpTerm->term_id,
            (string) $wpTerm->name
        );
    }

    public static function make(string $taxonomy, int $id, string $name): AbstractTerm
    {
        $taxonomies = self::getTaxonomies();

        if (! array_key_exists($taxonomy, $taxonomies)) {
            wp_die(__METHOD__ . '' . esc_html($taxonomy) . ' is not supported.');
        }

        return new $taxonomies[$taxonomy]($id, $name);
    }

    private static function getTaxonomies(): array
    {
        return [
            Draw::getTaxonomy() => Draw::class,
            Prize::getTaxonomy() => Prize::class,
            Ticket::getTaxonomy() => Ticket::class,
        ];
    }
}
