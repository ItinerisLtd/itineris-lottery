<?php
declare(strict_types=1);

namespace Itineris\Lottery\CSV\Transformers;

use Itineris\Lottery\CSV\Record;

final class FourColumnTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function toRecord(array $row): ?Record
    {
        [
            'draw' => $draw,
            'prize' => $prize,
            'ticket' => $ticket,
            'winner' => $winner,
        ] = $row;

        // Because the CSV file may contains empty rows.
        // This is the client's fault!
        if (empty($draw) && empty($ticket) && empty($winner)) {
            return null;
        }

        if (empty($winner)) {
            $winner = __('Anonymous', 'itineris-lottery');
        }

        return new Record($draw, $prize, $ticket, $winner);
    }
}
