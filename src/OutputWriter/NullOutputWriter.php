<?php

namespace PimcorePluginMigrationToolkit\OutputWriter;

class NullOutputWriter implements OutputWriterInterface
{
    public function __construct()
    {
    }

    public function writeMessage($message)
    {
    }
}
