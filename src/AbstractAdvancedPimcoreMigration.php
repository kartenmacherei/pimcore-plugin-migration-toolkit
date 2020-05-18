<?php

namespace PimcorePluginMigrationToolkit;

use PimcorePluginMigrationToolkit\Helper\SystemSettingsMigrationHelper;
use PimcorePluginMigrationToolkit\OutputWriter\CallbackOutputWriter;
use Doctrine\DBAL\Migrations\Version;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use ReflectionClass;

abstract class AbstractAdvancedPimcoreMigration extends AbstractPimcoreMigration
{
    /** @var string */
    private $dataFolder;

    /** @var SystemSettingsMigrationHelper */
    private $systemSettingsMigrationHelper;

    public function __construct(Version $version)
    {
        parent::__construct($version);

        $reflection       = new ReflectionClass($this);
        $this->dataFolder = 'data/' . str_replace('.php', '', $reflection->getFileName());
    }

    /**
     * @return SystemSettingsMigrationHelper
     */
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
}
