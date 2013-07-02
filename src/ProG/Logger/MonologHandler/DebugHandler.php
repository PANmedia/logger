<?php

namespace ProG\Logger\MonologHandler;

use Monolog\Handler\TestHandler;

class DebugHandler extends TestHandler
{
    /**
     * {@inheritdoc}
     */
    public function getLogs()
    {
        return array_map(function ($record) {
            return [
                'timestamp'  => $record['datetime']->getTimestamp(),
                'message'    => $record['message'],
                'level'      => $record['level'],
                'level_name' => $record['level_name'],
                'context'    => $record['context']
            ];
        }, $this->records);
    }
}
