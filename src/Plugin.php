<?php
declare(strict_types=1);

namespace Itineris\Lottery;

use Itineris\Lottery\Admin\AddNewPage;
use Itineris\Lottery\Admin\ImporterPage;
use Itineris\Lottery\Admin\UploadMimes;
use Itineris\Lottery\CSV\Transformers\FourColumnTransformer;
use Itineris\Lottery\PostTypes\Result;
use Itineris\Lottery\Taxonomies\Draw;
use Itineris\Lottery\Taxonomies\Prize;
use Itineris\Lottery\Taxonomies\Ticket;
use Itineris\Lottery\Taxonomies\Winner;

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
        add_action('init', [Winner::class, 'register']);

        add_action('admin_menu', [AddNewPage::class, 'removeSubmenuPage']);
        add_action('admin_init', [ImporterPage::class, 'registerSettings']);
        add_action('admin_menu', [ImporterPage::class, 'addSubmenuPage']);
        // Do not save importer page options.
        add_filter('pre_update_option_' . ImporterPage::CSV_FORMAT_OPTION_ID, '__return_false', 1000);
        add_filter('pre_update_option_' . ImporterPage::CSV_FILE_OPTION_ID, '__return_false', 1000);
        add_action('pre_update_option_' . ImporterPage::CSV_FILE_OPTION_ID, [ImporterPage::class, 'handleFormSubmit']);
        add_filter('upload_mimes', [UploadMimes::class, 'allowCSV']);

        add_action(self::PREFIX . 'register_transformers', [FourColumnTransformer::class, 'register']);
    }
}
