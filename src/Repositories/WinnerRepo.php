<?php
declare(strict_types=1);

namespace Itineris\Lottery\Repositories;

class WinnerRepo extends AbstractTermRepo
{
    protected function getTaxonomy(): string
    {
        return \Itineris\Lottery\Taxonomies\Winner::TAXONOMY;
    }
}
