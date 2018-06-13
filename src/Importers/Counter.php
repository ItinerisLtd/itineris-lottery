<?php

declare(strict_types=1);

namespace Itineris\Lottery\Importers;

class Counter
{
    private $successful = 0;
    private $ignored = 0;

    public function increaseSuccessful(): void
    {
        $this->successful++;
    }

    public function increaseIgnored(): void
    {
        $this->ignored++;
    }

    public function getSuccessful(): int
    {
        return $this->successful;
    }

    public function getIgnored(): int
    {
        return $this->ignored;
    }
}
