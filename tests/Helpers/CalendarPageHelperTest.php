<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Helpers;

use Carbon\Carbon;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\PageTypes\CalendarPage;

class CalendarPageHelperTest extends SapphireTest
{

    use DateTimeHelperTrait;

    protected static $fixture_file = ['tests/events.yml'];

    /** @var \TitleDK\Calendar\Helpers\CalendarPageHelper */
    private $helper;

   /** @var \TitleDK\Calendar\PageTypes\CalendarPage */
    private $calendarPage;

    public function setUp(): void
    {
        parent::setUp();

        $this->helper = new CalendarPageHelper();
        $this->calendarPage = $this->objFromFixture(CalendarPage::class, 'testcalendarpage');

        // Because Carbon::now() is used instead of time() we can set a fixed time for testing purposes
        $testNow = $this->carbonDateTime('2019-12-15 08:00:00');
        Carbon::setTestNow($testNow);
    }


    public function test_realtime_month_day(): void
    {
        $this->assertEquals('2019-12-15', $this->helper->realtimeMonthDay());
    }


    public function test_realtime_month(): void
    {
        $this->assertEquals('2019-12', $this->helper->realtimeMonth());
    }


    public function test_current_contextual_month(): void
    {
        $this->assertEquals('2019-12', $this->helper->currentContextualMonth());
    }


    public function test_current_contextual_month_str(): void
    {
        $this->assertEquals('Dec 2019', $this->helper->currentContextualMonthStr());
    }


    public function test_previous_contextual_month(): void
    {
        $this->assertEquals('2019-11', $this->helper->previousContextualMonth());
    }


    public function test_next_contextual_month(): void
    {
        $this->assertEquals('2020-01', $this->helper->nextContextualMonth());
    }


    public function test_perform_search_start_of_title_camel_case(): void
    {
        $titles = $this->getEventTitlesForSearch('SilverSt');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);
    }


    public function test_perform_search_start_of_title_lower_case(): void
    {
        $titles = $this->getEventTitlesForSearch('silverstr');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);
    }


    public function test_perform_search_multiple_words_lower_case(): void
    {
        $titles = $this->getEventTitlesForSearch('silverstri booz');
        $this->assertEquals(['SilverStripe Booze Up'], $titles);
    }


    public function test_perform_search_middle_of_title_camel_case(): void
    {
        $titles = $this->getEventTitlesForSearch('verStr');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);

        $titles = $this->getEventTitlesForSearch('Booze');
        $this->assertEquals(['SilverStripe Booze Up'], $titles);
    }


    public function test_perform_search_middle_of_title_lower_case(): void
    {
        $titles = $this->getEventTitlesForSearch('verstr');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);

        $titles = $this->getEventTitlesForSearch('booze');
        $this->assertEquals(['SilverStripe Booze Up'], $titles);
    }


    public function test_recent_events(): void
    {
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->calendarPage->Calendars());
        $events = $this->helper->recentEvents($calendarIDs);
        $titles = $this->convertEventsToTitles($events->toArray());
        $this->assertEquals([
            'Freezing in the Park',
            'Blink And You Will Miss It',
            'Blink And You Will Miss It 2',
            'The Neverending Event',
        ], $titles);
    }


    public function test_upcoming_events(): void
    {
        $member = $this->objFromFixture(Member::class, 'member1');
        $this->logInAs('member1');
        $calendarIDs = CalendarHelper::getValidCalendarIDsForCurrentUser($this->calendarPage->Calendars());
        $events = $this->helper->upcomingEvents($calendarIDs);
        $titles = $this->convertEventsToTitles($events->toArray());
        $this->assertEquals([
            'Freezing in the Park',
            'SilverStripe Booze Up',
            'SilverStripe Meet Up',
        ], $titles);
    }


    /**
     * @param $q
     * @return array
     */
    private function getEventTitlesForSearch($q): array
    {
        $events = $this->helper->performSearch($q)->toArray();

        return $this->convertEventsToTitles($events);
    }


    /**
     * @param array $events
     * @return array
     */
    private function convertEventsToTitles(array $events): array
    {
        $titles = \array_map(static fn ($event) => $event->Title, $events);

        return $titles;
    }
}
