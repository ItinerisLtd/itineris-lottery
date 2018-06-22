<?php
declare(strict_types=1);

namespace Itineris\Lottery\Taxonomies;

use Itineris\Lottery\Plugin;

class Winner extends AbstractTaxonomy
{
    public const TAXONOMY = Plugin::PREFIX . 'winner';

    protected static function getName(): string
    {
        return __('Winners', 'itineris-lottery');
    }

    protected static function getSingularName(): string
    {
        return __('Winner', 'itineris-lottery');
    }
}
