<?php
declare(strict_types=1);

namespace Itineris\Lottery\CSV\Transformers;

use Itineris\Lottery\CSV\Record;

interface TransformerInterface
{
    /**
     * Transform a CSV row into a Record.
     * If the CSV row is malformed,
     *   - return null to ignore this CSV row
     *   - wp_die to stop the whole importation
     * Note that:
     *   - non UTF-8 characters are converted or stripped already
     *   - array keys are the lower-cased column headers
     *
     * @param array $row The CSV row in array form.
     *
     * @return Record|null
     */
    public function toRecord(array $row): ?Record;
}
