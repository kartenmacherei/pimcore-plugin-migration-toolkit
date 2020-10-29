<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Migration;

use Basilicom\PimcorePluginMigrationToolkit\Helper\AssetMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\BundleMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\ClassDefinitionMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\CustomLayoutMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\DataObjectMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\DocTypesMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\DocumentMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\FieldcollectionMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\ImageThumbnailMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\LanguageSettingsMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\ObjectbrickMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\QuantityValueUnitMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\StaticRoutesMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\SystemSettingsMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\UserMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\UserRolesMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\VideoThumbnailMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\WebsiteSettingsMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\CallbackOutputWriter;
use Doctrine\DBAL\Migrations\Version;
use Exception;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use ReflectionClass;

abstract class AbstractAdvancedPimcoreMigration extends AbstractPimcoreMigration
{
    /** @var string */
    private $dataFolder;

    /** @var SystemSettingsMigrationHelper */
    private $systemSettingsMigrationHelper;

    /** @var LanguageSettingsMigrationHelper */
    private $languageSettingsMigrationHelper;

    /** @var WebsiteSettingsMigrationHelper */
    private $websiteSettingsMigrationHelper;

    /** @var StaticRoutesMigrationHelper */
    private $staticRoutesMigrationHelper;

    /** @var UserRolesMigrationHelper */
    private $userRolesMigrationHelper;

    /** @var UserMigrationHelper */
    private $userMigrationHelper;

    /** @var DocTypesMigrationHelper */
    private $docTypesMigrationHelper;

    /** @var BundleMigrationHelper */
    private $bundleMigrationHelper;

    /** @var ClassDefinitionMigrationHelper */
    private $classDefinitionMigrationHelper;

    /** @var ObjectbrickMigrationHelper */
    private $objectbrickMigrationHelper;

    /** @var FieldcollectionMigrationHelper */
    private $fieldcollectionMigrationHelper;

    /** @var CustomLayoutMigrationHelper */
    private $customLayoutMigrationHelper;

    /** @var DocumentMigrationHelper */
    private $documentMigrationHelper;

    /** @var DataObjectMigrationHelper */
    private $dataObjectMigrationHelper;

    /** @var AssetMigrationHelper */
    private $assetMigrationHelper;

    /** @var ImageThumbnailMigrationHelper */
    private $imageThumbnailMigrationHelper;

    /** @var VideoThumbnailMigrationHelper */
    private $videoThumbnailMigrationHelper;

    /** @var QuantityValueUnitMigrationHelper */
    private $quantityValueUnitMigrationHelper;

    public function __construct(Version $version)
    {
        parent::__construct($version);

        try {
            $reflection = new ReflectionClass($this);
            $path = str_replace($reflection->getShortName() . '.php', '', $reflection->getFileName());
            $this->dataFolder = $path . 'data/' . $reflection->getShortName();
        } catch (Exception $exception) {
            // do nothing
        }
    }

    public function getOutputWriter(): CallbackOutputWriter
    {
        return new CallbackOutputWriter(
            function ($message) {
                $this->writeMessage($message);
            }
        );
    }

    public function getSystemSettingsMigrationHelper(): SystemSettingsMigrationHelper
    {
        if ($this->systemSettingsMigrationHelper === null) {
            $this->systemSettingsMigrationHelper = new SystemSettingsMigrationHelper();
            $this->systemSettingsMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->systemSettingsMigrationHelper;
    }

    public function getLanguageSettingsMigrationHelper(): LanguageSettingsMigrationHelper
    {
        if ($this->languageSettingsMigrationHelper === null) {
            $this->languageSettingsMigrationHelper = new LanguageSettingsMigrationHelper();
            $this->languageSettingsMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->languageSettingsMigrationHelper;
    }

    public function getWebsiteSettingsMigrationHelper(): WebsiteSettingsMigrationHelper
    {
        if ($this->websiteSettingsMigrationHelper === null) {
            $this->websiteSettingsMigrationHelper = new WebsiteSettingsMigrationHelper();
            $this->websiteSettingsMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->websiteSettingsMigrationHelper;
    }

    public function getStaticRoutesMigrationHelper(): StaticRoutesMigrationHelper
    {
        if ($this->staticRoutesMigrationHelper === null) {
            $this->staticRoutesMigrationHelper = new StaticRoutesMigrationHelper();
            $this->staticRoutesMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->staticRoutesMigrationHelper;
    }

    public function getUserRolesMigrationHelper(): UserRolesMigrationHelper
    {
        if ($this->userRolesMigrationHelper === null) {
            $this->userRolesMigrationHelper = new UserRolesMigrationHelper();
            $this->userRolesMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->userRolesMigrationHelper;
    }

    public function getUserMigrationHelper(): UserMigrationHelper
    {
        if ($this->userMigrationHelper === null) {
            $this->userMigrationHelper = new UserMigrationHelper();
            $this->userMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->userMigrationHelper;
    }

    public function getDocTypesMigrationHelper(): DocTypesMigrationHelper
    {
        if ($this->docTypesMigrationHelper === null) {
            $this->docTypesMigrationHelper = new DocTypesMigrationHelper();
            $this->docTypesMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->docTypesMigrationHelper;
    }

    public function getBundleMigrationHelper(): BundleMigrationHelper
    {
        if ($this->bundleMigrationHelper === null) {
            $this->bundleMigrationHelper = new BundleMigrationHelper();
            $this->bundleMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->bundleMigrationHelper;
    }

    public function getClassDefinitionMigrationHelper(): ClassDefinitionMigrationHelper
    {
        if ($this->classDefinitionMigrationHelper === null) {
            $this->classDefinitionMigrationHelper = new ClassDefinitionMigrationHelper($this->dataFolder);
            $this->classDefinitionMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->classDefinitionMigrationHelper;
    }

    public function getObjectbrickMigrationHelper(): ObjectbrickMigrationHelper
    {
        if ($this->objectbrickMigrationHelper === null) {
            $this->objectbrickMigrationHelper = new ObjectbrickMigrationHelper($this->dataFolder);
            $this->objectbrickMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->objectbrickMigrationHelper;
    }

    public function getFieldcollectionMigrationHelper(): FieldcollectionMigrationHelper
    {
        if ($this->fieldcollectionMigrationHelper === null) {
            $this->fieldcollectionMigrationHelper = new FieldcollectionMigrationHelper($this->dataFolder);
            $this->fieldcollectionMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->fieldcollectionMigrationHelper;
    }

    public function getCustomLayoutMigrationHelper(): CustomLayoutMigrationHelper
    {
        if ($this->customLayoutMigrationHelper === null) {
            $this->customLayoutMigrationHelper = new CustomLayoutMigrationHelper($this->dataFolder);
            $this->customLayoutMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->customLayoutMigrationHelper;
    }

    public function getDocumentMigrationHelper(): DocumentMigrationHelper
    {
        if ($this->documentMigrationHelper === null) {
            $this->documentMigrationHelper = new DocumentMigrationHelper();
            $this->documentMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->documentMigrationHelper;
    }

    public function getDataObjectMigrationHelper(): DataObjectMigrationHelper
    {
        if ($this->dataObjectMigrationHelper === null) {
            $this->dataObjectMigrationHelper = new DataObjectMigrationHelper();
            $this->dataObjectMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->dataObjectMigrationHelper;
    }

    public function getAssetMigrationHelper(): AssetMigrationHelper
    {
        if ($this->assetMigrationHelper === null) {
            $this->assetMigrationHelper = new AssetMigrationHelper();
            $this->assetMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->assetMigrationHelper;
    }

    public function getImageThumbnailMigrationHelper(): ImageThumbnailMigrationHelper
    {
        if ($this->imageThumbnailMigrationHelper === null) {
            $this->imageThumbnailMigrationHelper = new ImageThumbnailMigrationHelper();
            $this->imageThumbnailMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->imageThumbnailMigrationHelper;
    }

    public function getVideoThumbnailMigrationHelper(): VideoThumbnailMigrationHelper
    {
        if ($this->videoThumbnailMigrationHelper === null) {
            $this->videoThumbnailMigrationHelper = new VideoThumbnailMigrationHelper();
            $this->videoThumbnailMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->videoThumbnailMigrationHelper;
    }

    public function getQuantityValueUnitMigrationHelper(): QuantityValueUnitMigrationHelper
    {
        if ($this->quantityValueUnitMigrationHelper === null) {
            $this->quantityValueUnitMigrationHelper = new QuantityValueUnitMigrationHelper();
            $this->quantityValueUnitMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->quantityValueUnitMigrationHelper;
    }
}
