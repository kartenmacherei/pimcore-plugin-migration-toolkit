<?php

namespace Basilicom\PimcoreMigrationToolkit\OutputWriter;

class NullOutputWriter implements OutputWriterInterface
{
    public function __construct()
    {
    }

    public function writeMessage($message)
    {
    }
}
