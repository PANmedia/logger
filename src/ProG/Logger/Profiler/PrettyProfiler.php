<?php

namespace ProG\Logger\Profiler;

use ProG\Logger\ConsoleInjectionTrait;

class PrettyProfiler implements ProfilerInterface
{
    /**
     * Inject the Console object
     */
    use ConsoleInjectionTrait;

    /**
     * {@inheritdoc}
     */
    public function appendTo($content)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {

    }
}
