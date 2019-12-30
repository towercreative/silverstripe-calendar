<?php

namespace TitleDK\Calendar\Tests\Libs;

use \SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\ArrayList;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Libs\ICSExport;

class ICSExportTest extends SapphireTest
{
    protected static $fixture_file = ['tests/events.yml', 'tests/eventpages.yml'];

    /** @var Calendar */
    private $calendar;

    public function setUp()
    {
        parent::setUp();
        $this->calendar = $this->objFromFixture(Calendar::class, 'testCalendar');
    }

    public function test__construct()
    {
    }

    public function testGetFile()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetString()
    {
        $eventsArr = $this->createEventsArray();

        $ics = new ICSExport($eventsArr);
        error_log($ics->getString());


        // @todo check the output - note there is a timestamp issue which changes each time the tests are run
        $this->markAsRisky();
    }

    public function testIcs_date()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCleanString()
    {
        $src = "wibble<br/>wobble";
        $this->assertEquals("wibble\\nwobble", ICSExport::cleanString($src));
    }

    public function testIcs_from_sscal()
    {
        $eventsArr = $this->createEventsArray();
        $calendar = Calendar::get()->first();
        $ics = new ICSExport($eventsArr);
        $this->assertEquals($ics->getString(), ICSExport::ics_from_sscal($calendar)->getString());
    }



    /**
     * @return array
     */
    private function createEventsArray()
    {
        $calendars = Calendar::get();
        error_log('N CALENDARS: ' . $calendars->count());
        $events = new ArrayList();
        foreach ($calendars as $cal) {
            $events->merge($cal->Events());
        }

        $eventsArr = $events->toNestedArray();
        return $eventsArr;
    }
}
