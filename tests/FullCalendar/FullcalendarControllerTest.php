<?php

namespace TitleDK\Calendar\Tests\FullCalendar;

use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Dev\SapphireTest;
use TitleDK\Calendar\Events\Event;
use TitleDK\Calendar\FullCalendar\FullcalendarController;

class FullcalendarControllerTest extends FunctionalTest
{
    protected static $fixture_file = ['tests/events.yml'];


    // fullcalendar//$Action/$ID/$OtherID
    public function test_init()
    {

        $this->markTestSkipped('TODO');
    }

    public function testEventlistOffsetDate()
    {
        $this->markTestSkipped('TODO');
    }

    public function testOffset_date()
    {
        $this->markTestSkipped('TODO');
    }

    /**
     * Test events over a date range.  This is the call made by the JavaScript calendar code
     */
    public function test_events()
    {
        $params = ['start' => '2019-12-15', 'end' => '2019-12-30'];
        $page = $this->post('/fullcalendar/events/?calendars=1', $params);
        $this->assertEquals(200, $page->getStatusCode());
        $this->assertEquals('application/json', $page->getHeader('Content-Type'));
        $decoded = json_decode($page->getBody());
        $titles = [];
        foreach ($decoded as $event) {
            $titles[] = $event->title;
        }

        $this->assertEquals([
            'Freezing in the Park',
            'Blink And You Will Miss It',
            'Blink And You Will Miss It 2',
            'The Neverending Event',
            'SilverStripe Booze Up',
            'SilverStripe Meet Up'
        ], $titles);
    }


    public function test_events_all_day()
    {
        $params = ['start' => '2019-12-15', 'end' => '2019-12-30'];
        $page = $this->post('/fullcalendar/events/?calendars=1&allDay=true', $params);
        $this->assertEquals(200, $page->getStatusCode());
        $this->assertEquals('application/json', $page->getHeader('Content-Type'));
        $decoded = json_decode($page->getBody());
        $titles = [];
        foreach ($decoded as $event) {
            $titles[] = $event->title;
        }

        $this->assertEquals([
            'Freezing in the Park',
            'Blink And You Will Miss It',
            'Blink And You Will Miss It 2',
            'The Neverending Event',
            'SilverStripe Booze Up',
            'SilverStripe Meet Up'
        ], $titles);


        // the allDay parameter increases coverage, but does not affect the output.  eventpopup is the only method
        // that uses $this->event and it is not referenced anywhere
        $this->markAsRisky();
    }


    public function test_single_event()
    {
        $params = ['start' => '2019-12-15', 'end' => '2019-12-30'];
        $page = $this->post('/fullcalendar/events/?calendars=1&eventID=1', $params);
        $this->assertEquals(200, $page->getStatusCode());
        $this->assertEquals('application/json', $page->getHeader('Content-Type'));

    }


    public function test_offset_date_null_timestring()
    {
        $result = FullcalendarController::offset_date('start', null, 124);
        $this->assertEquals('2019-09-03', $result);

        // @tired, need to revisit
        $this->markAsRisky();
    }


        public function testShadedevents()
    {
        $this->markTestSkipped('TODO');
    }


    public function test_handle_json_response()
    {
        // params are arbitrary here
        $params = ['title' => 'Some Event Title', 'allday' => true];
        $controller = new FullcalendarController();;

        $response = $controller->handleJsonResponse(true, $params);
        $this->assertEquals('{"title":"Some Event Title","allday":true,"success":true}', $response->getBody());
    }

    public function test_format_event_for_fullcalendar()
    {
        $event = $this->objFromFixture(Event::class, 'eventCricketSeason');
        $formatted = FullcalendarController::format_event_for_fullcalendar($event);
        $this->assertEquals([
            'id' => $event->ID,
            'title' => 'Scottish Cricket Season',
            'start' => '2020-04-11T12:00:00+00:00',
            'end' => '2020-09-21T21:30:00+00:00',
            'allDay' => true,
            'className' => 'TitleDK\Calendar\Events\Event',
            'backgroundColor' => '#999',
            'textColor' => '#FFFFFF',
            'borderColor' => '#555'
        ], $formatted);
    }

    public function test_format_datetime_for_fullcalendar()
    {
        $this->assertEquals('2019-12-15T08:30:00+00:00',
            FullcalendarController::format_datetime_for_fullcalendar('2019-12-15 08:30:00'));
    }
}
