<?php
declare(strict_types=1);

namespace Itineris\Lottery\Entities;

class Draw extends AbstractTerm
{
    public static function getTaxonomy(): string
    {
        return \Itineris\Lottery\Taxonomies\Draw::TAXONOMY;
    }
}
