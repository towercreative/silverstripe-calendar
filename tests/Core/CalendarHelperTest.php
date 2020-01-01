<?php

namespace TitleDK\Calendar\Tests\Core;

use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\Events\Event;

class CalendarHelperTest extends SapphireTest
{
    protected static $fixture_file = 'tests/events.yml';

    public function testGetValidCalendarIDsForCurrentUser()
    {
        $this->markTestSkipped('TODO');
    }

    public function testComing_events()
    {
    }

    public function testComing_events_limited()
    {
        $this->markTestSkipped('TODO');
    }

    public function testPast_events()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_all_events()
    {
        $events = Event::get();
        $allEvents = CalendarHelper::all_events();
        $this->assertEquals($events->count(), $allEvents->count());
        $this->debugEvents($events);
    }

    public function test_all_events_limited()
    {
        $allEvents = CalendarHelper::all_events_limited(4);
        $this->assertEquals(4, $allEvents->count());
    }

    public function test_events_for_month_december()
    {
        $events = CalendarHelper::events_for_month('2019-12');
        $this->debugEvents($events);
        $this->assertEquals(7, $events->count());
    }

    // @todo What is the expected behaviour here?
    public function test_events_for_month_in_long_event()
    {
        // during the cricket season event
        $events = CalendarHelper::events_for_month('2020-06');
        $this->debugEvents($events);
        $this->assertEquals(0, $events->count());
    }

    public function testEvents_for_date_range()
    {
        $this->markTestSkipped('TODO');
    }

    public function testAdd_preview_params()
    {
        $this->markTestSkipped('TODO');
    }


    private function debugEvents($events)
    {
        foreach ($events as $event) {
            //error_log($event->Title . ' ' . $event->StartDateTime . ' --> ' . $event->EndDateTime);
        }
    }
}
