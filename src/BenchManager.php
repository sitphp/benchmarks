<?php

namespace SitPHP\Benchmarks;

use InvalidArgumentException;
use SitPHP\Helpers\Format;
use SitPHP\Helpers\Text;

class BenchManager
{
    private $benchmarks = [];

    /**
     * Return a new bench
     *
     * @param string|null $name
     * @return Bench
     */
    function benchmark(string $name = null){
        if(isset($name)){
            if(isset($this->benchmarks[$name])){
                throw new InvalidArgumentException('Bench with name "'.$name.'" was already made');
            }
            $this->benchmarks[$name] = $bench = new Bench($name);;
        } else {
            $bench = new Bench();
        }
        return $bench;
    }

    /**
     * Check if benchmark exists
     *
     * @param string $name
     * @return bool
     */
    function hasBenchmark(string $name){
        return isset($this->benchmarks[$name]);
    }

    /**
     * Get a bench by name
     *
     * @param string $name
     * @return mixed|null
     */
    function getBenchmark(string $name){
        return $this->benchmarks[$name] ?? null;
    }

    /**
     * Return all benchmarks
     *
     * @return array
     */
    function getAllBenchmarks(){
        return $this->benchmarks;
    }

    /**
     * Get benchmarks by group
     *
     * @param string $name
     * @return array
     */
    function getBenchmarksByGroup(string $name){
        $benchmark_group = [];
        foreach($this->benchmarks as $benchmark){
            if($benchmark->getName() == $name || Text::startsWith($benchmark->getName(), $name.'.')){
                $benchmark_group[] = $benchmark;
            }
        }
        return $benchmark_group;
    }

    /**
     * Get benchmarks by tag
     *
     * @param string $tag
     * @return array
     */
    function getBenchmarksByTag(string $tag){
        $benchmarks = [];
        foreach ($this->benchmarks as $benchmark){
            if($benchmark->hasTag($tag)){
                $benchmarks[] = $benchmark;
            }
        }
        return $benchmarks;
    }

    /**
     * Wraps a callable with start() and end() calls
     *
     * @param callable $callable
     * @param array|null $args
     * @param null $response
     * @return Bench
     */
    function run(callable $callable, $args = null, &$response = null)
    {

        if($args !== null && !is_array($args)){
            $args = [$args];
        }
        $bench = new Bench();
        $bench->start();
        $response = call_user_func_array($callable, $args);
        $bench->stop();

        return $bench;
    }

    /**
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
     * Returns the memory peak, readable or not
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
}