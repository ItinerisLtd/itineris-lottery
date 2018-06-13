<?php
declare(strict_types=1);

namespace Itineris\Lottery\Importers;

use Itineris\Lottery\Repositories\ResultRepo;
use League\Csv\Reader;

class CSVImporter
{
    private $path;
    private $resultRepo;
    private $counter;

    public function __construct(string $path, ResultRepo $resultRepo, Counter $counter)
    {
        $this->path = $path;
        $this->resultRepo = $resultRepo;
        $this->counter = $counter;
    }

    public function import(): void
    {
        $reader = Reader::createFromPath($this->path);
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
}
