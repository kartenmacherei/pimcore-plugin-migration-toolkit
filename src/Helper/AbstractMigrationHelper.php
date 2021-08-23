<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Cache;
use Pimcore\Cache\Runtime as RuntimeCache;
use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\NullOutputWriter;
use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\OutputWriterInterface;
use Pimcore\Tool;

abstract class AbstractMigrationHelper
{
    const UP = 'up';
    const DOWN = 'down';

    protected OutputWriterInterface $output;

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

    protected function isValidLanguage(string $language): bool
    {
        return in_array($language, Tool::getValidLanguages());
    }

    protected function clearCache(): void
    {
        Cache::clearAll();
        RuntimeCache::clear();
    }
}
