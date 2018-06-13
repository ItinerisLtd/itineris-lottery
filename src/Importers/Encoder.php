<?php

declare(strict_types=1);

namespace Itineris\Lottery\Importers;

use ForceUTF8\Encoding;
use WP_Filesystem_Base;

class Encoder
{
    public static function forceUFT8(string $path): bool
    {
        /* @var WP_Filesystem_Base $wp_filesystem The WP filesystem instance. */
        global $wp_filesystem;

        if (! WP_Filesystem()) {
            return false;
        }

        $original = $wp_filesystem->get_contents($path);
        $utf8 = Encoding::fixUTF8($original, Encoding::ICONV_IGNORE);

        return $wp_filesystem->put_contents($path, $utf8);
    }
}
