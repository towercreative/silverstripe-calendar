<?php

namespace TitleDK\Calendar\Tests\Libs\ColorPool;

use \SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Libs\ColorPool\Color;

class ColorTest extends SapphireTest
{
    public function test__construct_param_not_string()
    {
        $c = new Color(444);
        $this->assertNull($c->r);
        $this->assertNull($c->g);
        $this->assertNull($c->b);
    }

    public function test__construct_param_string()
    {
        $c = new Color("rgb\t428");
        $this->assertEquals('', $c->r);
        $this->assertEquals('', $c->g);
        $this->assertEquals('', $c->b);
    }

    public function testFromHexString()
    {
        $this->markTestSkipped('TODO');
    }

    public function testFromRGBString()
    {
        $this->markTestSkipped('TODO');
    }

    public function testToHexString()
    {
        $this->markTestSkipped('TODO');
    }

    public function testDecToHex()
    {
        $this->markTestSkipped('TODO');
    }

    public function testFromHSL()
    {
        $this->markTestSkipped('TODO');
    }

    public function testFromHSV()
    {
        $this->markTestSkipped('TODO');
    }

    public function testDarken()
    {
        $this->markTestSkipped('TODO');
    }

    public function testLighten()
    {
        $this->markTestSkipped('TODO');
    }

    public function testSaturate()
    {
        $this->markTestSkipped('TODO');
    }

    public function testContrast()
    {
        $this->markTestSkipped('TODO');
    }

    public function testChangeHSL()
    {
        $this->markTestSkipped('TODO');
    }

    public function testApply()
    {
        $this->markTestSkipped('TODO');
    }

    public function testToHSL()
    {
        $this->markTestSkipped('TODO');
    }

    public function testToHSV()
    {
        $this->markTestSkipped('TODO');
    }

    public function testLuma()
    {
        $this->markTestSkipped('TODO');
    }

    public function testIsDark()
    {
        $this->markTestSkipped('TODO');
    }
}
