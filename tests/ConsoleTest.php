<?php

namespace ProfilerTest;

use ProG\Logger\Console;

class ConsoleTest extends \PHPUnit_Framework_Testcase
{
    public function testLoggerGetsSet()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $c = new Console($logger);
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $c->getLogger());
        $c->setLogger($logger);
        $this->assertInstanceOf('Psr\Log\LoggerInterface', $c->getLogger());
    }

    public function testStartAndStopTimer()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->once())
               ->method('info');
        $c = new Console($logger);
        $c->start('test');
        $c->stop('test');
        $this->assertArrayHasKey('test', $c->getTimers());
    }

    public function testStoppingTimerWithoutStartingThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $c = new Console($logger);
        $c->stop('un_started_key');
    }

    public function testStartAndStopMemoryMeasurement()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->once())
               ->method('info');
        $c = new Console($logger);
        $c->startMemory('test');
        $c->stopMemory('test');
        $this->assertArrayHasKey('test', $c->getMemoryMeasurements());
    }

    public function testStoppingMemoryMeasurementWithoutStartingThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $c = new Console($logger);
        $c->stopMemory('un_started_key');
    }

    public function testStartAndStopQuery()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $c = new Console($logger);
        $c->startQuery(
            'SELECT * FROM something WHERE something = ?',
            [3],
            ['integer']
        );
        $c->stopQuery();
        $this->assertArrayHasKey('sql', $c->getQueries()[0]);
        $this->assertArrayHasKey('params', $c->getQueries()[0]);
    }
}
