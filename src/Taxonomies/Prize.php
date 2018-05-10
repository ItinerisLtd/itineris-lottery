<?php
declare(strict_types=1);

namespace Itineris\Lottery\Taxonomies;

use Itineris\Lottery\Lottery;

class Prize extends AbstractTaxonomy
{
    public const TAXONOMY = Lottery::PREFIX . 'prize';

    protected static function getName(): string
    {
        return __('Prizes', 'lottery');
    }

    protected static function getSingularName(): string
    {
        return __('Prize', 'lottery');
    }
}
