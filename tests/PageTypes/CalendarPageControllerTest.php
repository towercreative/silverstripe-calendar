<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\PageTypes;

use Carbon\Carbon;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Security\Member;
use TitleDK\Calendar\DateTime\DateTimeHelper;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\PageTypes\CalendarPage;
use TitleDK\Calendar\PageTypes\CalendarPageController;

class CalendarPageControllerTest extends FunctionalTest
{

    use DateTimeHelper;

    protected static $fixture_file = ['../events.yml'];

    /** @var \TitleDK\Calendar\PageTypes\CalendarPage */
    private $calendarPage;

    /** @var \TitleDK\Calendar\PageTypes\CalendarPageController */
    private $calendarPageController;

    /** @var \SilverStripe\Security\Member */
    private $member;

    public function setUp(): void
    {
        parent::setUp();

        $this->calendarPage = $this->objFromFixture(CalendarPage::class, 'testcalendarpage');

        $this->calendarPageController = CalendarPageController::create($this->calendarPage);

        // this is necessary to publish a page from the fixtures so that it can be seen
        $this->calendarPage->publishRecursive();

        // Because Carbon::now() is used instead of time() we can set a fixed time for testing purposes
        $testNow = $this->carbonDateTime('2019-12-15 08:00:00');
        Carbon::setTestNow($testNow);

        $this->member = $this->objFromFixture(Member::class, 'member1');
    }


    public function testInit(): void
    {
        $this->markTestSkipped('TODO');
    }


    /**
     * Test a load of the index page
     */
    public function testIndex(): void
    {
        $this->logInAs($this->member);
        $page = $this->get('/test-calendar-page/');
        $this->assertEquals(200, $page->getStatusCode());
        $this->assertExactHTMLMatchBySelector('h1', ['<h1>Test Calendar Page</h1>']);
        $this->assertExactHTMLMatchBySelector(
            '.options',
            ['<div class="options">This is a test calendar page with several events</div>'],
        );
    }


    public function testUpcoming(): void
    {
        $page = $this->get('/test-calendar-page/eventlist?month=2019-12');
        $this->assertEquals(200, $page->getStatusCode());
    }


    public function testRecent(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testEventList(): void
    {
        $page = $this->get('/test-calendar-page/eventlist?month=2019-12');
        $this->assertEquals(200, $page->getStatusCode());
    }


    public function testRegistered(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testEventregistration(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testCalendarview(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testFunction(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testDetail(): void
    {
        $event = $this->objFromFixture(Event::class, 'eventAllDay');
        $page = $this->get('/test-calendar-page/detail/' . $event->ID);
        $this->assertEquals(200, $page->getStatusCode());
    }


    public function testTag(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testRegister(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testRegistrationsEnabled(): void
    {
        $this->assertFalse($this->calendarPageController->RegistrationsEnabled());
    }


    public function testSearchEnabled(): void
    {
        $this->assertTrue($this->calendarPageController->SearchEnabled());
    }


    public function testEvents(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testCurrentCalendar(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testEventPageTitle(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testCurrentMonth(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testCurrentMonthDay(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testNextMonthDay(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testCurrentMonthStr(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testNextMonth(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testNextMonthLink(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testPrevMonth(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testPrevMonthLink(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testEventListLink(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testCalendarViewLink(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testSearchQuery(): void
    {
        $page = $this->get('/test-calendar-page/search?q=SilverStripe');
        $this->assertEquals(200, $page->getStatusCode());
    }


    public function testAllCalendars(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testFeedLink(): void
    {
        $this->markTestSkipped('TODO');
    }
}
