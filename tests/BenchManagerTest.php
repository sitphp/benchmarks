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

}
