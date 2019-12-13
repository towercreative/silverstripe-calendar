<?php
namespace TitleDK\Calendar\Tests\Events;


use Carbon\Carbon;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;

class EventTest extends SapphireTest {
    use DateTimeHelperTrait;

    /** @var Event  */
    private $event;

    public function setUp()
    {
        parent::setUp();

        // fix the concept of now for testing purposes
        $this->now = Carbon::create(2018, 5, 16, 8);
        Carbon::setTestNow($this->now);

        /** @var Event event */
        $this->event = new Event();
        $this->event->Title = 'Test Event Title';
        $this->event->Details = 'This is detail about the test event title';
        $this->event->StartDateTime = $this->getSSDateTimeFromCarbon($this->now);

    }

    public function test_details_summary()
    {
        $this->assertEquals('This is detail about the test event title', $this->event->DetailsSummary());
    }

    public function test_event_page_title_no_calendar_page()
    {
        $this->assertEquals('-', $this->event->getEventPageCalendarTitle());
    }

    public function test_calc_end_date_time_based_on_duration()
    {
        // @todo Not sure how to set this
        $this->event->Duration->setValue('05:30:00');
        $this->event->write();
        $this->assertEquals('2018-05-16 08:45:00', $this->event->calcEndDateTimeBasedOnDuration());
    }

    public function test_is_all_day()
    {

    }
}
