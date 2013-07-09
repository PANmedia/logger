<?php

namespace ProfilerTest;

use ProG\Logger\Profiler;

class ProfilerTest extends \PHPUnit_Framework_Testcase
{
    public function getConsoleMock()
    {
        return $this->getMockBuilder('ProG\Logger\Console')
                    ->disableOriginalConstructor()
                    ->getMock();
    }

    public function testJsonProfilerThrowsExceptionWhenPassedNonString()
    {
        $this->setExpectedException('InvalidArgumentException');
        $p = new Profiler\JsonProfiler($this->getConsoleMock());
        $p->appendTo([]);
    }

    public function testJsonProfilerThrowsExceptionWhenPassedInvalidJson()
    {
        $this->setExpectedException('InvalidArgumentException');
        $p = new Profiler\JsonProfiler($this->getConsoleMock());
        $p->appendTo('[{{some_string');
    }

    public function testJsonProfilerStoresJsonDataToAppendTo()
    {
        $p = new Profiler\JsonProfiler($this->getConsoleMock());
    }
}
