<?php


use SitPHP\Benchmarks\Bench;
use SitPHP\Benchmarks\BenchManager;
use PHPUnit\Framework\TestCase;

class BenchManagerTest extends TestCase
{

    /*
     * Test benchmark
     */
    public function testBenchmark()
    {
        $bench_manager = new BenchManager();
        $benchmark1 = $bench_manager->benchmark('name');
        $benchmark2 = $bench_manager->benchmark();

        $this->assertEquals('name', $benchmark1->getName());
        $this->assertNull($benchmark2->getName());
    }

    public function testGetBenchmark()
    {
        $bench_manager = new BenchManager();
        $benchmark = $bench_manager->benchmark('name');
        $this->assertSame($benchmark, $bench_manager->getBenchmark('name'));
        $this->assertNull($bench_manager->getBenchmark('undefined'));
    }

    public function testHasBenchmark(){
        $bench_manager = new BenchManager();
        $bench_manager->benchmark('name');
        $this->assertTrue($bench_manager->hasBenchmark('name'));
        $this->assertFalse($bench_manager->hasBenchmark('undefined'));
    }

    function testCreateBenchmarkWithSameNameTwiceShouldFail(){
        $this->expectException(InvalidArgumentException::class);

        $bench_manager = new BenchManager();
        $bench_manager->benchmark('name');
        $bench_manager->benchmark('name');
    }


    /*
     * Test memory
     */
    public function testGetMemoryUsage()
    {
        $bench_manager = new BenchManager();
        $bench = $bench_manager->getMemoryUsage(true);
        $this->assertIsInt($bench);
    }


    public function testGetMemoryPeak()
    {
        $bench_manager = new BenchManager();
        $bench = $bench_manager->getMemoryPeak(true);
        $this->assertIsInt($bench);
    }

    /*
     * Test get benchmarks
     */

    public function testGetBenchmarksByGroup()
    {
        $bench_manager = new BenchManager();
        $benchmark1 = $bench_manager->benchmark('group1.name1');
        $benchmark2 = $bench_manager->benchmark('group1.name2');
        $bench_manager->benchmark('group2.name1');

        $this->assertEquals([$benchmark1, $benchmark2], $bench_manager->getBenchmarksByGroup('group1'));
    }

    public function testGetBenchmarksByTag()
    {
        $bench_manager = new BenchManager();
        $benchmark1 = $bench_manager->benchmark('name1')->setTags(['tag1', 'tag2']);
        $benchmark2 = $bench_manager->benchmark('name2')->setTags(['tag2', 'tag3']);
        $benchmark3 = $bench_manager->benchmark('name3')->setTags(['tag2','tag3','tag4']);

        $this->assertEquals([$benchmark1, $benchmark2, $benchmark3], $bench_manager->getBenchmarksByTag('tag2'));
        $this->assertEquals([$benchmark1], $bench_manager->getBenchmarksByTag('tag1'));
        $this->assertEquals([$benchmark2, $benchmark3], $bench_manager->getBenchmarksByTag('tag3'));
    }


    public function testGetAllBenchmarks()
    {
        $bench_manager = new BenchManager();
        $benchmark1 = $bench_manager->benchmark('name1');
        $benchmark2 = $bench_manager->benchmark('name2');

        $this->assertEquals(['name1' => $benchmark1, 'name2' => $benchmark2], $bench_manager->getAllBenchmarks());
    }

    /*
     * Test run
     */
    public function testRun()
    {
        $bench_manager = new BenchManager();
        $bench = $bench_manager->run(function ($a){
            return $a;
        }, 'return', $response);

        $this->assertEquals('return', $response);
        $this->assertInstanceOf(Bench::class, $bench);
    }

    /*
     * Test min/max
     */
    function testGetMinMemoryUsage(){
        $bench_manager = new BenchManager();

        $this->assertNull($bench_manager->getMinMemoryUsage());

        $bench_manager->benchmark('name1');
        $bench_manager->benchmark('name2');
        $bench_manager->benchmark('name3');

        $max_memory_usage = min([$bench_manager->getBenchmark('name1')->getMinMemoryUsage(true), $bench_manager->getBenchmark('name2')->getMinMemoryUsage(true), $bench_manager->getBenchmark('name3')->getMinMemoryUsage(true)]);

        $this->assertEquals($max_memory_usage, $bench_manager->getMinMemoryUsage(true));
    }

    function testGetMaxMemoryUsage(){
        $bench_manager = new BenchManager();

        $this->assertNull($bench_manager->getMaxMemoryUsage());

        $bench_manager->benchmark('name1');
        $bench_manager->benchmark('name2');
        $bench_manager->benchmark('name3');

        $max_memory_usage = max([$bench_manager->getBenchmark('name1')->getMaxMemoryUsage(true), $bench_manager->getBenchmark('name2')->getMaxMemoryUsage(true), $bench_manager->getBenchmark('name3')->getMaxMemoryUsage(true)]);

        $this->assertEquals($max_memory_usage, $bench_manager->getMaxMemoryUsage(true));
    }

    function testGetMinMemoryPeak(){
        $bench_manager = new BenchManager();

        $this->assertNull($bench_manager->getMinMemoryPeak());

        $bench_manager->benchmark('name1');
        $bench_manager->benchmark('name2');
        $bench_manager->benchmark('name3');

        $max_memory_usage = min([$bench_manager->getBenchmark('name1')->getMinMemoryPeak(true), $bench_manager->getBenchmark('name2')->getMinMemoryPeak(true), $bench_manager->getBenchmark('name3')->getMinMemoryPeak(true)]);

        $this->assertEquals($max_memory_usage, $bench_manager->getMinMemoryPeak(true));
    }

    function testGetMaxMemoryPeak(){
        $bench_manager = new BenchManager();

        $this->assertNull($bench_manager->getMaxMemoryPeak());

        $bench_manager->benchmark('name1');
        $bench_manager->benchmark('name2');
        $bench_manager->benchmark('name3');

        $max_memory_usage = max([$bench_manager->getBenchmark('name1')->getMaxMemoryPeak(true), $bench_manager->getBenchmark('name2')->getMaxMemoryPeak(true), $bench_manager->getBenchmark('name3')->getMaxMemoryPeak(true)]);

        $this->assertEquals($max_memory_usage, $bench_manager->getMaxMemoryPeak(true));
    }

    function testGetMinElapsed(){
        $bench_manager = new BenchManager();

        $this->assertNull($bench_manager->getMinElapsed());

        $bench_manager->benchmark('name1');
        $bench_manager->benchmark('name2');
        $bench_manager->benchmark('name3');

        $max_memory_usage = min([$bench_manager->getBenchmark('name1')->getElapsed(true), $bench_manager->getBenchmark('name2')->getElapsed(true), $bench_manager->getBenchmark('name3')->getElapsed(true)]);

        $this->assertEquals($max_memory_usage, $bench_manager->getMinElapsed(true));
    }

    function testGetMaxElapsed(){
        $bench_manager = new BenchManager();

        $this->assertNull($bench_manager->getMaxElapsed());

        $bench_manager->benchmark('name1');
        $bench_manager->benchmark('name2');
        $bench_manager->benchmark('name3');

        $max_memory_usage = max([$bench_manager->getBenchmark('name1')->getElapsed(true), $bench_manager->getBenchmark('name2')->getElapsed(true), $bench_manager->getBenchmark('name3')->getElapsed(true)]);

        $this->assertEquals($max_memory_usage, $bench_manager->getMaxElapsed(true));
    }
}
