<?php
declare(strict_types=1);

namespace Itineris\Lottery\Admin;

use AdamWathan\Form\FormBuilder;
use Itineris\Lottery\CSV\Counter;
use Itineris\Lottery\CSV\Encoder;
use Itineris\Lottery\CSV\Factory;
use Itineris\Lottery\CSV\TransformerCollection;
use Itineris\Lottery\CSV\Transformers\TransformerInterface;
use Itineris\Lottery\Plugin;
use Itineris\Lottery\PostTypes\Result;
use TypistTech\WPBetterSettings\Builder;
use TypistTech\WPBetterSettings\Field;
use TypistTech\WPBetterSettings\Registrar;
use TypistTech\WPBetterSettings\Section;
use TypistTech\WPOptionStore\Factory as OptionStoreFactory;

class ImporterPage
{
    private const SLUG = Plugin::PREFIX . 'csv_importer';
    public const CSV_FORMAT_OPTION_ID = Plugin::PREFIX . 'csv_format';
    public const CSV_FILE_OPTION_ID = Plugin::PREFIX . 'csv_file';

    public static function registerSettings(): void
    {
        $section = new Section(
            'csv_importer',
            __('Import results via CSV file', 'itineris-lottery')
        );

        $builder = new Builder(
            OptionStoreFactory::build()
        );

        // Because wp-better-settings does not support file field yet.
        $formBuilder = new FormBuilder();

        $transformerCollection = TransformerCollection::make();

        $section->add(
            $builder->select(
                self::CSV_FORMAT_OPTION_ID,
                __('CSV Format', 'itineris-lottery'),
                $transformerCollection->toSelect()
            ),
            new Field(
                self::CSV_FILE_OPTION_ID,
                __('CSV File', 'itineris-lottery'),
                $formBuilder->file(self::CSV_FILE_OPTION_ID)
                            ->id(self::CSV_FILE_OPTION_ID),
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
            'publish_' . Result::POST_TYPE,
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

    public static function handleFormSubmit(): void
    {
        [
            'error' => $message,
            'success' => $formatSuccess,
            'transformer' => $transformer,
        ] = self::handleFormat();

        if (! $formatSuccess) {
            add_settings_error(self::SLUG, esc_attr('settings_updated'), $message, 'error');

            return;
        }

        [
            'error' => $message,
            'success' => $uploadSuccess,
            'path' => $csvFilePath,
        ] = self::handleUpload();

        if ($uploadSuccess) {
            $message = self::encodeAndImport($csvFilePath, $transformer);
        }

        add_settings_error(
            self::SLUG,
            esc_attr('settings_updated'),
            $message,
            $uploadSuccess ? 'updated' : 'error'
        );
    }

    private static function handleFormat(): array
    {
        $transformerCollection = TransformerCollection::make();

        if (empty($_POST[self::CSV_FORMAT_OPTION_ID])) { // Input var okay.
            return [
                'success' => false,
                'error' => 'CSV format not given.',
                'transformer' => null,
            ];
        }

        $csvFormat = sanitize_key($_POST[self::CSV_FORMAT_OPTION_ID]); // Input var okay.

        if (! $transformerCollection->has($csvFormat)) {
            return [
                'success' => false,
                'error' => 'Unknown CSV format: ' . $csvFormat . '.',
                'transformer' => null,
            ];
        }

        return [
            'success' => true,
            'error' => null,
            'transformer' => $transformerCollection->get($csvFormat),
        ];
    }

    private static function handleUpload(): array
    {
        if (empty($_FILES)) { // Input var okay.
            return [
                'success' => false,
                'error' => esc_html__('Failed to accept CSV file. $_FILES is empty.', 'itineris-lottery'),
                'path' => null,
            ];
        }

        $files = wp_unslash($_FILES); // Input var okay.
        $file = $files[self::CSV_FILE_OPTION_ID];

        if (empty($file)) {
            return [
                'success' => false,
                'error' => esc_html__(
                    'Failed to accept CSV file. $files[self::CSV_FILE_OPTION_ID] is empty.',
                    'itineris-lottery'
                ),
                'path' => null,
            ];
        }

        $moveFile = wp_handle_upload($file, ['test_form' => false]);

        if (! is_array($moveFile) || isset($moveFile['error'])) {
            return [
                'success' => false,
                'error' => $moveFile['error'],
                'path' => null,
            ];
        }

        return [
            'success' => true,
            'error' => null,
            'path' => $moveFile['file'],
        ];
    }

    private static function encodeAndImport($csvPath, TransformerInterface $transformer): string
    {
        $isEncoded = Encoder::forceUFT8($csvPath);

        $csvImporter = Factory::make($transformer);
        $csvImporter->import($csvPath);

        return self::getMessage(
            $isEncoded,
            $csvImporter->getCounter()
        );
    }

    private static function getMessage(bool $isEncoded, Counter $counter): string
    {
        $message = esc_html__(
            'Warning: Unable to convert the CSV file into UTF-8 encoding. Process anyway.',
            'itineris-lottery'
        );
        if ($isEncoded) {
            $message = esc_html__(
                'Success: The CSV files was forced to UTF-8 encoding.',
                'itineris-lottery'
            );
        }

        // Translators: %1$d is the number of successful imported rows.
        $successfulCountMessageFormat = esc_html__('Success: %1$d row(s) imported.', 'itineris-lottery');
        $message .= '<br />';
        $message .= sprintf(
            $successfulCountMessageFormat,
            $counter->getSuccessful()
        );

        if ($counter->getIgnored() > 0) {
            // Translators: %1$d is the number of ignored imported rows.
            $ignoredCountMessageFormat = esc_html__('Warning: %1$d row(s) ignored.', 'itineris-lottery');
            $message .= '<br />';
            $message .= sprintf(
                $ignoredCountMessageFormat,
                $counter->getIgnored()
            );
        }

        return $message;
    }
}
