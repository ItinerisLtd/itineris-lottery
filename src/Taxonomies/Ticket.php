<?php
declare(strict_types=1);

namespace Itineris\Lottery\Taxonomies;

use Itineris\Lottery\Plugin;

class Ticket extends AbstractTaxonomy
{
    public const TAXONOMY = Plugin::PREFIX . 'ticket';

    protected static function getName(): string
    {
        return __('Tickets', 'itineris-lottery');
    }

    protected static function getSingularName(): string
    {
        return __('Ticket', 'itineris-lottery');
    }
}
