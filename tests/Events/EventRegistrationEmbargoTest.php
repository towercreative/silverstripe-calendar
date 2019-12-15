<?php
namespace TitleDK\Calendar\Tests\Events;


use Carbon\Carbon;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;

class EventRegistrationEmbargoTest extends SapphireTest {
    /** @var Carbon */
    protected $now;

    /** @var Event */
    protected $event;

    use DateTimeHelperTrait;


    public function setUp()
    {
        parent::setUp();
        $this->now = Carbon::create(2018, 5, 16, 8);
        Carbon::setTestNow($this->now);

        /** @var Event event */
        $this->event = new Event();
        $this->event->Title = 'Test Event Title';
        $this->event->Details = 'This is detail about the test event title';
       // $this->event->startDateTime = '2018-05-10 16:20';
        error_log('TIME: ' . $this->now->format('Y-m-d H:i:s'));
        $this->getSSDateTimeFromCarbon($this->now);
        $this->event->StartDateTime = $this->getSSDateTimeFromCarbon($this->now);
        $this->event->EndDate = $this->getSSDateTimeFromCarbon($this->now->addHours(6));
        $this->event->write();
    }

    public function test_default_embargo_date()
    {
        $embargoDate = $this->event->getRegistrationEmbargoDate();
        error_log('EMBARGO DATE: ' . $embargoDate);
        $this->assertEquals($this->event->StartDateTime, $embargoDate);
    }
}
