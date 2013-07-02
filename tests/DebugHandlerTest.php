<?php

namespace ProfilerTest;

use ProG\Logger\MonologHandler\DebugHandler;
use ProG\Logger\Console;
use Monolog\Logger;

class DebugHandlerTest extends \PHPUnit_Framework_Testcase
{
    public function testIsInstanceOfParents()
    {
        $h = new DebugHandler;
        $this->assertInstanceOf('Monolog\Handler\TestHandler', $h);
        $this->assertInstanceOf('Monolog\Handler\AbstractProcessingHandler', $h);
        $this->assertInstanceOf('Monolog\Handler\AbstractHandler', $h);
        $this->assertInstanceOf('Monolog\Handler\HandlerInterface', $h);
    }

    public function testGetLogsReturnsArray()
    {
        $h = new DebugHandler;
        $this->assertInternalType('array', $h->getLogs());
    }

    public function testGetLogsMapsArray()
    {
        $logger = new Logger('test');
        $c = new Console($logger);
        $h = new DebugHandler;
        $c->getLogger()->pushHandler($h);
        $c->getLogger()->info('Some info log');
        foreach ($h->getLogs() as $log) {
            $this->assertArrayHasKey('timestamp', $log);
            $this->assertArrayHasKey('message', $log);
            $this->assertArrayHasKey('level', $log);
            $this->assertArrayHasKey('level_name', $log);
            $this->assertArrayHasKey('context', $log);
        }
    }
}
