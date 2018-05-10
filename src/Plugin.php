<?php
declare(strict_types=1);

namespace Itineris\Lottery;

use Itineris\Lottery\PostTypes\Winner;
use Itineris\Lottery\Taxonomies\Draw;
use Itineris\Lottery\Taxonomies\Prize;

class Plugin
{
    // i stands for itineris.
    // Using abbreviation because of post type name 20-char limit.
    public const PREFIX = 'i_lottery_';

    public function run(): void
    {
        add_action('init', [Winner::class, 'register']);
        add_action('init', [Draw::class, 'register']);
        add_action('init', [Prize::class, 'register']);
    }
}
