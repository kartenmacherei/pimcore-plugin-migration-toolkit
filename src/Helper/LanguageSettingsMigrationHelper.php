<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore;
use Pimcore\Config;
use Pimcore\File;
use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;
use Basilicom\PimcorePluginMigrationToolkit\Exceptions\SettingsNotFoundException;
use Symfony\Component\Yaml\Yaml;

class LanguageSettingsMigrationHelper extends AbstractMigrationHelper
{
    const SETTINGS_PIMCORE               = 'pimcore';
    const SETTINGS_GENERAL               = 'general';
    const SETTING_VALID_LANGUAGES        = 'valid_languages';
    const SETTING_FALLBACK_LANGUAGES     = 'fallback_languages';

    /** @var string */
    private $configFile;

    /** @var array */
    private $systemConfig;

    public function __construct()
    {
        $this->configFile   = Config::locateConfigFile('system.yml');
        $this->systemConfig = Yaml::parseFile($this->configFile);
    }

    private function saveSystemSettings(): void
    {
        $settingsYml = Yaml::dump($this->systemConfig, 5);
        File::put($this->configFile, $settingsYml);
    }

    public function setDefaultLanguageInAdminInterface(string $language): void
    {
        if (!isset($this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL])) {
            $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL] = [];
        }

        $this->assertLanguageIsAvailable($language);
        $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL]['language'] = $language;
        $this->saveSystemSettings();
    }

    public function setDefaultLanguage(string $language): void
    {
        if (!isset($this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL])) {
            $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL] = [];
        }

        if (!$this->isLanguageValid($language)) {
            $message = sprintf(
                'The language "%s" is not in the valid languages.',
                $language
            );
            throw new InvalidSettingException($message);
        }

        $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL]['default_language'] = $language;
        $this->saveSystemSettings();
    }

    /**
     * @param string $language
     * @param string $fallback
     *
     * @return void
     *
     * @throws InvalidSettingException
     * @throws SettingsNotFoundException
     */
    public function addLanguageWithFallback(string $language, $fallback = ''): void
    {
        if (!isset($this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL])) {
            throw new SettingsNotFoundException('There are no general SystemSettings available.');
        }
        $this->assertLanguageIsAvailable($language);
        $this->addToValidLanguages($language);
        $this->addFallbackLanguages($language, $fallback);
        $this->saveSystemSettings();
    }

    public function removeLanguage(string $language): void
    {
        if (!isset($this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL])) {
            return;
        }
        $this->removeFromValidLanguages($language);
        $this->removeFallbackLanguages($language);
        $this->saveSystemSettings();
    }

    private function getValidLanguages(): array
    {
        $currentValidLanguages = $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL][self::SETTING_VALID_LANGUAGES];

        return explode(',', $currentValidLanguages);
    }

    private function removeFromValidLanguages(string $language): void
    {
        if ($this->isLanguageValid($language)) {
            $validLanguages = $this->getValidLanguages();
            $newValidLanguages = array_diff($validLanguages, [$language]);

            $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL][self::SETTING_VALID_LANGUAGES] = implode(',', $newValidLanguages);
        }
    }

    /**
     * @param string $language
     * @param string $fallback
     *
     * @return void
     *
     * @throws InvalidSettingException
     */
    private function addFallbackLanguages(string $language, string $fallback = ''): void
    {
        $fallbackLanguages = $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL][self::SETTING_FALLBACK_LANGUAGES];

        if (!$this->isLanguageValid($fallback)) {
            $message = sprintf(
                '"%s" is not a valid language and cannot be used as fallback.',
                $fallback
            );
            throw new InvalidSettingException($message);
        }

        $fallbackLanguages[$language] = $fallback;

        $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL][self::SETTING_FALLBACK_LANGUAGES] = $fallbackLanguages;
    }

    private function removeFallbackLanguages(string $language): void
    {
        $fallbackLanguages = $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL][self::SETTING_FALLBACK_LANGUAGES];

        if (isset($fallbackLanguages[$language])) {
            unset($fallbackLanguages[$language]);
        }

        $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL][self::SETTING_FALLBACK_LANGUAGES] = $fallbackLanguages;
    }

    /**
     * @param string $language
     *
     * @return void
     *
     * @throws InvalidSettingException
     */
    private function assertLanguageIsAvailable(string $language): void
    {
        $isLanguageValid = Pimcore::getKernel()->getContainer()->get('pimcore.locale')->isLocale($language);
        if ($isLanguageValid === false) {
            $exceptionMessage = sprintf(
                'The language "%s" is not a pimcore locale.',
                $language
            );
            throw new InvalidSettingException($exceptionMessage);
        }
    }

    private function addToValidLanguages(string $language): void
    {
        if ($this->isLanguageValid($language)) {
            $message = sprintf(
                '"%s" is already a valid language.',
                $language
            );
            $this->getOutput()->writeMessage($message);
            return;
        }

        $validLanguages = $this->getValidLanguages();
        $validLanguages[] = $language;

        $this->systemConfig[self::SETTINGS_PIMCORE][self::SETTINGS_GENERAL][self::SETTING_VALID_LANGUAGES] = implode(',', $validLanguages);

        $this->saveSystemSettings();
    }
}
