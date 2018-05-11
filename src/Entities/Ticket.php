<?php
declare(strict_types=1);

namespace Itineris\Lottery\Entities;

class Ticket extends AbstractTerm
{
    public static function getTaxonomy(): string
    {
        return \Itineris\Lottery\Taxonomies\Ticket::TAXONOMY;
    }
}
