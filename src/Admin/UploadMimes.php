<?php
declare(strict_types=1);

namespace Itineris\Lottery\Admin;

class UploadMimes
{
    public static function allowCSV(array $mimes): array
    {
        $mimes['csv'] = 'text/csv';

        return $mimes;
    }
}
