<?php

namespace SitPHP\Benchmarks;

use InvalidArgumentException;
use LogicException;
use SitPHP\Helpers\Format;

class Bench
{

    private $start_time;
    private $stop_time;

    private $run_starts = [];
    private $run_stops = [];
    private $elapsed = [];
    private $tags = [];
    private $name;
    /**
     * @var array
     */
    private $snaps = [];

    /**
     * Bench constructor.
     * @param string|null $name
     */
    function __construct(string $name = null)
    {
        $this->name = $name;
    }

    /**
     * Return benchmark name
     *
     * @return string|null
     */
    function getName()
    {
        return $this->name;
    }

    /**
     * Start benchmark
     *
     * @return void
     */
    function start()
    {
        if ($this->isRunning()) {
            return;
        }
        $this->run_starts[] = microtime(true);
        if (!isset($this->start_time)) {
            $this->start_time = end($this->run_starts);
        }
    }

    /**
     * Stop benchmark
     *
     * @return void
     * @throws LogicException
     */
    function stop()
    {
        if (!$this->isRunning()) {
            return;
        }
        $this->run_stops[] = microtime(true);
        $this->elapsed[] = end($this->run_stops) - end($this->run_starts);
        $this->stop_time = end($this->run_stops);
    }

    /**
     * Return start time
     *
     * @return string
     */
    function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Return stop time
     *
     * @return string
     */
    function getStopTime()
    {
        return $this->stop_time;
    }

    /**
     * Returns the elapsed time
     *
     * @param bool $raw
     * @param int $round
     * @param string $format The format to display (printf format)
     * @return float|string
     */
    function getElapsed($raw = false, int $round = 3, string $format = '%time%%unit%')
    {
        $elapsed = 0;
        for ($i = 0; $i < count($this->run_stops); $i++) {
            $elapsed += $this->run_stops[$i] - $this->run_starts[$i];
        }
        if ($this->isRunning()) {
            $elapsed += microtime(true) - end($this->run_starts);
        }

        return $raw ? $elapsed : Format::readableTime($elapsed, $round, $format);
    }


    /**
     * Take a snapshot of time and memory
     *
     * @param string|null $name
     * @return Snap
     */
    function snap(string $name = null)
    {

        if (isset($name)) {
            if (isset($this->snaps[$name])) {
                throw new InvalidArgumentException('Snap with name "' . $name . '" was already made.');
            }
            $this->snaps[$name] = $snap = new Snap($name);;
        } else {
            $snap = new Snap();
        }
        return $snap;
    }

    /**
     * Return given benchmark snapshot
     *
     * @param string $name
     * @return Snap
     */
    function getSnap(string $name)
    {
        return $this->snaps[$name] ?? null;
    }

    /**
     * Return all benchmark snapshots
     *
     * @return array
     */
    function getAllSnaps()
    {
        return $this->snaps;
    }

    /**
     * Check if benchmark has given snapshot
     *
     * @param string $name
     * @return bool
     */
    function hasSnap(string $name)
    {
        return isset($this->snaps[$name]);
    }

    /**
     * Set benchmark tags
     *
     * @param $tags
     * @return $this
     */
    function setTags($tags)
    {
        $tags = (array)$tags;
        $this->tags = array_unique(array_values($tags));
        return $this;
    }

    /**
     * Add benchmark tag
     *
     * @param $tags
     * @return $this
     */
    function addTags($tags)
    {
        $tags = (array)$tags;
        array_push($this->tags, ...$tags);
        $this->tags = array_unique($this->tags);
        return $this;
    }

    /**
     * Return benchmark tags
     *
     * @return array
     */
    function getTags()
    {
        return $this->tags;
    }

    /**
     * Check if benchmark has tag
     *
     * @param string $tag
     * @return bool
     */
    function hasTag(string $tag)
    {
        return in_array($tag, $this->tags);
    }

    /**
     * Check if benchmark is running
     *
     * @return bool
     */
    function isRunning()
    {
        return count($this->run_starts) - count($this->run_stops) == 1;
    }

    /**
     * Returns the memory usage
     *
     * @param bool $raw
     * @param int $round
     * @param string $format
     * @return int|string
     */
    function getMemoryUsage($raw = false, int $round = 3,$format = '%size%%unit%')
    {
        $memory = memory_get_usage(true);
        return $raw ? $memory : Format::readableSize($memory,$round, $format);
    }

    /**
     * Returns the memory peak
     *
     * @param bool $raw
     * @param int $round
     * @param string $format The format to display
     * @return string|float
     */
    function getMemoryPeak($raw = false, int $round = 3, $format = '%size%%unit%')
    {
        $memory = memory_get_peak_usage(true);
        return $raw ? $memory : Format::readableSize($memory,$round, $format);
    }

    /**
     * Return min snaps memory usage
     *
     * @param bool $raw
     * @param int $round
     * @param string $format
     * @return mixed|string|null
     */
    function getMinMemoryUsage($raw = false, int $round = 3, $format = '%size%%unit%')
    {
        if(empty($this->snaps)) {
            return null;
        }
        $min_memory_usage = min(array_map(function (Snap $snap){
            return $snap->getMemoryUsage(true);
        }, $this->snaps));

        return $raw ? $min_memory_usage : Format::readableSize($min_memory_usage, $round, $format);
    }

    /**
     * Return max snaps memory usage
     *
     * @param bool $raw
     * @param int $round
     * @param string $format
     * @return mixed|string|null
     */
    function getMaxMemoryUsage($raw = false, int $round = 3, $format = '%size%%unit%')
    {
        if(empty($this->snaps)) {
            return null;
        }
        $max_memory_usage = max(array_map(function (Snap $snap){
            return $snap->getMemoryUsage(true);
        }, $this->snaps));
        return $raw ? $max_memory_usage : Format::readableSize($max_memory_usage, $round, $format);
    }

    /**
     * Return max snaps memory peak
     *
     * @param bool $raw
     * @param int $round
     * @param string $format
     * @return mixed|string|null
     */
    function getMaxMemoryPeak($raw = false, int $round = 3, $format = '%size%%unit%')
    {
        if(empty($this->snaps)) {
            return null;
        }
        $max_memory_peak = max(array_map(function (Snap $snap){
            return $snap->getMemoryPeak(true);
        }, $this->snaps));
        return $raw ? $max_memory_peak : Format::readableSize($max_memory_peak, $round, $format);
    }

    /**
     * Return min snaps memory peak
     *
     * @param bool $raw
     * @param int $round
     * @param string $format
     * @return mixed|string|null
     */
    function getMinMemoryPeak($raw = false, int $round = 3, $format = '%size%%unit%')
    {
        if(empty($this->snaps)) {
            return null;
        }
        $min_memory_peak = min(array_map(function (Snap $snap){
            return $snap->getMemoryPeak(true);
        }, $this->snaps));
        return $raw ? $min_memory_peak : Format::readableSize($min_memory_peak, $round, $format);
    }

}