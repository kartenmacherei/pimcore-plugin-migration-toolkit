<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Cache;
use Pimcore\Cache\Runtime as RuntimeCache;
use Pimcore\Config;
use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\NullOutputWriter;
use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\OutputWriterInterface;

abstract class AbstractMigrationHelper
{
    const UP = 'up';
    const DOWN = 'down';

    /** @var OutputWriterInterface */
    protected $output;

    public function setOutput(OutputWriterInterface $output)
    {
        $this->output = $output;
    }

    protected function getOutput(): OutputWriterInterface
    {
        if (!$this->output instanceof OutputWriterInterface) {
            return new NullOutputWriter();
        }

        return $this->output;
    }

    protected function isLanguageValid(string $language): bool
    {
        $config = Config::getSystemConfiguration();

        return in_array($language, explode(',', $config['general']['valid_languages']));
    }

    protected function clearCache(): void
    {
        Cache::clearAll();
        RuntimeCache::clear();
    }
}
