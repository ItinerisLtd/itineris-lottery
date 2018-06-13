<?php

declare(strict_types=1);

namespace Itineris\Lottery\Importers;

use Itineris\Lottery\Repositories\Factory as RepoFactory;

class Factory
{
    public static function make(string $csvPath): array
    {
        [
            'resultRepo' => $resultRepo,
        ] = RepoFactory::make();

        $counter = new Counter();

        $csvImporter = new CSVImporter($csvPath, $resultRepo, $counter);

        return [
            'csvImporter' => $csvImporter,
            'counter' => $counter,
            'resultRepo' => $resultRepo,
        ];
    }
}
