<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Colors;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Colors\ColorpaletteField;

class ColorpaletteFieldTest extends SapphireTest
{

    public function testFieldColorWithHash(): void
    {
        $paletteField = new ColorpaletteField('ColorField', 'Chooose a color', null, '#48E');
        $this->assertEquals('#48E', $paletteField->value);
    }


    public function testFieldColorWithNoHash(): void
    {
        $paletteField = new ColorpaletteField('ColorField', 'Chooose a color', null, '48E');
        $this->assertEquals('#48E', $paletteField->value);
    }
}
