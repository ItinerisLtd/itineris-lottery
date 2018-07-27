<?php

declare(strict_types=1);

namespace Itineris\Lottery\CSV;

use Itineris\Lottery\Repositories\Factory as RepoFactory;

class Factory
{
    public static function make(): Importer
    {
        [
            'resultRepo' => $resultRepo,
        ] = RepoFactory::make();

        $counter = new Counter();

        return new Importer($resultRepo, $counter);
    }
}
