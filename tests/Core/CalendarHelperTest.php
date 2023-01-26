<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Core;

use Carbon\Carbon;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Calendars\Calendar;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelper;
use TitleDK\Calendar\Events\Event;

class CalendarHelperTest extends FunctionalTest
{

    use DateTimeHelper;

    protected static $fixture_file = 'tests/events.yml';

    public function setUp(): void
    {
        parent::setUp();

        $now = $this->carbonDateTime('2019-12-30 20:00:00');
        Carbon::setTestNow($now);
    }


    public function testGetValidCalendarIDsForCurrentUser(): void
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


    public function testGetValidCalendarIDsForLoggedOut(): void
    {
        $publicCalendar = $this->objFromFixture(Calendar::class, 'testPublicCalendar');

        $this->logOut();
        $ids = CalendarHelper::getValidCalendarIDsForCurrentUser(Calendar::get());
        $this->assertEquals([$publicCalendar->ID], $ids);
    }


    public function testComingEventsDateSpecified(): void
    {
        $events = CalendarHelper::comingEvents('2019-12-30 20:00:00');
        $this->assertEquals(3, $events->count());
    }


    public function testComingEventsDateSpecifiedUseCarbonNow(): void
    {
        $events = CalendarHelper::comingEvents();
        $this->assertEquals(3, $events->count());
    }


    public function testComingEventsLimited(): void
    {
        $events = CalendarHelper::comingEventsLimited(false, 1);
        $this->assertEquals(1, $events->count());
    }


    public function testPastEvents(): void
    {
        $events = CalendarHelper::pastEvents();
        $this->assertEquals(8, $events->count());
    }


    public function testAllEvents(): void
    {
        $events = Event::get();
        $allEvents = CalendarHelper::allEvents();
        $this->assertEquals($events->count(), $allEvents->count());
    }


    public function testAllEventsLimited(): void
    {
        $allEvents = CalendarHelper::allEventsLimited(4);
        $this->assertEquals(4, $allEvents->count());
    }


    public function testEventsForMonthDecember(): void
    {
        $events = CalendarHelper::eventsForMonth('2019-12');
        $this->assertEquals(7, $events->count());
    }


    public function testEventsForMonthDecemberWithCalendarIDs(): void
    {
        $calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
        $events = CalendarHelper::eventsForMonth('2019-12', [$calendar->ID]);

        // the new year event is not associated with this calendar
        $this->assertEquals(6, $events->count());
    }


    public function testEventsForMonthDecemberWithCalendarIDsNonArray(): void
    {
        try {
            $calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
            CalendarHelper::eventsForMonth('2019-12', "{$calendar->ID}");
            $this->fail('String calendar IDs should fail as expected');
        } catch (\Throwable $ex) {
            // @todo Can one add a success method here?
        }
    }



    // @todo What is the expected behaviour here?
    public function testEventsForMonthInLongEvent(): void
    {
        // during the cricket season event
        $events = CalendarHelper::eventsForMonth('2020-06');
        $this->assertEquals(0, $events->count());
    }


    public function testEventsForDateRange(): void
    {
        $calendar = $this->objFromFixture(Calendar::class, 'testCalendar1');
        $events = CalendarHelper::eventsForDateRange(
            '2019-10-01',
            '2019-11-01',
            [$calendar->ID],
        );
        $this->assertEquals(1, $events->count());
    }


    public function testAddPreviewParametersNoMember(): void
    {
        $this->logout();
        $link = 'http://localhost/calendar/';
        $this->assertEquals($link, CalendarHelper::addPreviewParams($link, null));
    }


    public function testAddPreviewParametersLoggedIn(): void
    {
        $this->markTestSkipped('Not sure how to test this');
    }
}
