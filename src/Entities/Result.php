<?php
declare(strict_types=1);

namespace Itineris\Lottery\Entities;

class Result
{
    private $draw;
    private $prize;
    private $ticket;

    public function __construct(int $id, Draw $draw, Prize $prize, Ticket $ticket)
    {
        $this->id = $id;
        $this->draw = $draw;
        $this->prize = $prize;
        $this->ticket = $ticket;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDraw(): Draw
    {
        return $this->draw;
    }

    public function getDrawName(): string
    {
        return $this->getDraw()->getName();
    }

    public function getDrawId(): int
    {
        return $this->getDraw()->getId();
    }

    public function getPrize(): Prize
    {
        return $this->prize;
    }

    public function getPrizeName(): string
    {
        return $this->getPrize()->getName();
    }

    public function getPrizeId(): int
    {
        return $this->getPrize()->getId();
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function getTicketName(): string
    {
        return $this->getTicket()->getName();
    }

    public function getTicketId(): int
    {
        return $this->getTicket()->getId();
    }
}
