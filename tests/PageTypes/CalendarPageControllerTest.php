<?php

namespace TitleDK\Calendar\Tests\PageTypes;

use Carbon\Carbon;
use SilverStripe\Dev\FunctionalTest;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\PageTypes\CalendarPage;
use TitleDK\Calendar\PageTypes\CalendarPageController;

class CalendarPageControllerTest extends FunctionalTest
{
    use DateTimeHelperTrait;

    protected static $fixture_file = ['tests/events.yml'];

    /** @var CalendarPage */
    private $calendarPage;

    /** @var CalendarPageController */
    private $calendarPageController;

    public function setUp()
    {
        parent::setUp();
        $this->calendarPage = $this->objFromFixture(CalendarPage::class, 'testcalendarpage');

        $this->calendarPageController = new CalendarPageController($this->calendarPage);

        // this is necessary to publish a page from the fixtures so that it can be seen
        $this->calendarPage->publishRecursive();

        // Because Carbon::now() is used instead of time() we can set a fixed time for testing purposes
        $testNow = $this->carbonDateTime('2019-12-15 08:00:00');
        Carbon::setTestNow($testNow);
    }
    public function testInit()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * Test a load of the index page
     */
    public function testIndex()
    {
        $page = $this->get('/test-calendar-page/');
        error_log($page->getBody());
        $this->assertEquals(200, $page->getStatusCode());
        $this->assertExactHTMLMatchBySelector('h1', ['<h1>Test Calendar Page</h1>']);
        $this->assertExactHTMLMatchBySelector('.options',
            ['<div class="options">This is a test calendar page with several events</div>']);
    }

    public function test_upcoming()
    {
        $page = $this->get('/test-calendar-page/eventlist?month=2019-12');
        error_log($page->getBody());
        $this->assertEquals(200, $page->getStatusCode());
    }

    public function testRecent()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_event_list()
    {
        $page = $this->get('/test-calendar-page/eventlist?month=2019-12');
        error_log($page->getBody());
        $this->assertEquals(200, $page->getStatusCode());
    }

    public function testRegistered()
    {
        $this->markTestSkipped('TODO');
    }

    public function testEventregistration()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCalendarview()
    {
        $this->markTestSkipped('TODO');
    }

    public function testFunction()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_detail()
    {
        $event = $this->objFromFixture(Event::class, 'eventAllDay');
        $page = $this->get('/test-calendar-page/detail/' . $event->ID);
        error_log($page->getBody());
        $this->assertEquals(200, $page->getStatusCode());
    }

    public function testTag()
    {
        $this->markTestSkipped('TODO');
    }

    public function testRegister()
    {
        $this->markTestSkipped('TODO');
    }

    public function testRegistrationsEnabled()
    {
        $this->assertFalse($this->calendarPageController->RegistrationsEnabled());
    }

    public function test_search_enabled()
    {
        $this->assertTrue($this->calendarPageController->SearchEnabled());
    }

    public function testEvents()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCurrentCalendar()
    {
        $this->markTestSkipped('TODO');
    }

    public function testEventPageTitle()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCurrentMonth()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCurrentMonthDay()
    {
        $this->markTestSkipped('TODO');
    }

    public function testNextMonthDay()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCurrentMonthStr()
    {
        $this->markTestSkipped('TODO');
    }

    public function testNextMonth()
    {
        $this->markTestSkipped('TODO');
    }

    public function testNextMonthLink()
    {
        $this->markTestSkipped('TODO');
    }

    public function testPrevMonth()
    {
        $this->markTestSkipped('TODO');
    }

    public function testPrevMonthLink()
    {
        $this->markTestSkipped('TODO');
    }

    public function testEventListLink()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCalendarViewLink()
    {
        $this->markTestSkipped('TODO');
    }

    public function testSearchQuery()
    {
        $page = $this->get('/test-calendar-page/search?q=SilverStripe');
        $this->assertEquals(200, $page->getStatusCode());

        error_log($page->getBody());

    }

    public function testAllCalendars()
    {
        $this->markTestSkipped('TODO');
    }

    public function testFeedLink()
    {
        $this->markTestSkipped('TODO');
    }
}
