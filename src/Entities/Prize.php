<?php
declare(strict_types=1);

namespace Itineris\Lottery\Entities;

class Prize extends AbstractTerm
{
    public static function getTaxonomy(): string
    {
        return \Itineris\Lottery\Taxonomies\Prize::TAXONOMY;
    }
}
