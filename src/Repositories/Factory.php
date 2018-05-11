<?php
declare(strict_types=1);

namespace Itineris\Lottery\Repositories;

class Factory
{
    public static function make(): array
    {
        $drawRepo = new DrawRepo();
        $prizeRepo = new PrizeRepo();
        $ticketRepo = new TicketRepo();

        $resultRepo = new ResultRepo($drawRepo, $prizeRepo, $ticketRepo);

        return [
            'resultRepo' => $resultRepo,
            'drawRepo' => $drawRepo,
            'prizeRepo' => $prizeRepo,
            'ticketRepo' => $ticketRepo,
        ];
    }
}
