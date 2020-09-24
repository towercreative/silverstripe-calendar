<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Core;

use Carbon\Carbon;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;

class CalendarHelperTest extends FunctionalTest
{

    use DateTimeHelperTrait;

    protected static $fixture_file = 'tests/events.yml';

    public function setUp(): void
    {
        parent::setUp();

        $now = $this->carbonDateTime('2019-12-30 20:00:00');
        Carbon::setTestNow($now);
    }


    public function test_get_valid_calendar_ids_for_current_user(): void
    {
        $publicCalendar = $this->objFromFixture(Calendar::class, 'testPublicCalendar');
        $calendar1 = $this->objFromFixture(Calendar::class, 'testCalendar1');
        $calendar2 = $this->objFromFixture(Calendar::class, 'testCalendar2');
        $member = $this->objFromFixture(Member::class, 'member1');
        $this->logInAs($member);
        $ids = CalendarHelper::getValidCalendarIDsForCurrentUser(Calendar::get());
        $this->assertEquals([$publicCalendar->ID, $calendar1->ID], $ids);

        $member = $this->objFromFixture(Member::class, 'member2');
        $this->logInAs($member);
        $ids = CalendarHelper::getValidCalendarIDsForCurrentUser(Calendar::get());
        $this->assertEquals([$publicCalendar->ID, $calendar2->ID], $ids);

        $csv = $ids = CalendarHelper::getValidCalendarIDsForCurrentUser(Calendar::get(), true);
        $expected = $publicCalendar->ID . ',' . $calendar2->ID;
        $this->assertEquals($expected, $csv);
    }


    public function test_get_valid_calendar_ids_for_logged_out(): void
    {
        $publicCalendar = $this->objFromFixture(Calendar::class, 'testPublicCalendar');

        $this->logOut();
        $ids = CalendarHelper::getValidCalendarIDsForCurrentUser(Calendar::get());
        $this->assertEquals([$publicCalendar->ID], $ids);
    }


    public function test_coming_events_date_specified(): void
    {
        $events = CalendarHelper::coming_events('2019-12-30 20:00:00');
        $this->debugEvents($events);
        $this->assertEquals(3, $events->count());
    }


    public function test_coming_events_date_specified_use_carbon_now(): void
    {
        $events = CalendarHelper::coming_events();
        $this->debugEvents($events);
        $this->assertEquals(3, $events->count());
    }


    public function test_coming_events_limited(): void
    {
        $events = CalendarHelper::coming_events_limited(false, 1);
        $this->debugEvents($events);
        $this->assertEquals(1, $events->count());
    }


    public function test_past_events(): void
    {
        $events = CalendarHelper::past_events();
        $this->debugEvents($events);
        $this->assertEquals(8, $events->count());
    }


    public function test_all_events(): void
    {
        $events = Event::get();
        $allEvents = CalendarHelper::all_events();
        $this->assertEquals($events->count(), $allEvents->count());
    }


    public function test_all_events_limited(): void
    {
        $allEvents = CalendarHelper::all_events_limited(4);
        $this->assertEquals(4, $allEvents->count());
    }


    public function test_events_for_month_december(): void
    {
        $events = CalendarHelper::events_for_month('2019-12');
        $this->debugEvents($events);
        $this->assertEquals(7, $events->count());
    }


    public function test_events_for_month_december_with_calendar_ids(): void
    {
        $calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
        $events = CalendarHelper::events_for_month('2019-12', [$calendar->ID]);
        $this->debugEvents($events);

        // the new year event is not associated with this calendar
        $this->assertEquals(6, $events->count());
    }


    public function test_events_for_month_december_with_calendar_id_non_array(): void
    {
        try {
            $calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
            $events = CalendarHelper::events_for_month('2019-12', "{$calendar->ID}");
            $this->fail('String calendar IDs should fail as expected');
        } catch (\Throwable $ex) {
            // @todo Can one add a success method here?
        }
    }



    // @todo What is the expected behaviour here?
    public function test_events_for_month_in_long_event(): void
    {
        // during the cricket season event
        $events = CalendarHelper::events_for_month('2020-06');
        $this->debugEvents($events);
        $this->assertEquals(0, $events->count());
    }


    public function test_events_for_date_range(): void
    {
        $calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
        $events = CalendarHelper::events_for_date_range(
            '2019-10-01',
            '2019-11-01',
            [$calendar->ID],
        );
        $this->assertEquals(1, $events->count());
        $this->debugEvents($events);
    }


    public function test_add_preview_params_no_member(): void
    {
        $this->logout();
        $link = 'http://localhost/calendar/';
        $this->assertEquals($link, CalendarHelper::add_preview_params($link, null));
    }


    public function test_add_preview_params_logged_in(): void
    {
        $this->markTestSkipped('Not sure how to test this');
    }


    private function debugEvents($events): void
    {
        foreach ($events as $event) {
           // error_log($event->Title . ' ' . $event->StartDateTime . ' --> ' . $event->EndDateTime);
        }
    }
}
