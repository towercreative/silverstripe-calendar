<?php

namespace TitleDK\Calendar\Tests\Colors;

use \SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Colors\ColorpaletteHelper;

class ColorpaletteHelperTest extends SapphireTest
{
    public function testRequirements()
    {
        //$this->markTestSkipped('TODO');
    }

    public function test_palette_dropdown()
    {
        $dropdown = ColorpaletteHelper::palette_dropdown('Colors');
        $this->assertEquals([
            '#4B0082',
            '#696969',
            '#B22222',
            '#A52A2A',
            '#DAA520',
            '#006400',
            '#40E0D0',
            '#0000CD',
            '#800080'
        ], $dropdown->getValidValues());
    }

    public function testGet_palette()
    {
        $this->markTestSkipped('TODO');
    }
}
