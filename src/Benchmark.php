<?php

namespace SitPHP\Benchmarks;

class Benchmark
{

    protected $start_count = 0;
    protected $stop_count = 0;
    protected $elapsed = 0;
    protected $start_time;
    protected $end_time;
    
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
        if(isset($this->start_time)){
            $this->start_time = microtime(true) - ($this->end_time - $this->start_time);
        } else {
            $this->start_time = microtime(true);
        }
        $this->start_count++;
    }

    /**
     * Stop benchmark
     *
     * @return void
     * @throws \LogicException
     */
    function stop()
    {
        if (!$this->isRunning()) {
            return;
        }
        $this->end_time = microtime(true);
        $this->stop_count++;
    }

    /**
     * Returns the elapsed time, readable or not
     *
     * @param bool $raw
     * @param  string $format The format to display (printf format)
     * @return float|string
     */
    function getExecutionTime($raw = false, $format = null)
    {
        $end_time = $this->hasStopped() ? $this->end_time : microtime(true);
        $elapsed = $end_time - $this->start_time;

        return $raw ? $elapsed : self::readableElapsedTime($elapsed, $format);
    }


    /**
     * Wraps a callable with start() and end() calls
     *
     * Additional arguments passed to this method will be passed to
     * the callable.
     *
     * @param callable $callable
     * @return mixed
     */
    function run(callable $callable)
    {
        $arguments = func_get_args();
        array_shift($arguments);

        $this->start();
        $result = call_user_func_array($callable, $arguments);
        $this->stop();

        return $result;
    }


    /**
     * Returns a human readable elapsed time
     *
     * @param  float $microtime
     * @param  string $format The format to display (printf format)
     * @param int $round
     * @return string
     */
    function readableElapsedTime($microtime, $format = null, $round = 3)
    {
        if (is_null($format)) {
            $format = '%.3f%s';
        }

        if ($microtime >= 1) {
            $unit = 's';
            $time = round($microtime, $round);
        } else {
            $unit = 'ms';
            $time = round($microtime * 1000);

            $format = preg_replace('/(%.[\d]+f)/', '%d', $format);
        }

        return sprintf($format, $time, $unit);
    }


    function isRunning(){
        return $this->start_count - $this->stop_count == 1;
    }
    
    function getStartCount(){
        return $this->start_count;
    }
    
    function getStopCount(){
        return $this->stop_count;
    }
    
    static function getMemoryUsage($raw = false, $format = null)
    {
        $memory = memory_get_usage(true);
        return $raw ? $memory : self::readableMemorySize($memory, $format);
    }

    /**
     * Returns the memory peak, readable or not
     *
     * @param bool $raw
     * @param  string $format The format to display (printf format)
     * @return string|float
     */
    static function getMemoryPeak($raw = false, $format = null)
    {
        $memory = memory_get_peak_usage(true);
        return $raw ? $memory : self::readableMemorySize($memory, $format);
    }

    /**
     * Returns a human readable memory size
     *
     * @param   int    $size
     * @param   string $format   The format to display (printf format)
     * @param   int    $round
     * @return  string
     */
    static function readableMemorySize($size, $format = null, $round = 3)
    {
        $mod = 1024;
        if (is_null($format)) {
            $format = '%.2f%s';
        }
        $units = explode(' ','B Kb Mb Gb Tb');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        if (0 === $i) {
            $format = preg_replace('/(%.[\d]+f)/', '%d', $format);
        }
        return sprintf($format, round($size, $round), $units[$i]);
    }

}