<?php


use SitPHP\Benchmarks\Bench;
use PHPUnit\Framework\TestCase;

class BenchTest extends TestCase
{
    /*
     * Test name
     */
    public function testGetName()
    {
        $bench1 = new Bench('name');
        $bench2 = new Bench();

        $this->assertEquals('name', $bench1->getName());
        $this->assertNull($bench2->getName());
    }

    /*
     * Test start/stop
     */
    public function testStartStopElapsed()
    {
        $bench = new Bench();
        $bench->start();
        sleep(1);
        $bench->stop();


        $this->assertTrue($bench->getElapsed(true) < 2);
        $this->assertTrue(1 < $bench->getElapsed(true));
    }

    public function testGetStartStopTime()
    {
        $bench = new Bench();
        $bench->start();
        sleep(1);
        $bench->stop();

        $elapsed = $bench->getStopTime(true) - $bench->getStartTime(true);

        $this->assertTrue($elapsed < 2);
        $this->assertTrue(1 < $elapsed);
    }

    public function testStartMultipleTimesShouldBeIgnored(){
        $bench = new Bench();
        $bench->start();
        sleep(1);
        $bench->start();
        $bench->stop();
        $this->assertTrue($bench->getElapsed(true) < 2);
        $this->assertTrue(1 < $bench->getElapsed(true));
    }

    function testStopMultipleTimesShouldBeIgnored(){
        $bench = new Bench();
        $bench->start();
        $bench->stop();
        sleep(1);
        $bench->stop();
        $this->assertTrue($bench->getElapsed(true) < 1);
    }

    public function testIsRunning()
    {
        $bench = new Bench();
        $running1 = $bench->isRunning();
        $bench->start();
        $running2 = $bench->isRunning();
        $bench->stop();
        $running3 = $bench->isRunning();

        $this->assertFalse($running1);
        $this->assertTrue($running2);
        $this->assertFalse($running3);
    }


    public function testGetElapsedWhenTestIsRunningShouldFail(){
        $this->expectException(LogicException::class);
        $bench = new Bench();
        $bench->start();
        $bench->getElapsed();
    }

    /*
     * Test tags
     */
    public function testAddGetTags()
    {
        $bench = new Bench();
        $bench->addTags('tag1');
        $bench->addTags(['tag2', 'tag3']);


        $this->assertEquals(['tag1', 'tag2', 'tag3'], $bench->getTags());
        $this->assertInstanceOf(Bench::class, $bench->addTags('tag4'));
    }

    public function testSetTags()
    {
        $bench = new Bench();
        $bench->addTags('tag1');
        $bench->setTags(['tag2', 'tag3']);

        $this->assertEquals(['tag2', 'tag3'], $bench->getTags());
        $this->assertInstanceOf(Bench::class, $bench->setTags('tag4'));
    }

    public function testHasTag()
    {
        $bench = new Bench();
        $bench->addTags(['tag1','tag2', 'tag3']);
        $this->assertTrue($bench->hasTag('tag1'));
        $this->assertFalse($bench->hasTag('tag4'));
    }

    public function testAddTagsTwiceShouldWork(){
        $bench = new Bench();
        $bench->addTags('tag1');
        $bench->addTags('tag1');

        $this->assertEquals(['tag1'], $bench->getTags());
    }
    public function testSetTagsTwiceShouldWork(){
        $bench = new Bench();
        $bench->setTags(['tag1', 'tag1']);

        $this->assertEquals(['tag1'], $bench->getTags());
    }

    public function testSetTagsShouldRemoveKeys(){
        $bench = new Bench();
        $bench->setTags(['tag1' => 'tag1', 'tag2' => 'tag1']);

        $this->assertEquals(['tag1'], $bench->getTags());
    }

    /*
     * Test snaps
     */
    public function testSnap()
    {
        $bench = new Bench();
        $snap1 = $bench->snap('name');
        $snap2 = $bench->snap();

        $this->assertEquals('name', $snap1->getName());
        $this->assertNull($snap2->getName());
    }

    public function testGetAllSnaps()
    {
        $bench = new Bench();
        $snap1 = $bench->snap('name');
        $bench->snap();

        $this->assertEquals(['name' => $snap1], $bench->getAllSnaps());
    }

    public function testGetSnaps()
    {
        $bench = new Bench();
        $snap = $bench->snap('name');

        $this->assertEquals($snap, $bench->getSnap('name'));
    }

    public function testHasSnap(){
        $bench = new Bench();
        $bench->snap('name');

        $this->assertTrue($bench->hasSnap('name'));
        $this->assertFalse($bench->hasSnap('undefined'));
    }

    public function testCreatingTwoSnapsWithTheSameNameShouldFail(){
        $this->expectException(InvalidArgumentException::class);
        $bench = new Bench();
        $bench->snap('name');
        $bench->snap('name');
    }

    /*
     * Test min/max
     */
    function testGetMinMemoryUsage(){
        $bench = new Bench();

        $this->assertNull($bench->getMinMemoryUsage());

        $bench->snap('name1');
        $bench->snap('name2');
        $bench->snap('name3');

        $min_memory_usage = min([$bench->getSnap('name1')->getMemoryUsage(true), $bench->getSnap('name2')->getMemoryUsage(true), $bench->getSnap('name3')->getMemoryUsage(true)]);

        $this->assertEquals($min_memory_usage, $bench->getMinMemoryUsage(true));
    }

    function testGetMaxMemoryUsage(){
        $bench = new Bench();

        $this->assertNull($bench->getMaxMemoryUsage());

        $bench->snap('name1');
        $bench->snap('name2');
        $bench->snap('name3');

        $max_memory_usage = max([$bench->getSnap('name1')->getMemoryUsage(true), $bench->getSnap('name2')->getMemoryUsage(true), $bench->getSnap('name3')->getMemoryUsage(true)]);

        $this->assertEquals($max_memory_usage, $bench->getMaxMemoryUsage(true));
    }

    function testGetMinMemoryPeak(){
        $bench = new Bench();

        $this->assertNull($bench->getMinMemoryPeak());

        $bench->snap('name1');
        $bench->snap('name2');
        $bench->snap('name3');

        $max_memory_usage = min([$bench->getSnap('name1')->getMemoryPeak(true), $bench->getSnap('name2')->getMemoryPeak(true), $bench->getSnap('name3')->getMemoryPeak(true)]);

        $this->assertEquals($max_memory_usage, $bench->getMinMemoryPeak(true));
    }

    function testGetMaxMemoryPeak(){
        $bench = new Bench();

        $this->assertNull($bench->getMaxMemoryPeak());

        $bench->snap('name1');
        $bench->snap('name2');
        $bench->snap('name3');

        $max_memory_usage = max([$bench->getSnap('name1')->getMemoryPeak(true), $bench->getSnap('name2')->getMemoryPeak(true), $bench->getSnap('name3')->getMemoryPeak(true)]);

        $this->assertEquals($max_memory_usage, $bench->getMaxMemoryPeak(true));
    }
}