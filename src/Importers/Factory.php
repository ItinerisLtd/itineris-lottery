<?php

declare(strict_types=1);

namespace Itineris\Lottery\Importers;

use Itineris\Lottery\Repositories\Factory as RepoFactory;

class Factory
{
    public static function make(): CSVImporter
    {
        [
            'resultRepo' => $resultRepo,
        ] = RepoFactory::make();

        $counter = new Counter();

        return new CSVImporter($resultRepo, $counter);
    }
}
