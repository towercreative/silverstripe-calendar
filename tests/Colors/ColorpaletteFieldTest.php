<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Colors;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Colors\ColorpaletteField;

class ColorpaletteFieldTest extends SapphireTest
{

    public function testFieldColorWithHash(): void
    {
        $paletteField = ColorpaletteField::create('ColorField', 'Chooose a color', null, '#48E');
        $this->assertEquals('#48E', $paletteField->value);
    }


    public function testFieldColorWithNoHash(): void
    {
        $paletteField = ColorpaletteField::create('ColorField', 'Choose a color', null, '48E');
        $this->assertEquals('#48E', $paletteField->Value());
    }
}
