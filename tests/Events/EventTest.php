<?php
namespace TitleDK\Calendar\Tests\Events;


use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;

class EventTest extends SapphireTest {
    /** @var Event  */
    private $event;

    public function setUp()
    {
        parent::setUp();
        /** @var Event event */
        $this->event = new Event();
        $this->event->Title = 'Test Event Title';
        $this->event->Details = 'This is detail about the test event title';
    }

    public function test_details_summary()
    {
        $this->assertEquals('This is detail about the test event title', $this->event->DetailsSummary());
    }

    public function test_event_page_title_no_calendar_page()
    {
        $this->assertEquals('-', $this->event->getEventPageCalendarTitle());
    }
}
