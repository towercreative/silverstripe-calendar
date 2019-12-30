<?php

namespace TitleDK\Calendar\Tests\PageTypes;

use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Dev\FunctionalTest;
use \SilverStripe\Dev\SapphireTest;
use SilverStripe\Security\Member;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\PageTypes\CalendarPage;

class CalendarPageControllerTest extends FunctionalTest
{
    protected static $fixture_file = ['tests/registered-events.yml'];

    /** @var CalendarPage */
    private $calendarPage;

    public function setUp()
    {
        parent::setUp();
        $this->calendarPage = $this->objFromFixture(CalendarPage::class, 'calendarpageconference');

        // this is necessary to publish a page from the fixtures so that it can be seen
        $this->calendarPage->publishRecursive();
    }
    public function testInit()
    {
        $this->markTestSkipped('TODO');
    }

    public function testIndex()
    {
        $pages = SiteTree::get();
        foreach($pages as $page) {
            error_log($page->ClassName);
            error_log($page->Link());
        }

        $page = $this->get('/conference-page/');

        $this->assertEquals(200, $page->getStatusCode());
        error_log($page->getBody());
        $this->markAsRisky(); // @todo Get the correct template rendering
        //$this->assertExactHTMLMatchBySelector('title', 'wibble');
    }

    public function testUpcoming()
    {
        $this->markTestSkipped('TODO');
    }

    public function testRecent()
    {
        $this->markTestSkipped('TODO');
    }

    public function testEventlist()
    {
        $this->markTestSkipped('TODO');
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

    public function testDetail()
    {
        $this->markTestSkipped('TODO');
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
        $this->markTestSkipped('TODO');
    }

    public function testSearchEnabled()
    {
        $this->markTestSkipped('TODO');
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
        $this->markTestSkipped('TODO');
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
