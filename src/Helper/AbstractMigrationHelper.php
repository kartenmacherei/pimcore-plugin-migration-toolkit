<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Pimcore\Cache;
use Pimcore\Config;
use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\NullOutputWriter;
use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\OutputWriterInterface;

abstract class AbstractMigrationHelper
{
    /**
     * @var OutputWriterInterface
     */
    protected $output;

    /**
     * @param OutputWriterInterface $output
     */
    public function setOutput(OutputWriterInterface $output)
    {
        $this->output = $output;
    }

    public function getOutput(): OutputWriterInterface
    {
        if (!$this->output instanceof OutputWriterInterface) {
            return new NullOutputWriter();
        }

        return $this->output;
    }

    protected function isLanguageValid(string $language): bool
    {
        $config = Config::getSystemConfiguration();
        if (in_array($language, explode(',', $config['general']['valid_languages']))) {
            return true;
        }

        return false;
    }

    protected function clearCache(): void
    {
        Cache::clearAll();
        Cache\Runtime::clear();
    }
}
