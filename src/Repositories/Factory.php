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
        $winnerRepo = new WinnerRepo();

        $resultRepo = new ResultRepo($drawRepo, $prizeRepo, $ticketRepo, $winnerRepo);

        return [
            'resultRepo' => $resultRepo,
            'drawRepo' => $drawRepo,
            'prizeRepo' => $prizeRepo,
            'ticketRepo' => $ticketRepo,
            'winnerRepo' => $winnerRepo,
        ];
    }
}
