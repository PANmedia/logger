<?php

namespace ProG\Logger\Profiler;

interface ProfilerInterface
{
    /**
     * Append the profiler to a string of content (json/html)
     * 
     * @param  string $content
     * @return \ProG\Logger\Profiler
     */
    public function appendTo($content);

    /**
     * Return a new string of content with the profiling/debugging information
     *
     * @return string
     */
    public function render();
}
