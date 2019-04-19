<?php


use Doublit\TestCase;
use SitPHP\Benchmarks\Snap;

class SnapTest extends TestCase
{

    function testGetName(){
        $snap1 = new Snap('name');
        $snap2 = new Snap();

        $this->assertEquals('name', $snap1->getName());
        $this->assertNull( $snap2->getName());
    }


    public function testGetTime()
    {
        $snap = new Snap();
        $this->assertIsFloat($snap->getTime(true));
    }

    public function testGetMemoryUsage()
    {
        $snap = new Snap();
        $this->assertIsInt($snap->getMemoryUsage(true));
    }

    public function testGetMemoryPeak()
    {
        $snap = new Snap();
        $this->assertIsInt($snap->getMemoryPeak(true));
    }
}
