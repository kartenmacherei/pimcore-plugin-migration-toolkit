<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Trait;

use Pimcore\Cache;
use Pimcore\Cache\Runtime as RuntimeCache;

trait ClearCacheTrait
{
    protected function clearCache()
    {
        Cache::clearAll();
        RuntimeCache::clear();
    }
}
