<?php

namespace ProG\Logger;

use PDO;
use Psr\Log\LoggerInterface;

class Console
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \PDO
     */
    protected $database;

    /**
     * @var array
     */
    protected $timers = [];

    /**
     * @var array
     */
    protected $memory = [];

    /**
     * @var array
     */
    protected $queries = [];

    /**
     * Constructor
     * 
     * @param \Psr\Log\LoggerInterface $logger
     * @param \PDO                     $database
     */
    public function __construct(LoggerInterface $logger, PDO $database = null)
    {
        $this->logger = $logger;
        $this->database = $database;
    }

    /**
     * Returns the logger instance
     * 
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set a new logger
     * 
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Start a timer
     * 
     * @param  string $key
     * @return \ProG\Logger\Console
     */
    public function start($key)
    {
        $this->timers[$key]['start'] = microtime(true);

        return $this;
    }

    /**
     * Stop a timer
     *
     * @throws \InvalidArgumentException
     * @param  string $key
     * @return \ProG\Logger\Console
     */
    public function stop($key)
    {
        if (! array_key_exists($key, $this->timers)) {
            throw new \InvalidArgumentException(
                sprintf('The timer for (%s) cannot be stopped as it was not started', $key)
            );
        }

        $this->timers[$key]['stop'] = microtime(true);

        $time = number_format($this->timers[$key]['stop'] - $this->timers[$key]['start'], 6);

        $this->getLogger()->info(
            sprintf('Timer: (%s) took %s seconds', $key, $time)
        );

        return $this;
    }

    /**
     * Start a memory measurement
     * 
     * @param  string $key
     * @return \ProG\Logger\Console
     */
    public function startMemory($key)
    {
        $this->memory[$key]['start'] = memory_get_usage();

        return $this;
    }

    /**
     * Stop a memory measurement
     *
     * @throws \InvalidArgumentException
     * @param  string $key
     * @return \ProG\Logger\Console
     */
    public function stopMemory($key)
    {
        if (! array_key_exists($key, $this->memory)) {
            throw new \InvalidArgumentException(
                sprintf('The memory measurement for (%s) cannot be stopped as it was not started', $key)
            );
        }

        $this->memory[$key]['stop'] = memory_get_usage();

        $memory = round($this->memory[$key]['stop'] - $this->memory[$key]['start'], 2);

        $this->getLogger()->info(
            sprintf('Memory: (%s) used %s kB of memory', $key, $memory)
        );

        $this->memory[$key]['stop'] = microtime(true);

        return $this;
    }

    /**
     * Run as Query starts
     * 
     * @param  string $sql    
     * @param  array  $params 
     * @param  array  $types  
     * @return \ProG\Logger\Console       
     */
    public function startQuery($sql, array $params = [], array $types = [])
    {
        $params = array_map(function ($type, $param) {
            return [$type => $param];
        }, $types, $params);

        $query = [
            'sql'       => $sql,
            'params'    => $params,
            'start'     => microtime(true),
            'mem_start' => memory_get_usage()
        ];

        // TODO EXPLAIN on queries if PDO is present

        $this->queries[] = $query;

        return $this;
    }

    /**
     * Run as query finishes
     * 
     * @return \PHProfiler\Console
     */

    public function stopQuery()
    {
        // move the pointer to the end of the queries array so we know what
        // key to match up with (the latest query started)
        end($this->queries);
        $key = key($this->queries);

        $this->queries[$key]['stop'] = microtime(true);
        $this->queries[$key]['mem_stop'] = memory_get_usage();

        $this->queries[$key]['time_taken'] = number_format($this->queries[$key]['stop'] - $this->queries[$key]['start'], 6);
        $mem = $this->queries[$key]['mem_stop'] - $this->queries[$key]['mem_start'];
        $this->queries[$key]['memory_usage'] = round($mem / 1024, 2);

        // clear out unwanted keys
        unset($this->queries[$key]['start'], $this->queries[$key]['stop'], $this->queries[$key]['mem_start'], $this->queries[$key]['mem_stop']);

        return $this;
    }

    /**
     * Return all timers
     * 
     * @return array
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * Return all memory measurements
     * 
     * @return array
     */
    public function getMemoryMeasurements()
    {
        return $this->memory;
    }

    /**
     * Return all SQL queries
     * 
     * @return array
     */
    public function getQueries()
    {
        return $this->queries;
    }
}
