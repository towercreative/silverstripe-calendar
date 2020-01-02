<?php

namespace TitleDK\Calendar\Tests\Events;

use Carbon\Carbon;
use SilverStripe\Control\Director;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\RequiredFields;
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

    /** @var Event */
    private $allDayEvent;

    /** @var Event */
    private $futureEvent;

    /** @var Event */
    private $zeroSecondsEvent1;

    /** @var Event */
    private $zeroSecondsEvent2;

    /** @var Event */
    private $noEndEvent;

    use DateTimeHelperTrait;

    public function setUp()
    {
        parent::setUp();

        $this->eveningMeetUpEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventSameDay');
        $this->allDayEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventAllDay');
        $this->durationEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventWithDuration');
        $this->cricketSeasonEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventCricketSeason');
        $this->weekendEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventWeekend');
        $this->futureEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventFuture');
        $this->zeroSecondsEvent1 = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventZeroSeconds1');
        $this->zeroSecondsEvent2 = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventZeroSeconds2');
        $this->noEndEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventNoEnd');
    }

    public function test_summary_fields()
    {
        $fields = $this->eveningMeetUpEvent->summaryFields();

        // @TimeFrameType and RegistrationEmbargoAt have different cases on the trailing words with Postgresql
        $this->assertEquals([
            'Title' => 'Title',
            'StartDateTime' => 'Date and Time',
            'DatesAndTimeframe' => 'Presentation String',
            'TimeFrameType' => 'Time Frame Type',
            'Duration' => 'Duration',
            'Calendar.Title' => 'Calendar',

            // this is from the event image extension
            'Thumbnail' => 'Thumbnail',

            'RegistrationEmbargoAt' => 'Embargo Registration At'
        ], $fields);
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
        $this->assertEquals(
            '<a href="https://silverstripe.org">SilverStripe</a> meetup, almost sold out',
            $this->eveningMeetUpEvent->DetailsSummary()
        );
    }

    public function testOnBeforeWrite()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_set_start_end()
    {
        /** @var Event $event */
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $this->assertEquals('2020-04-11 12:00:00', $event->StartDateTime);
        $this->assertEquals('2020-09-21 21:30:00', $event->EndDateTime);

        $event->setStartEnd('2019-04-14', '2019-09-20', true);

        $this->assertEquals('2019-04-14', $event->StartDateTime);
        $this->assertEquals('2019-09-20', $event->EndDateTime);

    }

    public function test_set_end_after_start()
    {
        /** @var Event $event */
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $this->assertEquals('2020-04-11 12:00:00', $event->StartDateTime);
        $this->assertEquals('2020-09-21 21:30:00', $event->EndDateTime);

        $event->setEnd('2020-09-20', true);

        $this->assertEquals('2020-04-11 12:00:00', $event->StartDateTime);
        $this->assertEquals('2020-09-20', $event->EndDateTime);
    }

    /**
     * If the end time is before the start time, the expected behaviour is to set the end time to the start time
     */
    public function test_set_end_before_start()
    {
        /** @var Event $event */
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $this->assertEquals('2020-04-11 12:00:00', $event->StartDateTime);
        $this->assertEquals('2020-09-21 21:30:00', $event->EndDateTime);

        $event->setEnd('2019-09-20', true);

        $this->assertEquals('2020-04-11 12:00:00', $event->StartDateTime);
        $this->assertEquals('2020-04-11 12:00:00', $event->EndDateTime);
    }

    public function test_calc_duration_based_on__end_date_time_less_than_24_hours()
    {
        $this->assertEquals(
            '01:30',
            $this->eveningMeetUpEvent->calcDurationBasedOnEndDateTime($this->eveningMeetUpEvent->EndDateTime)
        );
    }

    public function test_calc_duration_based_on__end_date_time_more_than_24_hours()
    {
        $this->assertFalse($this->weekendEvent->calcDurationBasedOnEndDateTime($this->weekendEvent->EndDateTime));
    }

    public function test_calc_end_date_time_based_on_duration()
    {
        $this->assertEquals('2019-10-12 22:05:24', $this->durationEvent->calcEndDateTimeBasedOnDuration());
    }

    public function test_is_not_all_day()
    {
        // if the AllDay flag is not set and the event does not straddle a day then is not all day
        $this->assertFalse($this->eveningMeetUpEvent->isAllDay());
    }

    public function test_long_event_is_all_day()
    {
        // if the time deltas > 24 hours, the event is all day
        $this->assertTrue($this->weekendEvent->isAllDay());
    }

    public function test_is_all_day()
    {
        $this->assertTrue($this->allDayEvent->isAllDay());
    }

    public function testGetFrontEndFields()
    {
        // this calls front end fields method
        $fields = $this->weekendEvent->getAddNewFields();
        $names = [];
        foreach ($fields as $field) {
            $names[] = $field->Name;
        }

        $this->assertEquals(['Title', 'AllDay', 'StartDateTime', 'TimeFrameHeader', 'TimeFrameType', 'Clear'], $names);
    }

    // @todo figure out a better test here
    public function testGetCMSFields()
    {
        $fields = $this->weekendEvent->getCMSFields();
        $names = [];
        foreach ($fields as $field) {
            $names[] = $field->Name;
        }
        $this->assertEquals(['Root'], $names);
    }

    public function testGetCMSValidator()
    {
        /** @var RequiredFields $validator */
        $validator = $this->weekendEvent->getCMSValidator();
        $this->assertEquals(['Title', 'CalendarID'], $validator->getRequired());
    }


    public function test_from_past_is_past_event()
    {
        $this->assertTrue($this->weekendEvent->getIsPastEvent());
    }

    public function test_from_past_is_future_event()
    {
        // need to massage dates
        $now = Carbon::now();

        // anything in the future is fine
        $start =$this->getSSDateTimeFromCarbon($now->addDays(10));
        $end =$this->getSSDateTimeFromCarbon($now->addDays(20));

        $this->weekendEvent->StartDateTime = $start;
        $this->weekendEvent->EndDateTime = $end;
        $this->assertFalse($this->futureEvent->getIsPastEvent());
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

    public function test_get_formatted_dates_no_end_date_set_non_duration_event()
    {
        $this->assertEquals('Dec 13th', $this->noEndEvent->getFormattedDates());
    }

    public function test_get_formatted_dates_start_end_same()
    {
        $this->assertEquals('Dec 13th', $this->zeroSecondsEvent1->getFormattedDates());
    }

    public function test_get_formatted_time_frame_same_day()
    {
        $this->assertEquals('8:00pm - 9:30pm', $this->eveningMeetUpEvent->getFormattedTimeframe());
    }

    public function test_get_formatted_time_frame_same_month()
    {
        $this->assertNull($this->weekendEvent->getFormattedTimeframe());
    }

    public function test_get_formatted_time_frame_multi_month()
    {
        $this->assertNull($this->cricketSeasonEvent->getFormattedTimeframe());
    }

    public function test_get_formatted_time_frame_zero_seconds()
    {
        $this->assertNull($this->zeroSecondsEvent1->getFormattedTimeframe());
    }

    public function test_get_formatted_time_frame_zero_seconds_after_parsing()
    {
        $this->assertNull($this->zeroSecondsEvent2->getFormattedTimeframe());
    }

    // @todo Check the behaviour here, the choice of an hour seems arbitrary
    public function test_get_formatted_time_frame_no_end_date()
    {
        $this->assertEquals('7:00pm - 8:00pm', $this->noEndEvent->getFormattedTimeframe());
    }

    public function test_get_start_and_end_dates()
    {
        // this currently shows a duration of 9hrs which is incorrect
        // Apr 11, 2020 (12:00pm) &ndash; Sep 21, 2020 (9hrs)
        // @todo Fix, removed the (9hrs) in order to flag it as a bug
        $this->assertEquals(
            'Apr 11, 2020 (12:00pm) &ndash; Sep 21, 2020  (3921 hrs)',
            $this->cricketSeasonEvent->getStartAndEndDates()
        );
    }

    public function test_get_dates_and_time_frame_same_day()
    {
        $this->assertEquals('Dec 16th @ 8:00pm - 9:30pm', $this->eveningMeetUpEvent->getDatesAndTimeframe());
    }

    public function test_get_dates_and_time_frame_same_month()
    {
        $this->assertEquals('Dec 13th - 15th', $this->weekendEvent->getDatesAndTimeframe());
    }

    public function test_get_dates_and_time_frame_same_year()
    {
        $this->assertEquals('Apr 11th - Sep 21st', $this->cricketSeasonEvent->getDatesAndTimeframe());
    }

    public function testGetInternalLink()
    {
        $this->markTestSkipped('TODO');
    }

    public function testGetRelativeLink()
    {
        $id = $this->eveningMeetUpEvent->ID;
        $link = 'detail/' . $id;
        $this->assertEquals($link, $this->eveningMeetUpEvent->getRelativeLink());
    }

    public function testCanView()
    {
        $this->assertTrue($this->cricketSeasonEvent->canView());
    }

    public function test_can_create()
    {
        // @todo is this correct?
        $this->assertTrue($this->cricketSeasonEvent->canCreate());
    }

    public function testCanCreateTags()
    {
        $this->markTestSkipped('TODO');
    }

    public function testCanEdit()
    {
        $this->assertTrue($this->cricketSeasonEvent->canEdit());
    }

    public function testCanDelete()
    {
        $this->assertTrue($this->cricketSeasonEvent->canDelete());
    }

    public function testCanManage()
    {
        $this->markTestSkipped('TODO');
    }

    public function test_tickets_remaining_no_registrations()
    {
        if (Director::isDev()) {
            error_log('**** DEV MODE ****');
        }

        if (Director::isTest()) {
            error_log('**** TEST MODE ****');
        }

        if (Director::isLive()) {
            error_log('**** LIVE MODE ****');
        }
        error_log('TICKETS: ' .  $this->cricketSeasonEvent->TicketsRemaining());
        $this->assertEquals(0, $this->cricketSeasonEvent->TicketsRemaining());
    }
}
