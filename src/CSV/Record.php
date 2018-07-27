<?php
declare(strict_types=1);

namespace Itineris\Lottery\CSV;

use InvalidArgumentException;
use Itineris\Lottery\Repositories\ResultRepo;

final class Record
{
    private $drawName;
    private $prizeName;
    private $ticketName;
    private $winnerName;

    public function __construct(string $drawName, string $prizeName, string $ticketName, string $winnerName)
    {
        $this->checkStringFormat($drawName);
        $this->checkStringFormat($prizeName);
        $this->checkStringFormat($ticketName);
        $this->checkStringFormat($winnerName);

        $this->drawName = $drawName;
        $this->prizeName = $prizeName;
        $this->ticketName = $ticketName;
        $this->winnerName = $winnerName;
    }

    private function checkStringFormat(string $str): void
    {
        if (empty($str)) {
            throw new InvalidArgumentException(
                __CLASS__ . '::__construct cannot accept empty arguments'
            );
        }

        if (strpos($str, ResultRepo::TITLE_SEPARATOR) !== false) {
            throw new InvalidArgumentException(
                __CLASS__ . '::__construct arguments cannot contain ' . ResultRepo::TITLE_SEPARATOR
            );
        }
    }

    public function getDrawName(): string
    {
        return $this->drawName;
    }

    public function getPrizeName(): string
    {
        return $this->prizeName;
    }

    public function getTicketName(): string
    {
        return $this->ticketName;
    }

    public function getWinnerName(): string
    {
        return $this->winnerName;
    }
}
