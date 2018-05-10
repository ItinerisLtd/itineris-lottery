<?php
declare(strict_types=1);

namespace Itineris\Lottery\Taxonomies;

use Itineris\Lottery\Plugin;

class Prize extends AbstractTaxonomy
{
    public const TAXONOMY = Plugin::PREFIX . 'prize';

    protected static function getName(): string
    {
        return __('Prizes', 'itineris-lottery');
    }

    protected static function getSingularName(): string
    {
        return __('Prize', 'itineris-lottery');
    }
}
