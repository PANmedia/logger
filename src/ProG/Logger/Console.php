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
     * PDO instance to execute EXPLAIN queries if available
     * 
     * @var \PDO
     */
    protected $database;

    /**
     * Array of timers
     * 
     * @var array
     */
    protected $timers = [];

    /**
     * Array of memory measurements
     * 
     * @var array
     */
    protected $memory = [];

    /**
     * Array of queries and related debugging data
     * 
     * @var array
     */
    protected $queries = [];

    /**
     * Array containing key => value handlers
     * 
     * @var array
     */
    protected $keyValueHandlers = [];

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

        $this->timers['script_execution']['start'] = $_SERVER['REQUEST_TIME_FLOAT'];
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

        $time = $this->getReadableTime($this->timers[$key]['start'], $this->timers[$key]['stop']);

        $this->timers[$key]['time_seconds'] = $time;

        $this->getLogger()->info(
            sprintf('Timer: (%s) took %s seconds', $key, $time)
        );

        return $this;
    }

    /**
     * Set additional timers
     *
     * @param array $timers Array of timers
     * @return \ProG\Logger\Console
     */
    public function setAdditionalTimers($timers = [])
    {
        foreach ($timers as $timer => $values) {
            $time = $this->getReadableTime($values['start'], $values['stop']);
            $timers[$timer]['time_seconds'] = $time;
        }

        $this->timers = array_merge($this->timers, $timers);
        return $this;
    }

    /**
     * Return all timers
     * 
     * @return array
     */
    public function getTimers()
    {
        $starts = [];
        foreach ($this->timers as $key => $v) $starts[$key] = $v['start'];
        array_multisort($starts);

        $sorted = [];
        foreach ($starts as $k=>$v) $sorted[$k] = $this->timers[$k];

        return $sorted;
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

        $this->memory[$key]['usage_kb'] = $this->getReadableMemory($this->memory[$key]['start'], $this->memory[$key]['stop']);

        $this->getLogger()->info(
            sprintf('Memory: (%s) used %s kB of memory', $key, $this->memory[$key]['usage_kb'])
        );

        return $this;
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
     * @return \ProG\Logger\Console
     */

    public function stopQuery()
    {
        // move the pointer to the end of the queries array so we know what
        // key to match up with (the latest query started)
        end($this->queries);
        $key = key($this->queries);

        $this->queries[$key]['stop'] = microtime(true);
        $this->queries[$key]['mem_stop'] = memory_get_usage();

        $this->queries[$key]['time_taken'] = $this->getReadableTime($this->queries[$key]['start'], $this->queries[$key]['stop']);
        $this->queries[$key]['memory_usage'] = $this->getReadableMemory($this->queries[$key]['mem_start'], $this->queries[$key]['mem_stop']);;

        // clear out unwanted keys
        unset($this->queries[$key]['start'], $this->queries[$key]['stop'], $this->queries[$key]['mem_start'], $this->queries[$key]['mem_stop']);

        return $this;
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

    /**
     * Returns an array of all included files and their file sizes
     * 
     * @return array
     */
    public function getIncludedFiles()
    {
        $return = [];

        foreach (get_included_files() as $file) {
            $return[$file] = round(filesize($file) / 1024, 2);
        }

        return $return;
    }

    /**
     * Provide an object an method name to invoke that will return a key => value array
     *
     * @param string        $name
     * @param object|string $object - the class/object that handles key => value data
     * @param string        $method - the method to retrieve all key => value pairs
     */
    public function setKeyValueHandler($name, $object, $method)
    {
        $this->keyValueHandlers[$name] = [
            'object' => $object,
            'method' => $method
        ];
    }

    /**
     * Invoke the method registered to retrieve session key => value pairs
     * 
     * @return array
     */
    protected function getKeyValueData()
    {
        if (empty($this->keyValueHandlers)) {
            return [];
        }

        // TODO reflect on object and invoke method to create array
    }

    /**
     * Return a readable seconds float
     * 
     * @param  mixed $start 
     * @param  mixed $stop  
     * @return float        
     */
    public function getReadableTime($start, $stop)
    {
        return (float) number_format($stop - $start, 4);
    }

    /**
     * Return a readable kB float
     * 
     * @param  mixed $start 
     * @param  mixed $stop  
     * @return float
     */
    public function getReadableMemory($start, $stop)
    {
        $mem = $stop - $start;

        return round($mem / 1024, 2);
    }

    /**
     * Return all debug data to be parsed by profiler
     * 
     * @return array
     */
    public function getDebugData()
    {
        $data = [
            'queries'             => $this->getQueries(),
            'timers'              => $this->getTimers(),
            'memory_measurements' => $this->getMemoryMeasurements(),
            'included_files'      => $this->getIncludedFiles(),
            'globals'             => [
                'get'     => (isset($_GET) && is_array($_GET)) ? $_GET : [],
                'post'    => (isset($_POST) && is_array($_POST)) ? $_POST : [],
                'files'   => (isset($_FILES) && is_array($_FILES)) ? $_FILES : [],
                'session' => (isset($_SESSION) && is_array($_SESSION)) ? $_SESSION : [],
                'cookie'  => (isset($_COOKIE) && is_array($_COOKIE)) ? $_COOKIE : [],
                'request' => (isset($_REQUEST) && is_array($_REQUEST)) ? $_REQUEST : [],
                'env'     => (isset($_ENV) && is_array($_ENV)) ? $_ENV : [],
                'server'  => (isset($_SERVER) && is_array($_SERVER)) ? $_SERVER : [],
                'http_response_header' => (isset($http_response_header) && is_array($http_response_header)) ? $http_response_header : []
            ]
        ];

        foreach ($this->getKeyValueData() as $key => $array) {
            $data[$key] = $array;
        }

        return $data;
    }
}
