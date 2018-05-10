<?php
/**
 * Plugin Name:     Lottery
 * Plugin URI:      https://www.itineris.co.uk/
 * Description:     Custom post type for lottery results
 * Version:         0.1.0
 * Author:          Itineris Limited
 * Author URI:      https://www.itineris.co.uk/
 * Text Domain:     lottery
 */

declare(strict_types=1);

namespace Itineris\Lottery;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Begins execution of the plugin.
 *
 * @return void
 */
function run(): void
{
    $lottery = new Lottery();
    $lottery->run();
}

run();
