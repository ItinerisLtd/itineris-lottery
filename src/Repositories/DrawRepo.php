<?php
declare(strict_types=1);

namespace Itineris\Lottery\Repositories;

class DrawRepo extends AbstractTermRepo
{
    protected function getTaxonomy(): string
    {
        return \Itineris\Lottery\Taxonomies\Draw::TAXONOMY;
    }
}
