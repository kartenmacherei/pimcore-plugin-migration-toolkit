<?php

namespace Basilicom\PimcorePluginMigrationToolkit;

use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

class PimcorePluginMigrationToolkitBundle extends AbstractPimcoreBundle
{
    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return 'Set of Migration Helpers and further Migration Tools for Pimcore Migrations.';
    }

    /**
     * @inheritDoc
     */
    public function getVersion()
    {
        $parts = explode('@', Versions::getVersion('basilicom/pimcore-plugin-migration-toolkit'));

        return $parts[0];
    }
}
