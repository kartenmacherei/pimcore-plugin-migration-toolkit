<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Config;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Pimcore\Model\Site;
use Pimcore\Model\WebsiteSetting;
use Basilicom\PimcorePluginMigrationToolkit\Exceptions\InvalidSettingException;

class WebsiteSettingsMigrationHelper extends AbstractMigrationHelper
{
    const TYPE_TEXT     = 'text';
    const TYPE_DOCUMENT = 'document';
    const TYPE_ASSET    = 'asset';
    const TYPE_OBJECT   = 'object';
    const TYPE_BOOL     = 'bool';

    /**
     * @param string $name
     * @param string|null $text
     * @param string|null $language
     * @param int|null $siteId
     *
     * @throws InvalidSettingException
     */
    public function createOfTypeText(string $name, string $text = null, string $language = null, int $siteId = null): void
    {
        $this->create($name, self::TYPE_TEXT, $text, $language, $siteId);
    }

    /**
     * @param string $name
     * @param int|null $documentId
     * @param string|null $language
     * @param int|null $siteId
     *
     * @throws InvalidSettingException
     */
    public function createOfTypeDocument(string $name, int $documentId = null, string $language = null, int $siteId = null): void
    {
        if ($documentId) {
            $document = Document::getById($documentId);
            if (empty($document)) {
                $message = sprintf(
                    'Not creating Website Setting with name "%s". Document "%s" does not exist.',
                    $name,
                    $documentId
                );
                throw new InvalidSettingException($message);
            }
        }

        $this->create($name, self::TYPE_DOCUMENT, $documentId, $language, $siteId);
    }

    /**
     * @param string $name
     * @param int|null $assetId
     * @param string|null $language
     * @param int|null $siteId
     *
     * @throws InvalidSettingException
     */
    public function createOfTypeAsset(string $name, int $assetId = null, string $language = null, int $siteId = null): void
    {
        if ($assetId) {
            $asset = Asset::getById($assetId);
            if (empty($asset)) {
                $message = sprintf(
                    'Not creating Website Setting with name "%s". Asset "%s" does not exist.',
                    $name,
                    $assetId
                );
                throw new InvalidSettingException($message);
            }
        }

        $this->create($name, self::TYPE_ASSET, $assetId, $language, $siteId);
    }

    /**
     * @param string $name
     * @param int|null $objectId
     * @param string|null $language
     * @param int|null $siteId
     *
     * @throws InvalidSettingException
     */
    public function createOfTypeObject(string $name, int $objectId = null, string $language = null, int $siteId = null): void
    {
        if ($objectId) {
            $object = DataObject::getById($objectId);
            if (empty($object)) {
                $message = sprintf(
                    'Not creating Website Setting with name "%s". Object "%s" does not exist.',
                    $name,
                    $objectId
                );
                throw new InvalidSettingException($message);
            }
        }

        $this->create($name, self::TYPE_OBJECT, $objectId, $language, $siteId);
    }

    /**
     * @param string $name
     * @param bool|null $value
     * @param string|null $language
     * @param int|null $siteId
     *
     * @throws InvalidSettingException
     */
    public function createOfTypeBool(string $name, bool $value = null, string $language = null, int $siteId = null): void
    {
        $this->create($name, self::TYPE_BOOL, $value, $language, $siteId);
    }

    /**
     * @param string $name
     * @param string $type
     * @param mixed|null $data
     * @param string|null $language
     * @param int|null $siteId
     *
     * @throws InvalidSettingException
     */
    private function create(string $name, string $type, $data = null, string $language = null, int $siteId = null): void
    {
        $websiteSetting = WebsiteSetting::getByName($name);

        if ($websiteSetting instanceof WebsiteSetting) {
            $message = sprintf(
                'Not creating Website Setting with name "%s". Setting with this name already exists.',
                $name
            );
            $this->getOutput()->writeMessage($message);
            return;
        }

        if ($language) {
            if (!$this->isLanguageValid($language)) {
                $message = sprintf(
                    'Not creating Website Setting with name "%s". Language "%s" is not a valid system language.',
                    $name,
                    $language
                );
                throw new InvalidSettingException($message);
            }
        }

        if ($siteId) {
            $site = Site::getById($siteId);
            if (empty($site)) {
                $message = sprintf(
                    'Not creating Website Setting with name "%s". Site with id "%s" does not exist.',
                    $name,
                    $siteId
                );
                throw new InvalidSettingException($message);
            }
        }

        $websiteSetting = new WebsiteSetting();
        $websiteSetting->setName($name);
        $websiteSetting->setType($type);

        if ($language) {
            $websiteSetting->setLanguage($language);
        }

        if ($siteId) {
            $websiteSetting->setSiteId($siteId);
        }

        if ($data) {
            $websiteSetting->setData($data);
        }

        $websiteSetting->save();
    }

    public function delete(string $name): void
    {
        $websiteSetting = WebsiteSetting::getByName($name);

        if (empty($websiteSetting)) {
            $message = sprintf('Website Setting with name "%s" can not be deleted, because it does not exist.', $name);
            $this->getOutput()->writeMessage($message);
            return;
        }

        $websiteSetting->delete();
    }
}
