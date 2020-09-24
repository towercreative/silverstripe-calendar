<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Colors;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Colors\ColorHelper;

class ColorHelperTest extends SapphireTest
{
    /** @var \TitleDK\Calendar\Colors\ColorHelper */
    private $helper;

    public function setUp(): void
    {
        parent::setUp();

        $this->helper = new ColorHelper();
    }


    public function testLightBackgroundsForTextColor(): void
    {
        $this->assertEquals('#000', $this->helper->calculate_textcolor('#AABBCC'));
        $this->assertEquals('#000', $this->helper->calculate_textcolor('#FFFFFF'));
        $this->assertEquals('#000', $this->helper->calculate_textcolor('#808080'));
    }


    public function testDarkBackgroundsForTextColor(): void
    {
        $this->assertEquals('#fff', $this->helper->calculate_textcolor('#000000'));
        $this->assertEquals('#fff', $this->helper->calculate_textcolor('#444444'));
        $this->assertEquals('#fff', $this->helper->calculate_textcolor('#7F7F7F'));
    }
}
