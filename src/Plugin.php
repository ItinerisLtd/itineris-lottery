<?php
declare(strict_types=1);

namespace Itineris\Lottery;

use Itineris\Lottery\Admin\AddNewPage;
use Itineris\Lottery\Admin\ImporterPage;
use Itineris\Lottery\Admin\UploadMimes;
use Itineris\Lottery\PostTypes\Result;
use Itineris\Lottery\Taxonomies\Draw;
use Itineris\Lottery\Taxonomies\Prize;
use Itineris\Lottery\Taxonomies\Ticket;

class Plugin
{
    // i stands for itineris.
    // Using abbreviation because of post type name 20-char limit.
    public const PREFIX = 'i_lottery_';

    public function run(): void
    {
        add_action('init', [Result::class, 'register']);
        add_filter('manage_edit-' . Result::POST_TYPE . '_columns', [Result::class, 'hideTitleColumn']);

        add_action('init', [Draw::class, 'register']);
        add_action('init', [Prize::class, 'register']);
        add_action('init', [Ticket::class, 'register']);

        add_action('admin_menu', [AddNewPage::class, 'removeSubmenuPage']);
        add_action('admin_init', [ImporterPage::class, 'registerSettings']);
        add_action('admin_menu', [ImporterPage::class, 'addSubmenuPage']);
        add_filter('pre_update_option_' . ImporterPage::CSV_FILE_OPTION_ID, [ImporterPage::class, 'import'], 10, 2);
        add_filter('upload_mimes', [UploadMimes::class, 'allowCSV']);
    }
}
