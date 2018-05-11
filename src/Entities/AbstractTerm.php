<?php
declare(strict_types=1);

namespace Itineris\Lottery\Entities;

abstract class AbstractTerm
{
    private $id;
    private $name;

    abstract public static function getTaxonomy(): string;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
