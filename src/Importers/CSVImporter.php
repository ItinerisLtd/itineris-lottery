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
                'winner' => $winner,
            ] = $record;

            if (empty($draw) && empty($prize) && empty($ticket)) {
                $this->counter->increaseIgnored();
                continue;
            }

            /**
             * Default winner field value
             */
            if (empty($winner)) {
                $winner = __('Anonymous', 'itineris-lottery');
            }

            $this->resultRepo->findOrCreate($draw, $prize, $ticket, $winner);
            $this->counter->increaseSuccessful();
        }
    }

    public function getCounter(): Counter
    {
        return $this->counter;
    }
}
