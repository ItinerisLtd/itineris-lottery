<?php
declare(strict_types=1);

namespace Itineris\Lottery\Importers;

use Itineris\Lottery\Repositories\ResultRepo;
use League\Csv\Reader;

class CSVImporter
{
    private $path;
    private $resultRepo;

    public function __construct(string $path, ResultRepo $resultRepo)
    {
        $this->path = $path;
        $this->resultRepo = $resultRepo;
    }

    public function import(): void
    {
        $reader = Reader::createFromPath($this->path);
        $reader->setHeaderOffset(0);

        $records = $reader->getRecords();
        foreach ($records as $offset => $record) {
            $this->resultRepo->findOrCreate(
                $record['draw'],
                $record['prize'],
                $record['ticket']
            );
        }
    }
}
