<?php

namespace TitleDK\Calendar\Tests\Events;

use \SilverStripe\Dev\SapphireTest;


use Carbon\Carbon;
use SilverStripe\ORM\FieldType\DBTime;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;

class EventTest extends SapphireTest
{
use DateTimeHelperTrait;

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

    public function testSummaryFields()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetEventPageCalendarTitle()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_event_page_title_no_calendar_page()
    {
        $this->assertEquals('-', $this->event->getEventPageCalendarTitle());
    }

    public function testDetailsSummary()
    {
        $this->assertEquals('This is detail about the test event title', $this->event->DetailsSummary());
    }

    public function testOnBeforeWrite()
    {
        $this->markTestSkipped('TODO');
    }

    public function testSetStartEnd()
    {
        $this->markTestSkipped('TODO');
    }

    public function testSetEnd()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCalcEndDateTimeBasedOnDuration()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCalcDurationBasedOnEndDateTime()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_calc_end_date_time_based_on_duration()
    {
        $this->event->Duration = '05:30:24';

        // 8am +5.5 hours 13:30
        $this->assertEquals('2018-05-16 13:30:24', $this->event->calcEndDateTimeBasedOnDuration());
    }

    public function testIsAllDay()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetFrontEndFields()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetCMSFields()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetCMSValidator()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetAddNewFields()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetIsPastEvent()
    {
        $this->markTestSkipped('TODO');
    }

    public function testRegistrationEmbargoDate()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetRegistrationEmbargoDate()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetIsPastRegistrationClosing()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetFormattedStartDate()
    {
        $this->assertEquals('May 16, 2018', $this->event->getFormattedStartDate());
    }

    public function test_get_formatted_dates_end_date_set_different_days_in_same_month()
    {
        $this->event->EndDateTime = $this->getSSDateTimeFromCarbon($this->now->addDays(2));
        error_log('EDT: ' . $this->event->EndDateTime);
        $this->assertEquals('May 16th - 18th', $this->event->getFormattedDates());
    }

    public function test_get_formatted_dates_end_date_set_different_days_in_different_month()
    {
        $this->event->EndDateTime = $this->getSSDateTimeFromCarbon($this->now->addMonths(2));
        error_log('EDT: ' . $this->event->EndDateTime);
        $this->assertEquals('May 16th - Jul 16th', $this->event->getFormattedDates());
    }

    public function test_get_formatted_dates_end_date_set_same_day()
    {
        $this->event->EndDateTime = $this->getSSDateTimeFromCarbon($this->now->addHours(2));
        error_log('EDT: ' . $this->event->EndDateTime);
        $this->assertEquals('May 16th', $this->event->getFormattedDates());
    }

    public function test_get_formatted_dates_no_end_date_set()
    {

        $this->assertEquals('May 16th', $this->event->getFormattedDates());
    }

    public function testGetFormattedTimeframe()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetStartAndEndDates()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetDatesAndTimeframe()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetInternalLink()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetRelativeLink()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanView()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanCreate()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanCreateTags()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanEdit()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanDelete()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanManage()
    {
        $this->markTestSkipped('TODO');
    }

    public function testTicketsRemaining()
    {
        $this->markTestSkipped('TODO');
    }
}
