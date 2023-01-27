<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Helpers;

use Carbon\Carbon;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Core\CalendarHelper;
use TitleDK\Calendar\DateTime\DateTimeHelper;
use TitleDK\Calendar\PageTypes\CalendarPage;

class CalendarPageHelperTest extends SapphireTest
{

    use DateTimeHelper;

    protected static $fixture_file = ['../events.yml'];

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


    public function testRealtimeMonthDay(): void
    {
        $this->assertEquals('2019-12-15', $this->helper->realtimeMonthDay());
    }


    public function testRealtimeMonth(): void
    {
        $this->assertEquals('2019-12', $this->helper->realtimeMonth());
    }


    public function testCurrentContextualMonth(): void
    {
        $this->assertEquals('2019-12', $this->helper->currentContextualMonth());
    }


    public function testCurrentContextualMonthString(): void
    {
        $this->assertEquals('Dec 2019', $this->helper->currentContextualMonthStr());
    }


    public function testPreviousContextualMonth(): void
    {
        $this->assertEquals('2019-11', $this->helper->previousContextualMonth());
    }


    public function textNextContextualMonth(): void
    {
        $this->assertEquals('2020-01', $this->helper->nextContextualMonth());
    }


    public function testPerformSearchStartOfTitleCamelCase(): void
    {
        $titles = $this->getEventTitlesForSearch('SilverSt');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);
    }


    public function testPerformSearchStartOfTitleLowerCase(): void
    {
        $titles = $this->getEventTitlesForSearch('silverstr');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);
    }


    public function testPerformSearchMultipleWordsLowerCase(): void
    {
        $titles = $this->getEventTitlesForSearch('silverstri booz');
        $this->assertEquals(['SilverStripe Booze Up'], $titles);
    }


    public function testPerformSearchMiddleOfTitleCamelCase(): void
    {
        $titles = $this->getEventTitlesForSearch('verStr');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);

        $titles = $this->getEventTitlesForSearch('Booze');
        $this->assertEquals(['SilverStripe Booze Up'], $titles);
    }


    public function testPerformSearchMiddleOfTitleLowerCase(): void
    {
        $titles = $this->getEventTitlesForSearch('verstr');
        $this->assertEquals(['SilverStripe Booze Up', 'SilverStripe Meet Up'], $titles);

        $titles = $this->getEventTitlesForSearch('booze');
        $this->assertEquals(['SilverStripe Booze Up'], $titles);
    }


    public function testRecentEvents(): void
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


    public function testUpcomingEvents(): void
    {
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
     * @param string $q the search query
     * @return array<string>
     */
    private function getEventTitlesForSearch(string $q): array
    {
        $events = $this->helper->performSearch($q)->toArray();

        return $this->convertEventsToTitles($events);
    }


    /**
     * @param array<\TitleDK\Calendar\Helpers\Event> $events
     * @return array<string>
     */
    private function convertEventsToTitles(array $events): array
    {
        return \array_map(static function ($event) {
            return $event->Title;
        }, $events);
    }
}
