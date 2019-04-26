<?php

namespace SitPHP\Benchmarks;

use SitPHP\Helpers\Format;

class Snap
{
    /**
     * @var BenchManager
     */
    private $time;
    /**
     * @var int
     */
    private $memory;
    /**
     * @var int
     */
    private $memory_peak;
    /**
     * @var string
     */
    private $name;

    function __construct(string $name = null)
    {
        $this->name = $name;
        $this->time = microtime(true);
        $this->memory = memory_get_usage(true);
        $this->memory_peak = memory_get_peak_usage(true);
    }

    function getName(){
        return $this->name;
    }

    function getTime()
    {
        return $this->time;
    }

    function getMemoryUsage($raw = false, int $round = 3, $format = '%size%%unit%')
    {
        return $raw ? $this->memory : Format::readableSize($this->memory, $round, $format);
    }

    function getMemoryPeak($raw = false, int $round = 3, $format = '%size%%unit%'){
        return $raw ? $this->memory_peak : Format::readableSize($this->memory_peak, $round, $format);
    }
}