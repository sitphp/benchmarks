<?php

namespace SitPHP\Benchmarks;

use InvalidArgumentException;
use SitPHP\Helpers\Format;
use SitPHP\Helpers\Text;

class BenchManager
{
    private $benchmarks = [];

    /**
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

    function hasBenchmark(string $name){
        return isset($this->benchmarks[$name]);
    }

    function getBenchmark(string $name){
        return $this->benchmarks[$name] ?? null;
    }

    function getBenchmarksByGroup(string $name){
        $benchmark_group = [];
        foreach($this->benchmarks as $benchmark){
            if($benchmark->getName() == $name || Text::startsWith($benchmark->getName(), $name.'.')){
                $benchmark_group[] = $benchmark;
            }
        }
        return $benchmark_group;
    }

    function getAllBenchmarks(){
        return $this->benchmarks;
    }

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

    function getMaxMemoryUsage($raw = false, int $round = 3, $format = '%size%%unit%'){
        if(empty($this->benchmarks)) {
            return null;
        }
        $max_memory_peak = max(array_map(function (Bench $bench){
            return $bench->getMaxMemoryUsage(true);
        }, $this->benchmarks));
        return $raw ? $max_memory_peak : Format::readableSize($max_memory_peak,$round, $format);
    }

    function getMinMemoryUsage($raw = false, int $round = 3, $format = '%size%%unit%'){
        if(empty($this->benchmarks)) {
            return null;
        }
        $min_memory_peak = min(array_map(function (Bench $bench){
            return $bench->getMinMemoryUsage(true);
        }, $this->benchmarks));
        return $raw ? $min_memory_peak : Format::readableSize($min_memory_peak,$round, $format);
    }

    function getMaxMemoryPeak($raw = false, int $round = 3, $format = '%size%%unit%'){
        if(empty($this->benchmarks)) {
            return null;
        }
        $max_memory_peak = max(array_map(function (Bench $bench){
            return $bench->getMaxMemoryPeak(true);
        }, $this->benchmarks));
        return $raw ? $max_memory_peak : Format::readableSize($max_memory_peak,$round, $format);
    }

    function getMinMemoryPeak($raw = false, int $round = 3, $format = '%size%%unit%'){
        if(empty($this->benchmarks)) {
            return null;
        }
        $min_memory_peak = min(array_map(function (Bench $bench){
            return $bench->getMinMemoryPeak(true);
        }, $this->benchmarks));
        return $raw ? $min_memory_peak : Format::readableSize($min_memory_peak,$round, $format);
    }

    function getMaxElapsed($raw = false, int $round = 3, string $format = '%time%%unit%'){
        if(empty($this->benchmarks)) {
            return null;
        }
        $max_elapsed = max(array_map(function (Bench $bench){
            return $bench->getElapsed(true);
        }, $this->benchmarks));
        return $raw ? $max_elapsed : Format::readableTime($max_elapsed,$round, $format);
    }

    function getMinElapsed($raw = false, int $round = 3, string $format = '%time%%unit%'){
        if(empty($this->benchmarks)) {
            return null;
        }
        $min_elapsed = min(array_map(function (Bench $bench){
            return $bench->getElapsed(true);
        }, $this->benchmarks));
        return $raw ? $min_elapsed : Format::readableTime($min_elapsed, $round, $format);
    }
}