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
        if ($this->isRunning()) {
            throw new LogicException('Stop benchmark before retrieving execution time');
        }

        $elapsed = 0;
        for ($i = 0; $i < count($this->run_stops); $i++) {
            $elapsed += $this->run_stops[$i] - $this->run_starts[$i];
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
     * @param string $name
     * @return Snap
     */
    function getSnap(string $name)
    {
        return $this->snaps[$name] ?? null;
    }

    function getAllSnaps()
    {
        return $this->snaps;
    }

    function hasSnap(string $name)
    {
        return isset($this->snaps[$name]);
    }

    function setTags($tags)
    {
        $tags = (array)$tags;
        $this->tags = array_unique(array_values($tags));
        return $this;
    }

    function addTags($tags)
    {
        $tags = (array)$tags;
        array_push($this->tags, ...$tags);
        $this->tags = array_unique($this->tags);
        return $this;
    }

    function getTags()
    {
        return $this->tags;
    }

    function hasTag(string $tag)
    {
        return in_array($tag, $this->tags);
    }

    function isRunning()
    {
        return count($this->run_starts) - count($this->run_stops) == 1;
    }

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