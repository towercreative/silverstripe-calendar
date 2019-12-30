<?php

namespace TitleDK\Calendar\Tests\Colors;

use \SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Colors\ColorpaletteField;

class ColorpaletteFieldTest extends SapphireTest
{

    public function testFieldColorWithHash()
    {
        $paletteField = new ColorpaletteField('ColorField', 'Chooose a color', null, '#48E');
        $field = $paletteField->Field([]);
        $this->assertEquals('#48E', $paletteField->value);
    }

    public function testFieldColorWithNoHash()
    {
        $paletteField = new ColorpaletteField('ColorField', 'Chooose a color', null, '48E');
        $field = $paletteField->Field([]);
        $this->assertEquals('#48E', $paletteField->value);
    }
}
