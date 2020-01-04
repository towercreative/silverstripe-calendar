<?php

namespace TitleDK\Calendar\Helpers;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Calendars\Calendar;

class ICSExportHelperTest extends SapphireTest
{
    protected static $fixture_file = ['tests/events.yml', 'tests/eventpages.yml'];

    /** @var Calendar */
    private $calendar;

    /** @var Member */
    private $member;

    /** @var ICSExportHelper */
    private $helper;

    public function setUp()
    {
        parent::setUp();
        $this->member = $this->objFromFixture(Member::class, 'member1');
        $this->calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
        $this->helper = new ICSExportHelper();
    }


    public function test_process_calendar()
    {
        $this->logInAs($this->member);
        $ics = $this->helper->processCalendar($this->calendar);
        // @todo How best to test this - manually it imports into Lightning on Thunderbird
    }

    public function test_get_string()
    {
        $this->logInAs($this->member);
        $ics = $this->helper->processCalendar($this->calendar);
        $this->assertEquals($ics, $this->helper->getString());
    }

    public function test_get_file()
    {
        $this->markTestSkipped('TODO');
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
