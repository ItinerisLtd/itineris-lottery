<?php

declare(strict_types=1);

namespace Itineris\Lottery\CSV;

use Itineris\Lottery\CSV\Transformers\TransformerInterface;
use Itineris\Lottery\Repositories\Factory as RepoFactory;

class Factory
{
    public static function make(TransformerInterface $transformer): Importer
    {
        [
            'resultRepo' => $resultRepo,
        ] = RepoFactory::make();

        $counter = new Counter();

        return new Importer($transformer, $resultRepo, $counter);
    }
}
