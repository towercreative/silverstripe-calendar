<?php declare(strict_types = 1);

namespace TitleDK\Calendar\Tests\Events;

use Carbon\Carbon;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\DateTime\DateTimeHelperTrait;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\PageTypes\CalendarPage;
use TitleDK\Calendar\PageTypes\EventPage;

class EventTest extends SapphireTest
{

    use DateTimeHelperTrait;

    protected static $fixture_file = 'tests/events.yml';

    /** @var \TitleDK\Calendar\Events\Event */
    private $eveningMeetUpEvent;

    /** @var \TitleDK\Calendar\Events\Event */
    private $durationEvent;

    /** @var \TitleDK\Calendar\Events\Event */
    private $cricketSeasonEvent;

    /** @var \TitleDK\Calendar\Events\Event */
    private $weekendEvent;

    /** @var \TitleDK\Calendar\Events\Event */
    private $allDayEvent;

    /** @var \TitleDK\Calendar\Events\Event */
    private $futureEvent;

    /** @var \TitleDK\Calendar\Events\Event */
    private $zeroSecondsEvent1;

    /** @var \TitleDK\Calendar\Events\Event */
    private $zeroSecondsEvent2;

    /** @var \TitleDK\Calendar\Events\Event */
    private $noEndEvent;

    /** @var \TitleDK\Calendar\Events\Event */
    private $newYearEvent;

    /** @var \TitleDK\Calendar\PageTypes\CalendarPage */
    private $calendarPage;

    public function setUp(): void
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
        $this->newYearEvent = $this->objFromFixture('TitleDK\Calendar\Events\Event', 'eventNewYear');
        $this->calendarPage = $this->objFromFixture(CalendarPage::class, 'testcalendarpage');
    }


    public function testSummaryFields(): void
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

            'RegistrationEmbargoAt' => 'Embargo Registration At',
        ], $fields);
    }


    public function testGetEventPageCalendarTitle(): void
    {
        $eventPage = $this->objFromFixture(EventPage::class, 'testEventPage');
        $this->assertEquals('Test Event Page', $eventPage->getCalendarTitle());
    }


    public function testEventPageTitleNoCalendarPage(): void
    {
        $this->assertEquals('-', $this->eveningMeetUpEvent->getEventPageCalendarTitle());
    }


    public function testDetailsSummary(): void
    {
        $this->assertEquals(
            '<a href="https://silverstripe.org">SilverStripe</a> meetup, almost sold out',
            $this->eveningMeetUpEvent->DetailsSummary(),
        );
    }


    public function testOnBeforeWrite(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testSetStartEndDurationSameDay(): void
    {
        /** @var \TitleDK\Calendar\Events\Event $event */
        $event = $this->objFromFixture(Event::class, 'eventWithDuration');
        $this->assertEquals('2019-10-12 18:00:00', $event->StartDateTime);
        $this->assertEquals('2019-10-12 22:05:24', $event->EndDateTime);

        $event->setEnd('2019-10-12 23:30:00');

        $this->assertEquals('2019-10-12 18:00:00', $event->StartDateTime);
        $this->assertEquals('2019-10-12 23:30:00', $event->EndDateTime);
        $this->assertEquals('Duration', $event->TimeFrameType);
    }


    public function testSetStartEndDurationMoreThanOneDay(): void
    {
        /** @var \TitleDK\Calendar\Events\Event $event */
        $event = $this->objFromFixture(Event::class, 'eventWithDuration');
        $this->assertEquals('2019-10-12 18:00:00', $event->StartDateTime);
        $this->assertEquals('2019-10-12 22:05:24', $event->EndDateTime);

        $event->setEnd('2019-10-15 23:30:00');

        $this->assertEquals('2019-10-12 18:00:00', $event->StartDateTime);
        $this->assertEquals('2019-10-15 23:30:00', $event->EndDateTime);

        // the event is converted to a DateTime event as it is more than 24 hours, 24 being the maximum duration
        $this->assertEquals('DateTime', $event->TimeFrameType);
    }


    public function testSetStartEnd(): void
    {
        /** @var \TitleDK\Calendar\Events\Event $event */
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $this->assertEquals('2020-04-11 12:00:00', $event->StartDateTime);
        $this->assertEquals('2020-09-21 21:30:00', $event->EndDateTime);

        $event->setStartEnd('2019-04-14', '2019-09-20', true);

        $this->assertEquals('2019-04-14', $event->StartDateTime);
        $this->assertEquals('2019-09-20', $event->EndDateTime);
    }


    public function testSetEndAfterStart(): void
    {
        /** @var \TitleDK\Calendar\Events\Event $event */
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
    public function testSetEndBeforeStart(): void
    {
        /** @var \TitleDK\Calendar\Events\Event $event */
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $this->assertEquals('2020-04-11 12:00:00', $event->StartDateTime);
        $this->assertEquals('2020-09-21 21:30:00', $event->EndDateTime);

        $event->setEnd('2019-09-20', true);

        $this->assertEquals('2020-04-11 12:00:00', $event->StartDateTime);
        $this->assertEquals('2020-04-11 12:00:00', $event->EndDateTime);
    }


    public function test_calc_duration_based_on__end_date_time_less_than_24_hours(): void
    {
        $this->assertEquals(
            '01:30',
            $this->eveningMeetUpEvent->calcDurationBasedOnEndDateTime($this->eveningMeetUpEvent->EndDateTime),
        );
    }


    public function test_calc_duration_based_on__end_date_time_more_than_24_hours(): void
    {
        $this->assertFalse($this->weekendEvent->calcDurationBasedOnEndDateTime($this->weekendEvent->EndDateTime));
    }


    public function test_calc_end_date_time_based_on_duration(): void
    {
        $this->assertEquals('2019-10-12 22:05:24', $this->durationEvent->calcEndDateTimeBasedOnDuration());
    }


    public function test_is_not_all_day(): void
    {
        // if the AllDay flag is not set and the event does not straddle a day then is not all day
        $this->assertFalse($this->eveningMeetUpEvent->isAllDay());
    }


    public function test_long_event_is_all_day(): void
    {
        // if the time deltas > 24 hours, the event is all day
        $this->assertTrue($this->weekendEvent->isAllDay());
    }


    public function test_is_all_day(): void
    {
        $this->assertTrue($this->allDayEvent->isAllDay());
    }


    public function testGetFrontEndFields(): void
    {
        // this calls front end fields method
        $fields = $this->weekendEvent->getAddNewFields();
        $names = [];
        foreach ($fields as $field) {
            $names[] = $field->Name;
        }

        $this->assertEquals(['Title', 'AllDay', 'StartDateTime', 'TimeFrameHeader', 'TimeFrameType', 'Clear',
                                'CalendarID'], $names);
    }


    public function testGetFrontEndFieldsNoEnd(): void
    {
        Config::inst()->set(Event::class, 'force_end', false);
        $fields = $this->weekendEvent->getAddNewFields();

        $names = [];
        foreach ($fields as $field) {
            $names[] = $field->Name;
        }

        $this->assertEquals(['Title', 'AllDay', 'StartDateTime', 'NoEnd', 'TimeFrameHeader', 'TimeFrameType', 'Clear',
                                'CalendarID'], $names);
    }


    public function testGetFrontEndFieldsNoAllDay(): void
    {
     //   Config::inst()->update('ClassName', 'var_name', 'new_var_value');
        Config::inst()->set(Event::class, 'enable_allday_events', false);
        $fields = $this->weekendEvent->getAddNewFields();

        $names = [];
        foreach ($fields as $field) {
            $names[] = $field->Name;
        }

        // AllDay field is removed as a result of the above config tweak
        $this->assertEquals(['Title', 'StartDateTime', 'TimeFrameHeader', 'TimeFrameType', 'Clear',
                                'CalendarID'], $names);
    }

    // @todo figure out a better test here
    public function testGetCMSFields(): void
    {
        $fields = $this->weekendEvent->getCMSFields();
        $names = [];
        foreach ($fields as $field) {
            $names[] = $field->Name;
        }
        $this->assertEquals(['Root'], $names);
    }


    public function testGetCMSValidator(): void
    {
        /** @var \SilverStripe\Forms\RequiredFields $validator */
        $validator = $this->weekendEvent->getCMSValidator();
        $this->assertEquals(['Title', 'CalendarID'], $validator->getRequired());
    }


    public function test_from_past_is_past_event(): void
    {
        $this->assertTrue($this->weekendEvent->getIsPastEvent());
    }


    public function test_from_past_is_future_event(): void
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


    /**
     * By default the embargo time is the moment the event starts
     */
    public function test_registration_embargo_date(): void
    {
        $this->assertEquals('2019-12-13 19:00:00', $this->weekendEvent->getRegistrationEmbargoDate(true));
        $this->assertEquals('2019-12-13 19:00:00', $this->weekendEvent->RegistrationEmbargoDate());
    }


    public function test_get_is_not_past_registration_closing(): void
    {
        $now = $this->carbonDateTime('2019-12-10 04:00:00');
        Carbon::setTestNow($now);
        $this->assertFalse($this->weekendEvent->getIsPastRegistrationClosing());
    }


    public function test_get_is_past_registration_closing(): void
    {
        $now = $this->carbonDateTime('2019-12-14 04:00:00');
        Carbon::setTestNow($now);
        $this->assertTrue($this->weekendEvent->getIsPastRegistrationClosing());
    }


    public function testGetFormattedStartDate(): void
    {
        $this->assertEquals('Dec 16, 2019', $this->eveningMeetUpEvent->getFormattedStartDate());
    }


    public function test_get_formatted_dates_end_date_set_different_days_in_same_month(): void
    {
        $this->assertEquals('Dec 13th - 15th', $this->weekendEvent->getFormattedDates());
    }


    public function test_get_formatted_dates_end_date_set_different_days_in_different_month(): void
    {
        $this->assertEquals('Apr 11th - Sep 21st', $this->cricketSeasonEvent->getFormattedDates());
    }


    public function test_get_formatted_dates_end_date_set_same_day(): void
    {
        $this->assertEquals('Dec 16th', $this->eveningMeetUpEvent->getFormattedDates());
    }


    public function test_get_formatted_dates_no_end_date_set(): void
    {
        $this->assertEquals('Oct 12th', $this->durationEvent->getFormattedDates());
    }


    public function test_get_formatted_dates_no_end_date_set_non_duration_event(): void
    {
        $this->assertEquals('Dec 13th', $this->noEndEvent->getFormattedDates());
    }


    public function test_get_formatted_dates_start_end_same(): void
    {
        $this->assertEquals('Dec 13th', $this->zeroSecondsEvent1->getFormattedDates());
    }


    public function test_get_formatted_time_frame_same_day(): void
    {
        $this->assertEquals('8:00pm - 9:30pm', $this->eveningMeetUpEvent->getFormattedTimeframe());
    }


    public function test_get_formatted_time_frame_same_month(): void
    {
        $this->assertNull($this->weekendEvent->getFormattedTimeframe());
    }


    public function test_get_formatted_time_same_time(): void
    {
        $this->weekendEvent->StartDateTime = $this->weekendEvent->EndDateTime;
        $this->assertNull($this->weekendEvent->getFormattedTimeframe());
    }


    public function test_get_formatted_time_frame_multi_month(): void
    {
        $this->assertNull($this->cricketSeasonEvent->getFormattedTimeframe());
    }


    public function test_get_formatted_time_frame_zero_seconds(): void
    {
        $this->assertNull($this->zeroSecondsEvent1->getFormattedTimeframe());
    }


    public function test_get_formatted_time_frame_explicit_no_end(): void
    {
        $this->noEndEvent->EndDateTime = null;
        $this->assertEquals('7:00pm', $this->noEndEvent->getFormattedTimeframe());
    }


    public function test_get_formatted_time_frame_zero_seconds_after_parsing(): void
    {
        $this->assertNull($this->zeroSecondsEvent2->getFormattedTimeframe());
    }

    // @todo Check the behaviour here, the choice of an hour seems arbitrary
    public function test_get_formatted_time_frame_no_end_date(): void
    {
        $this->assertEquals('7:00pm - 8:00pm', $this->noEndEvent->getFormattedTimeframe());
    }


    public function test_get_start_and_end_dates(): void
    {
        // this currently shows a duration of 9hrs which is incorrect
        // Apr 11, 2020 (12:00pm) &ndash; Sep 21, 2020 (9hrs)
        // @todo Fix, removed the (9hrs) in order to flag it as a bug
        $this->assertEquals(
            'Apr 11, 2020 (12:00pm) &ndash; Sep 21, 2020  (3921 hrs)',
            $this->cricketSeasonEvent->getStartAndEndDates(),
        );
    }


    public function test_get_start_and_end_dates_same_date_time(): void
    {
        $this->cricketSeasonEvent->EndDateTime = $this->cricketSeasonEvent->StartDateTime;
        $this->assertFalse(
            $this->cricketSeasonEvent->getStartAndEndDates(),
        );
    }


    /**
     * This is to test that the time is not shown in the summary if the event starts at midnight
     */
    public function test_get_start_and_end_dates_starts_at_midnight(): void
    {
        $this->weekendEvent->StartDateTime = '2019-12-13 00:00';
        $this->assertEquals(
            'Dec 13 December, 2019 &ndash; Dec 15, 2019  (69 hrs)',
            $this->weekendEvent->getStartAndEndDates(),
        );
    }


    /**
     * No duration is shown if the event ends at midnight
     *
     * @todo Is this desired functionality?
     */
    public function test_get_start_and_end_dates_ends_at_midnight(): void
    {
        $this->weekendEvent->EndDateTime = '2019-12-16 00:00';
        $this->assertEquals(
            'Dec 13, 2019 (7:00pm) &ndash; Dec 16, 2019',
            $this->weekendEvent->getStartAndEndDates(),
        );
    }


    public function test_get_start_and_end_dates_straddles_year(): void
    {
        $this->assertEquals(
            'Dec 31, 2019 (7:00pm) &ndash; Jan 1, 2020  (12 hrs)',
            $this->newYearEvent->getStartAndEndDates(),
        );
    }


    public function test_get_dates_and_time_frame_same_day(): void
    {
        $this->assertEquals('Dec 16th @ 8:00pm - 9:30pm', $this->eveningMeetUpEvent->getDatesAndTimeframe());
    }


    public function test_get_dates_and_time_frame_same_month(): void
    {
        $this->assertEquals('Dec 13th - 15th', $this->weekendEvent->getDatesAndTimeframe());
    }


    public function test_get_dates_and_time_frame_same_year(): void
    {
        $this->assertEquals('Apr 11th - Sep 21st', $this->cricketSeasonEvent->getDatesAndTimeframe());
    }


    public function test_get_internal_link(): void
    {
        $link = $this->durationEvent->getInternalLink();
        $expected = '/' . $this->calendarPage->URLSegment . '/detail/' . $this->durationEvent->ID;
        $this->assertEquals($expected, $link);
    }


    public function test_get_relative_link(): void
    {
        $id = $this->eveningMeetUpEvent->ID;
        $link = 'detail/' . $id;
        $this->assertEquals($link, $this->eveningMeetUpEvent->getRelativeLink());
    }


    public function testCanView(): void
    {
        $this->assertTrue($this->cricketSeasonEvent->canView());
    }


    public function test_can_create(): void
    {
        // @todo is this correct?
        $this->assertTrue($this->cricketSeasonEvent->canCreate());
    }


    public function testCanCreateTags(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function testCanEdit(): void
    {
        $this->assertTrue($this->cricketSeasonEvent->canEdit());
    }


    public function testCanDelete(): void
    {
        $this->assertTrue($this->cricketSeasonEvent->canDelete());
    }


    public function testCanManage(): void
    {
        $this->markTestSkipped('TODO');
    }


    public function test_tickets_remaining_no_registrations(): void
    {
        if (Director::isDev()) {
            \error_log('**** DEV MODE ****');
        }

        if (Director::isTest()) {
            \error_log('**** TEST MODE ****');
        }

        if (Director::isLive()) {
            \error_log('**** LIVE MODE ****');
        }
        $this->assertEquals(0, $this->cricketSeasonEvent->TicketsRemaining());
    }
}
