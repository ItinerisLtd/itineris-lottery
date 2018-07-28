<?php
declare(strict_types=1);

namespace Itineris\Lottery\CSV;

use Itineris\Lottery\CSV\Transformers\TransformerInterface;
use Itineris\Lottery\Repositories\ResultRepo;
use League\Csv\Reader;

class Importer
{
    private $transformer;
    private $resultRepo;
    private $counter;

    public function __construct(TransformerInterface $transformer, ResultRepo $resultRepo, Counter $counter)
    {
        $this->transformer = $transformer;
        $this->resultRepo = $resultRepo;
        $this->counter = $counter;
    }

    public function import(string $path): void
    {
        $reader = Reader::createFromPath($path);
        $reader->setHeaderOffset(0);

        $rows = $reader->getRecords();
        foreach ($rows as $row) {
            $row = array_change_key_case($row, CASE_LOWER);

            $record = $this->transformer->toRecord($row);

            if (null === $record) {
                $this->counter->increaseIgnored();
                continue;
            }

            $this->resultRepo->findOrCreate(
                $record->getDrawName(),
                $record->getPrizeName(),
                $record->getTicketName(),
                $record->getWinnerName()
            );

            $this->counter->increaseSuccessful();
        }
    }

    public function getCounter(): Counter
    {
        return $this->counter;
    }
}
