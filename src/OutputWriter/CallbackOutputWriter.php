<?php

namespace Basilicom\PimcorePluginMigrationToolkit\OutputWriter;

use Closure;

class CallbackOutputWriter implements OutputWriterInterface
{
    /** @var Closure */
    protected $callback;

    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    public function writeMessage($message)
    {
        ($this->callback)($message);
    }
}
