<?php
declare(strict_types=1);

namespace Itineris\Lottery\Taxonomies;

use Itineris\Lottery\Lottery;

class Draw extends AbstractTaxonomy
{
    public const TAXONOMY = Lottery::PREFIX . 'draw';

    protected static function getName(): string
    {
        return __('Draws', 'lottery');
    }

    protected static function getSingularName(): string
    {
        return __('Draw', 'lottery');
    }
}
