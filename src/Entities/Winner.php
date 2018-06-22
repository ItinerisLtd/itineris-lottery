<?php
declare(strict_types=1);

namespace Itineris\Lottery\Entities;

class Winner extends AbstractTerm
{
    public static function getTaxonomy(): string
    {
        return \Itineris\Lottery\Taxonomies\Winner::TAXONOMY;
    }
}
