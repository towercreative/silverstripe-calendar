<?php

namespace TitleDK\Calendar\Tests\Events;

use \SilverStripe\Dev\SapphireTest;


use Carbon\Carbon;
use SilverStripe\ORM\FieldType\DBTime;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;

class EventTest extends SapphireTest
{
    protected static $fixture_file = 'tests/events.yml';

    /** @var Event */
    private $eveningMeetUpEvent;

    /** @var Event */
    private $durationEvent;

    /** @var Event */
    private $cricketSeasonEvent;

    /** @var Event */
    private $weekendEvent;

    use DateTimeHelperTrait;

    public function setUp()
    {
        parent::setUp();

        $this->eveningMeetUpEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventSameDay');
        $this->durationEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventWithDuration');
        $this->cricketSeasonEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventCricketSeason');
        $this->weekendEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventWeekend');
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
        $this->assertEquals('-', $this->eveningMeetUpEvent->getEventPageCalendarTitle());
    }

    public function test_details_summary()
    {
        $this->assertEquals('<a href="https://silverstripe.org">SilverStripe</a> meetup, almost sold out',
            $this->eveningMeetUpEvent->DetailsSummary());
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
        // 8am +5.5 hours 13:30
        $this->assertEquals('2019-10-12 22:05:24', $this->durationEvent->calcEndDateTimeBasedOnDuration());
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
        $this->assertEquals('Dec 16, 2019', $this->eveningMeetUpEvent->getFormattedStartDate());
    }

    public function test_get_formatted_dates_end_date_set_different_days_in_same_month()
    {
        $this->assertEquals('Dec 13th - 15th', $this->weekendEvent->getFormattedDates());
    }

    public function test_get_formatted_dates_end_date_set_different_days_in_different_month()
    {
        $this->assertEquals('Apr 11th - Sep 21st', $this->cricketSeasonEvent->getFormattedDates());
    }

    public function test_get_formatted_dates_end_date_set_same_day()
    {
        $this->assertEquals('Dec 16th', $this->eveningMeetUpEvent->getFormattedDates());
    }

    public function test_get_formatted_dates_no_end_date_set()
    {
        $this->assertEquals('Oct 12th', $this->durationEvent->getFormattedDates());
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
