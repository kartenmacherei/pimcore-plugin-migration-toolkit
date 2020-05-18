<?php

namespace PimcorePluginMigrationToolkit\Helper;

use PimcorePluginMigrationToolkit\OutputWriter\NullOutputWriter;
use PimcorePluginMigrationToolkit\OutputWriter\OutputWriterInterface;

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
}
