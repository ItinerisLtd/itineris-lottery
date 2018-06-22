<?php
declare(strict_types=1);

namespace Itineris\Lottery\Entities;

class Result
{
    private $draw;
    private $prize;
    private $ticket;
    private $winner;

    public function __construct(int $id, Draw $draw, Prize $prize, Ticket $ticket, Winner $winner)
    {
        $this->id = $id;
        $this->draw = $draw;
        $this->prize = $prize;
        $this->ticket = $ticket;
        $this->winner = $winner;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDrawName(): string
    {
        return $this->getDraw()->getName();
    }

    public function getDraw(): Draw
    {
        return $this->draw;
    }

    public function getDrawId(): int
    {
        return $this->getDraw()->getId();
    }

    public function getPrizeName(): string
    {
        return $this->getPrize()->getName();
    }

    public function getPrize(): Prize
    {
        return $this->prize;
    }

    public function getPrizeId(): int
    {
        return $this->getPrize()->getId();
    }

    public function getTicketName(): string
    {
        return $this->getTicket()->getName();
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function getTicketId(): int
    {
        return $this->getTicket()->getId();
    }

    public function getWinner(): Winner
    {
        return $this->winner;
    }

    public function getWinnerId(): int
    {
        return $this->getWinner()->getId();
    }

    public function getWinnerName(): string
    {
        return $this->getWinner()->getName();
    }
}
