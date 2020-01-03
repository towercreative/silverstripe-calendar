<?php

namespace TitleDK\Calendar\Tests\Core;

use Carbon\Carbon;
use SebastianBergmann\CodeCoverage\Exception;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;

class CalendarHelperTest extends SapphireTest
{
    use DateTimeHelperTrait;

    protected static $fixture_file = 'tests/events.yml';

    public function setUp()
    {
        parent::setUp();
        $now = $this->carbonDateTime('2019-12-30 20:00:00');
        Carbon::setTestNow($now);
    }

    public function testGetValidCalendarIDsForCurrentUser()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_coming_events_date_specified()
    {
        $events = CalendarHelper::coming_events('2019-12-30 20:00:00');
        $this->debugEvents($events);
        $this->assertEquals(3, $events->count());
    }

    public function test_coming_events_date_specified_use_carbon_now()
    {
        $events = CalendarHelper::coming_events();
        $this->debugEvents($events);
        $this->assertEquals(3, $events->count());
    }

    public function test_coming_events_limited()
    {
        $events = CalendarHelper::coming_events_limited(false, 1);
        $this->debugEvents($events);
        $this->assertEquals(1, $events->count());
    }

    public function test_past_events()
    {
        $events = CalendarHelper::past_events();
        $this->debugEvents($events);
        $this->assertEquals(7, $events->count());
    }

    public function test_all_events()
    {
        $events = Event::get();
        $allEvents = CalendarHelper::all_events();
        $this->assertEquals($events->count(), $allEvents->count());
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

    public function test_events_for_month_december_with_calendar_ids()
    {
        $calendar = $this->objFromFixture(Calendar::class, 'testCalendar');
        $events = CalendarHelper::events_for_month('2019-12', [$calendar->ID]);
        $this->debugEvents($events);
        $this->assertEquals(7, $events->count());
    }

    public function test_events_for_month_december_with_calendar_id_non_array()
    {
        try {
            $calendar = $this->objFromFixture(Calendar::class, 'testCalendar');
            $events = CalendarHelper::events_for_month('2019-12', "{$calendar->ID}");
            $this->fail('String calendar IDs should fail as expected');
        } catch (\Exception $ex) {
            // @todo Can one add a success method here?
        }

    }



    // @todo What is the expected behaviour here?
    public function test_events_for_month_in_long_event()
    {
        // during the cricket season event
        $events = CalendarHelper::events_for_month('2020-06');
        $this->debugEvents($events);
        $this->assertEquals(0, $events->count());
    }

    public function test_events_for_date_range()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_add_preview_params()
    {
        $this->markTestSkipped('TODO');
    }


    private function debugEvents($events)
    {
        foreach ($events as $event) {
          //  error_log($event->Title . ' ' . $event->StartDateTime . ' --> ' . $event->EndDateTime);
        }
    }
}
