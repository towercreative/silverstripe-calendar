<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Helpers;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\ArrayList;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Calendars\Calendar;

class ICSExportHelperTest extends SapphireTest
{
    protected static $fixture_file = ['tests/events.yml', 'tests/eventpages.yml'];

    /** @var \TitleDK\Calendar\Calendars\Calendar */
    private $calendar;

    /** @var \SilverStripe\Security\Member */
    private $member;

    /** @var \TitleDK\Calendar\Helpers\ICSExportHelper */
    private $helper;

    public function setUp(): void
    {
        parent::setUp();

        $this->member = $this->objFromFixture(Member::class, 'member1');
        $this->calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
        $this->helper = new ICSExportHelper();
    }


    public function testProcessCalendar(): void
    {
        $this->logInAs($this->member);
        $this->helper->processCalendar($this->calendar);
        // @todo How best to test this - manually it imports into Lightning on Thunderbird
    }


    public function testGetString(): void
    {
        $this->logInAs($this->member);
        $ics = $this->helper->processCalendar($this->calendar);
        $this->assertEquals($ics, $this->helper->getString());
    }


    public function testGetFile(): void
    {
        $this->logInAs($this->member);
        $this->helper->processCalendar($this->calendar);
       // $file = $this->helper->getFile('test.ics', false);
        $this->markAsRisky();
    }


    /** @return array<\TitleDK\Calendar\Events\Event> */
    private function createEventsArray(): array
    {
        $calendars = Calendar::get();
        $events = new ArrayList();
        foreach ($calendars as $cal) {
            $events->merge($cal->Events());
        }

        return $events->toNestedArray();
    }
}
