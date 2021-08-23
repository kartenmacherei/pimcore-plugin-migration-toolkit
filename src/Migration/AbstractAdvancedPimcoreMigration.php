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
use Basilicom\PimcorePluginMigrationToolkit\Helper\ObjectbrickMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\QuantityValueUnitMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\StaticRoutesMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\UserMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\UserRolesMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\VideoThumbnailMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\Helper\WebsiteSettingsMigrationHelper;
use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\CallbackOutputWriter;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\AbstractMigration;
use Exception;
use Psr\Log\LoggerInterface;
use ReflectionClass;

abstract class AbstractAdvancedPimcoreMigration extends AbstractMigration
{
    private WebsiteSettingsMigrationHelper $websiteSettingsMigrationHelper;
    private StaticRoutesMigrationHelper $staticRoutesMigrationHelper;
    private UserRolesMigrationHelper $userRolesMigrationHelper;
    private UserMigrationHelper $userMigrationHelper;
    private DocTypesMigrationHelper $docTypesMigrationHelper;
    private BundleMigrationHelper $bundleMigrationHelper;
    private ClassDefinitionMigrationHelper $classDefinitionMigrationHelper;
    private ObjectbrickMigrationHelper $objectBrickMigrationHelper;
    private FieldcollectionMigrationHelper $fieldCollectionMigrationHelper;
    private CustomLayoutMigrationHelper $customLayoutMigrationHelper;
    private DocumentMigrationHelper $documentMigrationHelper;
    private DataObjectMigrationHelper $dataObjectMigrationHelper;
    private AssetMigrationHelper $assetMigrationHelper;
    private ImageThumbnailMigrationHelper $imageThumbnailMigrationHelper;
    private VideoThumbnailMigrationHelper $videoThumbnailMigrationHelper;
    private QuantityValueUnitMigrationHelper $quantityValueUnitMigrationHelper;

    private string $dataFolder;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        parent::__construct($connection, $logger);

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
                $this->write($message);
            }
        );
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

    public function getObjectBrickMigrationHelper(): ObjectbrickMigrationHelper
    {
        if ($this->objectBrickMigrationHelper === null) {
            $this->objectBrickMigrationHelper = new ObjectbrickMigrationHelper($this->dataFolder);
            $this->objectBrickMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->objectBrickMigrationHelper;
    }

    public function getFieldCollectionMigrationHelper(): FieldcollectionMigrationHelper
    {
        if ($this->fieldCollectionMigrationHelper === null) {
            $this->fieldCollectionMigrationHelper = new FieldcollectionMigrationHelper($this->dataFolder);
            $this->fieldCollectionMigrationHelper->setOutput($this->getOutputWriter());
        }

        return $this->fieldCollectionMigrationHelper;
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
