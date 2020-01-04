<?php

namespace TitleDK\Calendar\Tests\Libs;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Libs\ICSExport;

class ICSExportTest extends SapphireTest
{
    protected static $fixture_file = ['tests/events.yml', 'tests/eventpages.yml'];

    /** @var Calendar */
    private $calendar;

    /** @var Member */
    private $member;

    public function setUp()
    {
        parent::setUp();
        $this->member = $this->objFromFixture(Member::class, 'member1');
        $this->calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
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
        $this->logInAs($this->member);
        $eventsArr = $this->createEventsArray();
        $calendar = Calendar::get()->first();
        $ics = new ICSExport($eventsArr);

        // @todo Fix this test
        $this->markTestSkipped('Need to fix this with respect to groupings');

        //$this->assertEquals($ics->getString(), ICSExport::ics_from_sscal($calendar)->getString());
    }



    /**
     * @return array
     */
    private function createEventsArray()
    {
        $calendars = Calendar::get();
        $events = new ArrayList();
        foreach ($calendars as $cal) {
            $events->merge($cal->Events());
        }

        $eventsArr = $events->toNestedArray();
        return $eventsArr;
    }
}
