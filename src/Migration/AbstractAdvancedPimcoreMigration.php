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

    public function getSystemSettingsMigrationHelper(): SystemSettingsMigrationHelper
    {
        if ($this->systemSettingsMigrationHelper === null) {
            $this->systemSettingsMigrationHelper = new SystemSettingsMigrationHelper();
            $this->systemSettingsMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->systemSettingsMigrationHelper;
    }

    public function getLanguageSettingsMigrationHelper(): LanguageSettingsMigrationHelper
    {
        if ($this->languageSettingsMigrationHelper === null) {
            $this->languageSettingsMigrationHelper = new LanguageSettingsMigrationHelper();
            $this->languageSettingsMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->languageSettingsMigrationHelper;
    }

    public function getWebsiteSettingsMigrationHelper(): WebsiteSettingsMigrationHelper
    {
        if ($this->websiteSettingsMigrationHelper === null) {
            $this->websiteSettingsMigrationHelper = new WebsiteSettingsMigrationHelper();
            $this->websiteSettingsMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->websiteSettingsMigrationHelper;
    }

    public function getStaticRoutesMigrationHelper(): StaticRoutesMigrationHelper
    {
        if ($this->staticRoutesMigrationHelper === null) {
            $this->staticRoutesMigrationHelper = new StaticRoutesMigrationHelper();
            $this->staticRoutesMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->staticRoutesMigrationHelper;
    }

    public function getUserRolesMigrationHelper(): UserRolesMigrationHelper
    {
        if ($this->userRolesMigrationHelper === null) {
            $this->userRolesMigrationHelper = new UserRolesMigrationHelper();
            $this->userRolesMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->userRolesMigrationHelper;
    }

    public function getUserMigrationHelper(): UserMigrationHelper
    {
        if ($this->userMigrationHelper === null) {
            $this->userMigrationHelper = new UserMigrationHelper();
            $this->userMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->userMigrationHelper;
    }

    public function getDocTypesMigrationHelper(): DocTypesMigrationHelper
    {
        if ($this->docTypesMigrationHelper === null) {
            $this->docTypesMigrationHelper = new DocTypesMigrationHelper();
            $this->docTypesMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->docTypesMigrationHelper;
    }

    public function getBundleMigrationHelper(): BundleMigrationHelper
    {
        if ($this->bundleMigrationHelper === null) {
            $this->bundleMigrationHelper = new BundleMigrationHelper();
            $this->bundleMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->bundleMigrationHelper;
    }

    public function getClassDefinitionMigrationHelper(): ClassDefinitionMigrationHelper
    {
        if ($this->classDefinitionMigrationHelper === null) {
            $this->classDefinitionMigrationHelper = new ClassDefinitionMigrationHelper($this->dataFolder);
            $this->classDefinitionMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->classDefinitionMigrationHelper;
    }

    public function getObjectbrickMigrationHelper(): ObjectbrickMigrationHelper
    {
        if ($this->objectbrickMigrationHelper === null) {
            $this->objectbrickMigrationHelper = new ObjectbrickMigrationHelper($this->dataFolder);
            $this->objectbrickMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->objectbrickMigrationHelper;
    }

    public function getFieldcollectionMigrationHelper(): FieldcollectionMigrationHelper
    {
        if ($this->fieldcollectionMigrationHelper === null) {
            $this->fieldcollectionMigrationHelper = new FieldcollectionMigrationHelper($this->dataFolder);
            $this->fieldcollectionMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->fieldcollectionMigrationHelper;
    }

    public function getCustomLayoutMigrationHelper(): CustomLayoutMigrationHelper
    {
        if ($this->customLayoutMigrationHelper === null) {
            $this->customLayoutMigrationHelper = new CustomLayoutMigrationHelper($this->dataFolder);
            $this->customLayoutMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->customLayoutMigrationHelper;
    }

    public function getDocumentMigrationHelper(): DocumentMigrationHelper
    {
        if ($this->documentMigrationHelper === null) {
            $this->documentMigrationHelper = new DocumentMigrationHelper();
            $this->documentMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->documentMigrationHelper;
    }

    public function getDataObjectMigrationHelper(): DataObjectMigrationHelper
    {
        if ($this->dataObjectMigrationHelper === null) {
            $this->dataObjectMigrationHelper = new DataObjectMigrationHelper();
            $this->dataObjectMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->dataObjectMigrationHelper;
    }

    public function getAssetMigrationHelper(): AssetMigrationHelper
    {
        if ($this->assetMigrationHelper === null) {
            $this->assetMigrationHelper = new AssetMigrationHelper();
            $this->assetMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->assetMigrationHelper;
    }

    public function getImageThumbnailMigrationHelper(): ImageThumbnailMigrationHelper
    {
        if ($this->imageThumbnailMigrationHelper === null) {
            $this->imageThumbnailMigrationHelper = new ImageThumbnailMigrationHelper();
            $this->imageThumbnailMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->imageThumbnailMigrationHelper;
    }

    public function getQuantityValueUnitMigrationHelper(): QuantityValueUnitMigrationHelper
    {
        if ($this->quantityValueUnitMigrationHelper === null) {
            $this->quantityValueUnitMigrationHelper = new QuantityValueUnitMigrationHelper();
            $this->quantityValueUnitMigrationHelper->setOutput(
                new CallbackOutputWriter(
                    function ($message) {
                        $this->writeMessage($message);
                    }
                )
            );
        }

        return $this->quantityValueUnitMigrationHelper;
    }
}
