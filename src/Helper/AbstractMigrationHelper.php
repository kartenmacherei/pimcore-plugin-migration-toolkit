<?php

namespace Basilicom\PimcorePluginMigrationToolkit\Helper;

use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\NullOutputWriter;
use Basilicom\PimcorePluginMigrationToolkit\OutputWriter\OutputWriterInterface;
use Basilicom\PimcorePluginMigrationToolkit\Trait\ClearCacheTrait;
use Pimcore\Tool;

abstract class AbstractMigrationHelper
{
    use ClearCacheTrait;

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
}
