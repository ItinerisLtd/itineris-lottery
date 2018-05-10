<?php
declare(strict_types=1);

namespace Itineris\Lottery\Taxonomies;

use Itineris\Lottery\Plugin;

class Draw extends AbstractTaxonomy
{
    public const TAXONOMY = Plugin::PREFIX . 'draw';

    protected static function getName(): string
    {
        return __('Draws', 'itineris-lottery');
    }

    protected static function getSingularName(): string
    {
        return __('Draw', 'itineris-lottery');
    }
}
