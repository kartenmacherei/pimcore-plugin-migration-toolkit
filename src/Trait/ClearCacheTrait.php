<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Trait;

use Pimcore\Cache;
use Pimcore\Cache\RuntimeCache;

trait ClearCacheTrait
{
    protected function clearCache()
    {
        Cache::clearAll();
        RuntimeCache::clear();
    }
}
