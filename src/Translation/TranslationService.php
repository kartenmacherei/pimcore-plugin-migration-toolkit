<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Translation;

use Pimcore\Model\Translation;
use Basilicom\PimcorePluginMigrationToolkit\Translation\Exception\InvalidTranslationFileFormatException;

class TranslationService
{
    /**
     * @param string $filename
     * @param string $delimiter
     *
     * @param bool $replaceExistingTranslation
     *
     * @throws InvalidTranslationFileFormatException
     */
    public function importSharedTranslationCsv(string $filename, string $delimiter, bool $replaceExistingTranslation): void
    {
        $this->validateTranslationsFile($filename, $delimiter);

        Translation::importTranslationsFromFile($filename, Translation::DOMAIN_DEFAULT, $replaceExistingTranslation);
    }

    /**
     * @param string $filename
     * @param string $delimiter
     *
     * @param bool $replaceExistingTranslation
     *
     * @throws InvalidTranslationFileFormatException
     */
    public function importAdminTranslationCsv(string $filename, string $delimiter, bool $replaceExistingTranslation): void
    {
        $this->validateTranslationsFile($filename, $delimiter);

        Translation::importTranslationsFromFile($filename, Translation::DOMAIN_ADMIN, $replaceExistingTranslation);
    }

    /**
     * @param string $translationsFilePath
     * @param string $delimiter
     *
     * @throws InvalidTranslationFileFormatException
     */
    protected function validateTranslationsFile(string $translationsFilePath, string $delimiter)
    {
        if (!file_exists($translationsFilePath) || !is_readable($translationsFilePath)) {
            throw new InvalidTranslationFileFormatException(sprintf('file "%s" does not exist or is not readable', $translationsFilePath));
        }

        $firstRow = $this->getFirstRowFromCsvFile($translationsFilePath, $delimiter);

        if (!in_array('key', $firstRow)) {
            throw new InvalidTranslationFileFormatException(sprintf('required column "%s" is missing', 'key'));
        }
    }

    /**
     * @param string $translationsFilePath
     * @param string $delimiter
     *
     * @return array
     *
     * reads the first line from the translation file
     */
    protected function getFirstRowFromCsvFile(string $translationsFilePath, string $delimiter): array
    {
        $firstRow = [];

        $fileResource = fopen($translationsFilePath, 'r');

        while (false !== ($row = fgetcsv($fileResource, 0, $delimiter))) {
            $firstRow = $row;

            break;
        }

        fclose($fileResource);
        return $firstRow;
    }
}
