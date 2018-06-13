<?php
declare(strict_types=1);

namespace Itineris\Lottery\Importers;

use Itineris\Lottery\Repositories\ResultRepo;
use League\Csv\Reader;

class CSVImporter
{
    private $resultRepo;
    private $counter;

    public function __construct(ResultRepo $resultRepo, Counter $counter)
    {
        $this->resultRepo = $resultRepo;
        $this->counter = $counter;
    }

    public function import(string $path): void
    {
        $reader = Reader::createFromPath($path);
        $reader->setHeaderOffset(0);

        $records = $reader->getRecords();
        foreach ($records as $record) {
            [
                'draw' => $draw,
                'prize' => $prize,
                'ticket' => $ticket,
            ] = $record;

            if (empty($draw) && empty($prize) && empty($ticket)) {
                $this->counter->increaseIgnored();
                continue;
            }

            $this->resultRepo->findOrCreate($draw, $prize, $ticket);
            $this->counter->increaseSuccessful();
        }
    }

    public function getCounter(): Counter
    {
        return $this->counter;
    }
}
