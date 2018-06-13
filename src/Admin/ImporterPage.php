<?php
declare(strict_types=1);

namespace Itineris\Lottery\Admin;

use AdamWathan\Form\FormBuilder;
use Itineris\Lottery\Importers\Counter;
use Itineris\Lottery\Importers\CSVImporter;
use Itineris\Lottery\Importers\Factory;
use Itineris\Lottery\Plugin;
use Itineris\Lottery\PostTypes\Result;
use TypistTech\WPBetterSettings\Field;
use TypistTech\WPBetterSettings\Registrar;
use TypistTech\WPBetterSettings\Section;

class ImporterPage
{
    private const SLUG = Plugin::PREFIX . 'csv_importer';
    public const CSV_FILE_OPTION_ID = Plugin::PREFIX . 'csv_file';

    public static function registerSettings(): void
    {
        $section = new Section(
            'csv_importer',
            __('Import results via CSV file', 'itineris-lottery')
        );

        $formBuilder = new FormBuilder();
        $fileField = $formBuilder->file(self::CSV_FILE_OPTION_ID);
        $fileField->id(self::CSV_FILE_OPTION_ID);

        $section->add(
            new Field(
                self::CSV_FILE_OPTION_ID,
                __('CSV File', 'itineris-lottery'),
                $fileField,
                []
            )
        );
        $registrar = new Registrar(self::SLUG);
        $registrar->add($section);
        $registrar->run();
    }

    public static function addSubmenuPage(): void
    {
        add_submenu_page(
            'edit.php?post_type=' . Result::POST_TYPE,
            __('CSV Importer', 'itineris-lottery'),
            __('CSV Importer', 'itineris-lottery'),
            'manage_options',
            self::SLUG,
            function () {
                echo '<div class="wrap">';
                settings_errors();
                echo '<h1>' . esc_html(get_admin_page_title()) . '</h1>';
                echo '<form action="options.php" method="post" enctype="multipart/form-data">';
                settings_fields(self::SLUG);
                do_settings_sections(self::SLUG);
                submit_button();
                echo '</form>';
                echo '</div>';
            }
        );
    }

    public static function import($_input, $oldValue)
    {
        if (empty($_FILES)) { // Input var okay.
            return $oldValue;
        }

        $files = wp_unslash($_FILES); // Input var okay.
        $file = $files[self::CSV_FILE_OPTION_ID];

        if (empty($file)) {
            return $oldValue;
        }

        $moveFile = wp_handle_upload($file, ['test_form' => false]);

        if (! is_array($moveFile) || isset($moveFile['error'])) {
            add_settings_error(
                self::SLUG,
                esc_attr('settings_updated'),
                $moveFile['error'],
                'error'
            );

            return $oldValue;
        }

        $csvPath = $moveFile['file'];

        [
            'csvImporter' => $csvImporter,
            'counter' => $counter,
        ] = Factory::make($csvPath);

        /* @var CSVImporter $csvImporter The CSV importer. */
        $csvImporter->import();

        /* @var Counter $counter The import counter. */
        add_settings_error(
            self::CSV_FILE_OPTION_ID,
            self::CSV_FILE_OPTION_ID,
            self::getMessage($counter),
            'updated'
        );

        return $oldValue;
    }

    private static function getMessage(Counter $counter): string
    {
        $message = sprintf(
            // Translators: %1$d is the number of successful imported rows.
            __('%1$d row(s) imported.', 'itineris-lottery'),
            $counter->getSuccessful()
        );

        if ($counter->getIgnored() > 0) {
            $message .= '<br />';
            $message .= sprintf(
                // Translators: %1$d is the number of ignored imported rows.
                __('%1$d row(s) ignored because they are totally empty.', 'itineris-lottery'),
                $counter->getIgnored()
            );
        }

        return $message;
    }
}
