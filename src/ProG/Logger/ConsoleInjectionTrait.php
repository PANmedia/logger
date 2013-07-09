<?php

namespace ProG\Logger;

trait ConsoleInjectionTrait
{
    /**
     * @var \ProG\Logger\Console
     */
    protected $console;

    /**
     * Constructor to allow injection of the Console object
     * 
     * @param \ProG\Logger\Console $console
     */
    public function __construct(Console $console)
    {
        $this->console = $console;
    }

    /**
     * Return the console object
     * 
     * @return \ProG\Logger\Console
     */
    public function getConsole()
    {
        return $this->console;
    }
}
